<?php

namespace Modules\Appointment\Services;

use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Log;
use Modules\Appointment\Models\Appointment;

class GoogleMeetService
{
    public function createForAppointment(
        Appointment $appointment
    ): ?array {
        /*
         * Prevent duplicate Calendar events if this method
         * is accidentally called more than once.
         */
        if (
            filled($appointment->google_event_id) &&
            filled($appointment->meet_link)
        ) {
            return [
                'event_id' =>
                    $appointment->google_event_id,

                'meet_link' =>
                    $appointment->meet_link,
            ];
        }

        $appointment->loadMissing([
            'user',
            'doctor',
            'clinicservice',
            'cliniccenter',
        ]);

        $doctor = User::query()->find(
            $appointment->doctor_id
        );

        $patient = User::query()->find(
            $appointment->user_id
        );

        if (!$doctor || !$patient) {
            Log::warning(
                'Google Meet was not created because the doctor or patient was missing.',
                [
                    'appointment_id' =>
                        $appointment->id,

                    'doctor_id' =>
                        $appointment->doctor_id,

                    'patient_id' =>
                        $appointment->user_id,
                ]
            );

            return null;
        }

        $storedToken = json_decode(
            $doctor->google_access_token ?? '',
            true
        );

        if (
            !is_array($storedToken) ||
            empty($storedToken['access_token'])
        ) {
            Log::warning(
                'Google Meet was not created because the doctor has not connected Google Calendar.',
                [
                    'appointment_id' =>
                        $appointment->id,

                    'doctor_id' =>
                        $doctor->id,
                ]
            );

            return null;
        }

        $settings = Setting::query()
            ->whereIn('name', [
                'google_clientid',
                'google_secret_key',
                'google_appname',
                'google_event',
                'content',
            ])
            ->pluck('val', 'name');

        $clientId = $settings->get(
            'google_clientid'
        );

        $clientSecret = $settings->get(
            'google_secret_key'
        );

        if (blank($clientId) || blank($clientSecret)) {
            Log::warning(
                'Google Meet was not created because Google OAuth credentials are missing.',
                [
                    'appointment_id' =>
                        $appointment->id,
                ]
            );

            return null;
        }

        $client = new GoogleClient();

        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);

        $client->setApplicationName(
            $settings->get(
                'google_appname',
                config('app.name')
            )
        );

        /*
         * Keep this consistent with your existing profile
         * Google authorization implementation.
         */
        $client->setRedirectUri('postmessage');
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $client->setScopes([
            Calendar::CALENDAR_EVENTS,
        ]);

        $client->setAccessToken($storedToken);

        if ($client->isAccessTokenExpired()) {
            $refreshToken =
                $storedToken['refresh_token'] ?? null;

            if (blank($refreshToken)) {
                Log::warning(
                    'Google token expired and no refresh token is available.',
                    [
                        'appointment_id' =>
                            $appointment->id,

                        'doctor_id' =>
                            $doctor->id,
                    ]
                );

                return null;
            }

            $newToken = $client->fetchAccessTokenWithRefreshToken(
                $refreshToken
            );

            if (isset($newToken['error'])) {
                Log::error(
                    'Google token refresh failed.',
                    [
                        'appointment_id' =>
                            $appointment->id,

                        'doctor_id' =>
                            $doctor->id,

                        'google_error' =>
                            $newToken['error'],

                        'google_error_description' =>
                            $newToken[
                                'error_description'
                            ] ?? null,
                    ]
                );

                return null;
            }

            /*
             * Google may omit refresh_token from a refreshed
             * token response, so preserve the original one.
             */
            $newToken['refresh_token'] =
                $newToken['refresh_token']
                ?? $refreshToken;

            $doctor->forceFill([
                'google_access_token' =>
                    json_encode($newToken),
            ])->save();

            $client->setAccessToken($newToken);
        }

        $calendar = new Calendar($client);

        $timeZone = config(
            'app.timezone',
            'UTC'
        );

        $start = Carbon::parse(
            $appointment->appointment_date . ' ' .
            $appointment->appointment_time,
            $timeZone
        );

        $duration = max(
            1,
            (int) ($appointment->duration ?? 30)
        );

        $end = $start->copy()->addMinutes(
            $duration
        );

        $serviceName =
            optional($appointment->clinicservice)->name
            ?? 'Consultation';

        $clinicName =
            optional($appointment->cliniccenter)->name
            ?? '';

        $patientName = trim(
            ($patient->first_name ?? '') . ' ' .
            ($patient->last_name ?? '')
        );

        $doctorName = trim(
            ($doctor->first_name ?? '') . ' ' .
            ($doctor->last_name ?? '')
        );

