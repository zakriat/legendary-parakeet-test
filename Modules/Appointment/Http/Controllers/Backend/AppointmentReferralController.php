<?php

namespace Modules\Appointment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Models\AppointmentReferral;
use PDF;

use Modules\Appointment\Models\ReferralSpecialty;

class AppointmentReferralController extends Controller
{
    public function show(
        Appointment $appointment
    ): JsonResponse {
        $appointment->load([
            'referral.referringDoctor',
            'referral.receivingDoctor',
        ]);

        return response()->json([
            'success' => true,
            'referral' => $appointment->referral,
        ]);
    }

    // public function doctors(): JsonResponse
    // {

    //     $specialties = ReferralSpecialty::query()
    //     ->where('status', true)
    //     ->orderBy('category')
    //     ->orderBy('sort_order')
    //     ->orderBy('name')
    //     ->get([
    //         'id',
    //         'category',
    //         'name',
    //     ])
    //     ->groupBy('category')
    //     ->map(function ($items, $category) {
    //         return [
    //             'category' => $category,
    //             'items' => $items->map(
    //                 function ($specialty) {
    //                     return [
    //                         'id' => $specialty->id,
    //                         'name' => $specialty->name,
    //                     ];
    //                 }
    //             )->values(),
    //         ];
    //     })
    //     ->values();

    //     $doctors = User::query()
    //         ->where('user_type', 'doctor')
    //         ->where('status', 1)
    //         ->orderBy('first_name')
    //         ->orderBy('last_name')
    //         ->get([
    //             'id',
    //             'first_name',
    //             'last_name',
    //             'email',
    //             'mobile',
    //             'gmc_number',
    //         ])
    //         ->map(function (User $doctor) {
    //             return [
    //                 'id' => $doctor->id,
    //                 'name' => trim(
    //                     $doctor->first_name . ' ' .
    //                     $doctor->last_name
    //                 ),
    //                 'email' => $doctor->email,
    //                 'phone' => $doctor->mobile,
    //                 'gmc_number' => $doctor->gmc_number,
    //             ];
    //         });

    //     return response()->json([
    //         'success' => true,
    //         'doctors' => $doctors,
    //         'specialties' => $specialties,

    //     ]);
    // }
    public function doctors(): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Internal CRM doctors
        |--------------------------------------------------------------------------
        */

        $doctors = User::query()
            ->where('user_type', 'doctor')
            ->where('status', 1)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get([
                'id',
                'first_name',
                'last_name',
                'email',
                'mobile',
                'gmc_number',
            ])
            ->map(function (User $doctor) {
                $name = trim(
                    ($doctor->first_name ?? '') .
                    ' ' .
                    ($doctor->last_name ?? '')
                );

                return [
                    'id' => $doctor->id,
                    'name' => $name !== ''
                        ? $name
                        : $doctor->email,

                    'email' =>
                        $doctor->email,

                    'phone' =>
                        $doctor->mobile,

                    'gmc_number' =>
                        $doctor->gmc_number,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Referral specialties
        |--------------------------------------------------------------------------
        */

        $specialtyRecords =
            ReferralSpecialty::query()
                ->where('status', 1)
                ->orderBy('category')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get([
                    'id',
                    'category',
                    'name',
                ]);

        $specialties = $specialtyRecords
            ->groupBy('category')
            ->map(
                function (
                    $items,
                    $category
                ) {
                    return [
                        'category' => $category,

                        'items' => $items
                            ->map(
                                function (
                                    ReferralSpecialty
                                    $specialty
                                ) {
                                    return [
                                        'id' =>
                                            $specialty->id,

                                        'name' =>
                                            $specialty->name,
                                    ];
                                }
                            )
                            ->values()
                            ->all(),
                    ];
                }
            )
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'doctors' => $doctors,
            'specialties' => $specialties,
        ]);
    }

    public function store(
        Request $request,
        Appointment $appointment
    ): JsonResponse {
        $validated = $request->validate([
            'referral_type' => [
                'required',
                Rule::in(['internal', 'external']),
            ],

            'receiving_doctor_id' => [
                'nullable',
                'required_if:referral_type,internal',
                'integer',
                Rule::exists('users', 'id')
                    ->where(function ($query) {
                        $query->where(
                            'user_type',
                            'doctor'
                        );
                    }),
            ],

                'referral_specialty_id' => [
                    'required',
                    'integer',
                    Rule::exists(
                        'referral_specialties',
                        'id'
                    )->where(function ($query) {
                        $query->where(
                            'status',
                            true
                        );
                    }),
                ],

            'receiving_doctor_name' => [
                'nullable',
                'required_if:referral_type,external',
                'string',
                'max:255',
            ],

            // 'receiving_doctor_speciality' => [
            //     'required',
            //     'string',
            //     'max:255',
            // ],

            'receiving_organisation_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'receiving_doctor_email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'receiving_doctor_phone' => [
                'nullable',
                'string',
                'max:40',
            ],

            'receiving_doctor_address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'referral_reason' => [
                'required',
                'string',
                'max:5000',
            ],

            'clinical_summary' => [
                'required',
                'string',
                'max:15000',
            ],

            'diagnosis' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'requested_action' => [
                'nullable',
                'string',
                'max:255',
            ],

            'urgency' => [
                'required',
                Rule::in([
                    'routine',
                    'urgent',
                    'emergency',
                ]),
            ],
        ]);

