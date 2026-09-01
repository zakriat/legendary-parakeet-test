<?php

namespace Modules\Clinic\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DoctorGmcVerificationController extends Controller
{
    private function findDoctorUser(int $userId): User
    {
        return User::query()
            ->where('id', $userId)
            ->where('user_type', 'doctor')
            ->firstOrFail();
    }

    public function show(int $userId)
    {
        $doctor = $this->findDoctorUser($userId);

        $doctor->load(
            'gmcVerification.checkedBy'
        );

        $gmcNumber = trim(
            (string) $doctor->gmc_number
        );

        $validNumber =
            preg_match('/^\d{7}$/', $gmcNumber) === 1;

        return response()->json([
            'status' => true,
            'data' => [
                'doctor_user_id' => $doctor->id,

                'doctor_name' => trim(
                    "{$doctor->first_name} {$doctor->last_name}"
                ),

                'gmc_number' => $gmcNumber,

                'gmc_number_valid' => $validNumber,

                'official_register_url' => $validNumber
                    ? "https://www.gmc-uk.org/registrants/{$gmcNumber}"
                    : null,

                'verification' =>
                    $doctor->gmcVerification,
            ],
        ]);
    }

    public function begin(int $userId)
    {
        $doctor = $this->findDoctorUser($userId);

        $gmcNumber = trim(
            (string) $doctor->gmc_number
        );

        if (!preg_match('/^\d{7}$/', $gmcNumber)) {
            return response()->json([
                'status' => false,
                'message' =>
                    'The doctor must have a valid seven-digit GMC number.',
            ], 422);
        }

        $officialUrl =
            "https://www.gmc-uk.org/registrants/{$gmcNumber}";

        $verification = $doctor
            ->gmcVerification()
            ->updateOrCreate(
                [
                    'doctor_user_id' => $doctor->id,
                ],
                [
                    'verified_gmc_number' => $gmcNumber,

                    'official_register_url' =>
                        $officialUrl,

                    'verification_status' => 'pending',

                    'verification_method' =>
                        'manual_official_register',

                    'registered_name' => null,
                    'registration_status' => null,
                    'has_licence_to_practise' => null,
                    'checked_at' => null,
                    'expires_at' => null,
                    'checked_by' => null,
                ]
            );

        return response()->json([
            'status' => true,

            'message' =>
                'Open the official GMC record and confirm the current status.',

            'data' => [
                'verification' => $verification,

                'official_register_url' =>
                    $officialUrl,
            ],
        ]);
    }

    public function confirm(
        Request $request,
        int $userId
    ) {
        $doctor = $this->findDoctorUser($userId);

        $validated = $request->validate([
            'registered_name' => [
                'required',
                'string',
                'max:255',
            ],

            'registration_status' => [
                'required',
                'string',
                'max:255',
            ],

            'has_licence_to_practise' => [
                'required',
                'boolean',
            ],

            'name_matches' => [
                'required',
                'boolean',
            ],

            'official_record_checked' => [
                'accepted',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $verification = $doctor
            ->gmcVerification()
            ->firstOrFail();

        if (
            $verification->verified_gmc_number !==
            $doctor->gmc_number
        ) {
            return response()->json([
                'status' => false,

                'message' =>
                    'The GMC number changed. Start a new verification.',
            ], 422);
        }

        $hasLicence = (bool)
            $validated['has_licence_to_practise'];

        $nameMatches = (bool)
            $validated['name_matches'];

        $verificationStatus = match (true) {
            !$nameMatches => 'mismatch',
            !$hasLicence => 'not_licensed',
            default => 'verified',
        };

        $verification->update([
            'registered_name' =>
                $validated['registered_name'],

            'registration_status' =>
                $validated['registration_status'],

            'has_licence_to_practise' =>
                $hasLicence,

            'verification_status' =>
                $verificationStatus,

            'checked_at' => now(),

            // Require another check in 30 days.
            'expires_at' => now()->addDays(30),

            'checked_by' => auth()->id(),

            'notes' =>
                $validated['notes'] ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'GMC verification saved.',

            'data' => $verification->fresh(
                'checkedBy'
            ),
        ]);
    }

    public function uploadCertificate(
        Request $request,
        int $userId
    ) {
        $doctor = $this->findDoctorUser($userId);

        $validated = $request->validate([
            'certificate' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],
        ]);

        $verification = $doctor
            ->gmcVerification()
            ->firstOrFail();

        $file = $validated['certificate'];

        $checksum = hash_file(
            'sha256',
            $file->getRealPath()
        );

        if ($verification->certificate_path) {
            Storage::disk('local')->delete(
                $verification->certificate_path
            );
        }

        $path = $file->storeAs(
            "private/gmc/{$doctor->id}",

            now()->format('YmdHis') .
                '-' .
                bin2hex(random_bytes(8)) .
                '.' .
                $file->getClientOriginalExtension(),

            'local'
        );

        $verification->update([
            'certificate_path' => $path,

            'certificate_original_name' =>
                $file->getClientOriginalName(),

            'certificate_mime_type' =>
                $file->getMimeType(),

            'certificate_checksum' =>
                $checksum,

            'certificate_uploaded_at' =>
                now(),
        ]);

        return response()->json([
            'status' => true,

            'message' =>
                'Supporting GMC document uploaded securely.',
        ]);
    }

    public function downloadCertificate(
        int $userId
    ): StreamedResponse {
        $doctor = $this->findDoctorUser($userId);

        $verification = $doctor
            ->gmcVerification()
            ->firstOrFail();

        abort_unless(
            $verification->certificate_path &&
            Storage::disk('local')->exists(
                $verification->certificate_path
            ),
            404
        );

        return Storage::disk('local')->download(
            $verification->certificate_path,

            $verification->certificate_original_name
                ?? 'gmc-supporting-document'
        );
    }
}