        $presentingComplaint =
            $appointment->presenting_complaint
            ?? $appointment->appointment_extra_info
            ?? '';

        $eventTitleTemplate =
            $settings->get(
                'google_event',
                '{{service_name}}'
            );

        $descriptionTemplate =
            $settings->get(
                'content',
                'New appointment'
            );

        $replacements = [
            '{{service_name}}' =>
                $serviceName,

            '{{appointment_date}}' =>
                $start->format('d/m/Y'),

            '{{appointment_time}}' =>
                $start->format('h:i A'),

            '{{patient_name}}' =>
                $patientName,

            '{{doctor_name}}' =>
                $doctorName,

            '{{clinic_name}}' =>
                $clinicName,

            '{{appointment_desc}}' =>
                $presentingComplaint,
        ];

        $eventTitle = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $eventTitleTemplate
        );

        $eventDescription = str_replace(
            array_keys($replacements),
            array_values($replacements),
            $descriptionTemplate
        );

        $event = new Event([
            'summary' => $eventTitle,

            'description' =>
                strip_tags($eventDescription),

            'start' => [
                'dateTime' =>
                    $start->toRfc3339String(),

                'timeZone' =>
                    $timeZone,
            ],

            'end' => [
                'dateTime' =>
                    $end->toRfc3339String(),

                'timeZone' =>
                    $timeZone,
            ],

            /*
             * The doctor owns the primary calendar event.
             * The patient is invited as an attendee.
             */
            'attendees' => [
                [
                    'email' =>
                        $patient->email,

                    'displayName' =>
                        $patientName,
                ],
            ],

            /*
             * Google Calendar will send an email reminder
             * one hour before the appointment.
             */
            'reminders' => [
                'useDefault' => false,

                'overrides' => [
                    [
                        'method' => 'email',
                        'minutes' => 60,
                    ],
                    [
                        'method' => 'popup',
                        'minutes' => 10,
                    ],
                ],
            ],

            'conferenceData' => [
                'createRequest' => [
                    'requestId' =>
                        'appointment-' .
                        $appointment->id . '-' .
                        bin2hex(random_bytes(8)),

                    'conferenceSolutionKey' => [
                        'type' =>
                            'hangoutsMeet',
                    ],
                ],
            ],
        ]);

        try {
            $createdEvent = $calendar
                ->events
                ->insert(
                    'primary',
                    $event,
                    [
                        'conferenceDataVersion' => 1,

                        /*
                         * Google sends the patient the
                         * Calendar invitation.
                         */
                        'sendUpdates' => 'all',
                    ]
                );

            $meetLink = $this->extractMeetLink(
                $createdEvent
            );

            /*
             * Conference generation can briefly be pending.
             * Retrieve the event again a few times.
             */
            if (blank($meetLink)) {
                for ($attempt = 0; $attempt < 3; $attempt++) {
                    usleep(500000);

                    $createdEvent = $calendar
                        ->events
                        ->get(
                            'primary',
                            $createdEvent->getId()
                        );

                    $meetLink = $this->extractMeetLink(
                        $createdEvent
                    );

                    if (filled($meetLink)) {
                        break;
                    }
                }
            }

            $appointment->forceFill([
                'google_event_id' =>
                    $createdEvent->getId(),

                'meet_link' =>
                    $meetLink,
            ])->save();

            Log::info(
                'Google Meet Calendar event created.',
                [
                    'appointment_id' =>
                        $appointment->id,

                    'google_event_id' =>
                        $createdEvent->getId(),

                    'meet_link_created' =>
                        filled($meetLink),
                ]
            );

            return [
                'event_id' =>
                    $createdEvent->getId(),

                'meet_link' =>
                    $meetLink,

                'event_link' =>
                    $createdEvent->getHtmlLink(),
            ];
        } catch (\Throwable $exception) {
            Log::error(
                'Google Meet creation failed.',
                [
                    'appointment_id' =>
                        $appointment->id,

                    'doctor_id' =>
                        $doctor->id,

                    'error' =>
                        $exception->getMessage(),
                ]
            );

            /*
             * Do not break appointment booking merely
             * because the external calendar failed.
             */
            return null;
        }
    }

    private function extractMeetLink(
        Event $event
    ): ?string {
        if (filled($event->getHangoutLink())) {
            return $event->getHangoutLink();
        }

        $conferenceData =
            $event->getConferenceData();

        if (!$conferenceData) {
            return null;
        }

        foreach (
            $conferenceData->getEntryPoints() ?? []
            as $entryPoint
        ) {
            if (
                $entryPoint->getEntryPointType()
                === 'video'
            ) {
                return $entryPoint->getUri();
            }
        }

        return null;
    }
}