        $loggedInUserId = auth()->id();

        /*
         * The appointment's doctor is the referring doctor.
         * This remains correct even when an administrator
         * enters the referral on the doctor's behalf.
         */
        $referringDoctorId =
            $appointment->doctor_id ?: null;

        $receivingDoctor = null;

        if (
            $validated['referral_type'] === 'internal'
        ) {
            $receivingDoctor = User::query()
                ->whereKey(
                    $validated['receiving_doctor_id']
                )
                ->where('user_type', 'doctor')
                ->where('status', 1)
                ->firstOrFail();

            /*
             * Prevent referring an appointment back to the
             * same doctor.
             */
            if (
                (int) $receivingDoctor->id ===
                (int) $referringDoctorId
            ) {
                return response()->json([
                    'success' => false,
                    'message' =>
                        'Please select a different receiving doctor.',
                    'errors' => [
                        'receiving_doctor_id' => [
                            'A doctor cannot refer the appointment to themselves.',
                        ],
                    ],
                ], 422);
            }

            /*
             * Save a snapshot of the internal doctor's details.
             */
            $validated['receiving_doctor_name'] = trim(
                $receivingDoctor->first_name . ' ' .
                $receivingDoctor->last_name
            );

            $validated['receiving_doctor_email'] =
                $receivingDoctor->email;

            $validated['receiving_doctor_phone'] =
                $receivingDoctor->mobile;
        } else {
            /*
             * An external referral must not retain an old
             * internal CRM doctor ID.
             */
            $validated['receiving_doctor_id'] = null;
        }

        $specialty = ReferralSpecialty::query()
            ->whereKey(
                $validated[
                    'referral_specialty_id'
                ]
            )
            ->where('status', true)
            ->firstOrFail();

        /*
        * Store a permanent text snapshot for PDFs
        * and historical referral records.
        */
        $validated[
            'receiving_doctor_speciality'
        ] = $specialty->name;

        $referral = DB::transaction(
            function () use (
                $validated,
                $appointment,
                $referringDoctorId,
                $loggedInUserId
            ) {
                $existingReferral =
                    $appointment->referral()
                        ->withTrashed()
                        ->first();

                $referralData = array_merge(
                    $validated,
                    [
                        'referring_doctor_id' =>
                            $referringDoctorId,

                        'referred_at' => now(),

                        'updated_by' =>
                            $loggedInUserId,
                    ]
                );

                if ($existingReferral) {
                    if ($existingReferral->trashed()) {
                        $existingReferral->restore();
                    }

                    $existingReferral->update(
                        $referralData
                    );

                    $referral = $existingReferral;
                } else {
                    $referralData['created_by'] =
                        $loggedInUserId;

                    $referral =
                        $appointment->referral()->create(
                            $referralData
                        );
                }

                /*
                 * Do not create another appointment.
                 * Only update the current appointment.
                 */
                $appointment->update([
                    'status' => 'referred',
                    'updated_by' => $loggedInUserId,
                ]);

                return $referral;
            }
        );

        $referral->load([
            'referringDoctor',
            'receivingDoctor',
        ]);

        return response()->json([
            'success' => true,
            'message' =>
                'Referral saved successfully.',
            'referral' => $referral,
            'pdf_url' => route(
                'backend.appointments.referral.pdf',
                $appointment->id
            ),
        ]);
    }

    public function downloadPdf(
        Appointment $appointment
    ) {
        $appointment->load([
            'user',
            'doctor',
            'cliniccenter',
            'clinicservice',
            'referral.referringDoctor',
            'referral.receivingDoctor',
            'patientConditions',
            'patientMedications',
            'patientAllergies',
        ]);

        abort_if(
            !$appointment->referral,
            404,
            'No referral has been recorded.'
        );

        $pdf = PDF::loadView(
            'appointment::backend.clinic_appointment.referral_pdf',
            [
                'appointment' => $appointment,
                'referral' => $appointment->referral,
            ]
        )->setPaper('a4');

        return $pdf->download(
            'appointment-' .
            $appointment->id .
            '-referral.pdf'
        );
    }
}