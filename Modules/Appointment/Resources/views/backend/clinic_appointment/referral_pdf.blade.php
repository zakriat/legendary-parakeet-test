<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        Referral - Appointment #{{ $appointment->id }}
    </title>

    <style>
        @page {
            margin: 32px;
        }

        body {
            color: #000;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.5;
        }

        h1 {
            margin: 0 0 5px;
            font-size: 22px;
        }

        h2 {
            margin: 0 0 9px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            font-size: 14px;
        }

        p {
            margin: 4px 0;
        }

        .header {
            border-bottom: 2px solid #000;
            margin-bottom: 18px;
            padding-bottom: 12px;
        }

        .section {
            border: 1px solid #555;
            margin-bottom: 13px;
            padding: 12px;
        }

        .label {
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            border: 1px solid #000;
            padding: 2px 7px;
            text-transform: capitalize;
        }

        .two-column {
            width: 100%;
            border-collapse: collapse;
        }

        .two-column td {
            width: 50%;
            padding: 3px 10px 3px 0;
            vertical-align: top;
        }

        table.records {
            width: 100%;
            border-collapse: collapse;
        }

        table.records th,
        table.records td {
            border: 1px solid #555;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }

        .footer {
            border-top: 1px solid #000;
            margin-top: 20px;
            padding-top: 8px;
            font-size: 9px;
        }
    </style>
</head>

<body>
    @php
        $patient = $appointment->user;
        $doctor = $referral->referringDoctor
            ?? $appointment->doctor;

        $patientName = trim(
            ($patient->first_name ?? '') . ' ' .
            ($patient->last_name ?? '')
        );

        $referringDoctorName = trim(
            ($doctor->first_name ?? '') . ' ' .
            ($doctor->last_name ?? '')
        );
    @endphp

    <div class="header">
        <h1>Medical Referral</h1>

        <p>
            Appointment:
            <strong>#{{ $appointment->id }}</strong>
        </p>

        <p>
            Referral date:
            <strong>
                {{ optional($referral->referred_at)->format('d/m/Y H:i') }}
            </strong>
        </p>

        <p>
            Urgency:
            <span class="badge">
                {{ $referral->urgency }}
            </span>
        </p>
    </div>

    <div class="section">
        <h2>Receiving doctor</h2>

        <table class="two-column">
            <tr>
                <td>
                    <span class="label">Referral type:</span>
                    {{ ucfirst($referral->referral_type) }}
                </td>

                <td>
                    <span class="label">CRM doctor ID:</span>
                    {{ $referral->receiving_doctor_id ?: 'External doctor' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Doctor:</span>
                    {{ $referral->receiving_doctor_name }}
                </td>

                <td>
                    <span class="label">Speciality:</span>
                    {{ $referral->receiving_doctor_speciality }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Organisation:</span>
                    {{ $referral->receiving_organisation_name ?: '—' }}
                </td>

                <td>
                    <span class="label">Telephone:</span>
                    {{ $referral->receiving_doctor_phone ?: '—' }}
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <span class="label">Email:</span>
                    {{ $referral->receiving_doctor_email ?: '—' }}
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <span class="label">Address:</span>
                    {{ $referral->receiving_doctor_address ?: '—' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Patient</h2>

        <table class="two-column">
            <tr>
                <td>
                    <span class="label">Name:</span>
                    {{ $patientName ?: '—' }}
                </td>

                <td>
                    <span class="label">Date of birth:</span>
                    {{ $patient->date_of_birth
                        ? \Carbon\Carbon::parse(
                            $patient->date_of_birth
                        )->format('d/m/Y')
                        : '—'
                    }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">Email:</span>
                    {{ $patient->email ?? '—' }}
                </td>

                <td>
                    <span class="label">Telephone:</span>
                    {{ $patient->mobile ?? '—' }}
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <span class="label">Address:</span>
                    {{ $patient->address ?? '—' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>Referral information</h2>

        <p class="label">Reason for referral</p>
        <p>{!! nl2br(e($referral->referral_reason)) !!}</p>

        <p class="label">Clinical summary</p>
        <p>{!! nl2br(e($referral->clinical_summary)) !!}</p>

        <p class="label">Diagnosis</p>
        <p>
            {!! nl2br(e($referral->diagnosis ?: 'Not recorded')) !!}
        </p>

        <p class="label">Requested action</p>
        <p>
            {!! nl2br(e($referral->requested_action ?: 'Not recorded')) !!}
        </p>
    </div>

    <div class="section">
        <h2>Important clinical information</h2>

        <p class="label">Allergies</p>

        @forelse($appointment->patientAllergies as $allergy)
            <p>
                {{ $allergy->allergen }}
                @if($allergy->reaction)
                    — reaction: {{ $allergy->reaction }}
                @endif
                @if($allergy->severity)
                    ({{ ucfirst($allergy->severity) }})
                @endif
            </p>
        @empty
            <p>No allergies recorded.</p>
        @endforelse

        <p class="label">Medical conditions</p>

        @forelse($appointment->patientConditions as $condition)
            <p>
                {{ $condition->condition_name }}
                @if($condition->status)
                    — {{ ucfirst($condition->status) }}
                @endif
            </p>
        @empty
            <p>No medical conditions recorded.</p>
        @endforelse

        <p class="label">Current medications</p>

        @forelse($appointment->patientMedications as $medication)
            <p>
                {{ $medication->medication_name }}
                {{ $medication->dose }}
                {{ $medication->frequency }}
            </p>
        @empty
            <p>No medications recorded.</p>
        @endforelse
    </div>

    <div class="section">
        <h2>Referring doctor</h2>

        <table class="two-column">
            <tr>
                <td>
                    <span class="label">Doctor:</span>
                    {{ $referringDoctorName ?: '—' }}
                </td>

                <td>
                    <span class="label">CRM doctor ID:</span>
                    {{ $referral->referring_doctor_id ?: '—' }}
                </td>
            </tr>

            <tr>
                <td>
                    <span class="label">GMC number:</span>
                    {{ $doctor->gmc_number ?? '—' }}
                </td>

                <td>
                    <span class="label">Clinic:</span>
                    {{ $appointment->cliniccenter->name ?? '—' }}
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <span class="label">Service:</span>
                    {{ $appointment->clinicservice->name ?? '—' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        This document contains confidential medical information
        and must be handled securely. Generated from appointment
        #{{ $appointment->id }}.
    </div>
</body>
</html>