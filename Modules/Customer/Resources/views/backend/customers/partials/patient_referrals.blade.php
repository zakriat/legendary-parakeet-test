@php
    $patientReferrals =
        $patientReferrals ?? collect();
@endphp

<section
    class="patient-referrals"
    aria-labelledby="patient-referrals-heading"
>
    <header class="patient-referrals__header">
        <div>
            <h3
                class="patient-referrals__title"
                id="patient-referrals-heading"
            >
                Patient referrals
            </h3>

            <p class="patient-referrals__description">
                Referral records created from this
                patient’s appointments.
            </p>
        </div>

        <div
            class="patient-referrals__count"
            aria-label="{{ $patientReferrals->count() }} referral records"
        >
            {{ $patientReferrals->count() }}
            {{ Str::plural(
                'referral',
                $patientReferrals->count()
            ) }}
        </div>
    </header>

    @if($patientReferrals->isEmpty())
        <div
            class="patient-referrals__empty"
            role="status"
        >
            <i
                class="ph ph-file-text"
                aria-hidden="true"
            ></i>

            <div>
                <h4>No referrals recorded</h4>

                <p>
                    This patient has not been referred
                    from an appointment.
                </p>
            </div>
        </div>
    @else
        <div class="patient-referrals__list">
            @foreach($patientReferrals as $referral)
                @php
                    $appointment =
                        $referral->appointment;

                    $referringDoctor =
                        $referral->referringDoctor
                        ?? optional($appointment)->doctor;

                    $referringDoctorName = trim(
                        (
                            $referringDoctor->first_name
                            ?? ''
                        ) . ' ' . (
                            $referringDoctor->last_name
                            ?? ''
                        )
                    );

                    $receivingDoctorName =
                        $referral
                            ->receiving_doctor_name
                        ?: trim(
                            (
                                optional(
                                    $referral
                                        ->receivingDoctor
                                )->first_name
                                ?? ''
                            ) . ' ' . (
                                optional(
                                    $referral
                                        ->receivingDoctor
                                )->last_name
                                ?? ''
                            )
                        );

                    $urgencyLabel = match (
                        $referral->urgency
                    ) {
                        'emergency' => 'Emergency',
                        'urgent' => 'Urgent',
                        default => 'Routine',
                    };
                @endphp

                <article
                    class="patient-referral-card"
                    aria-labelledby="referral-title-{{ $referral->id }}"
                >
                    <header class="patient-referral-card__header">
                        <div>
                            <p class="patient-referral-card__reference">
                                Referral
                                #{{ $referral->id }}

                                @if($appointment)
                                    · Appointment
                                    #{{ $appointment->id }}
                                @endif
                            </p>

                            <h4
                                class="patient-referral-card__title"
                                id="referral-title-{{ $referral->id }}"
                            >
                                {{ $receivingDoctorName ?: 'Receiving doctor not recorded' }}
                            </h4>

                            <p class="patient-referral-card__speciality">
                                {{ $referral->receiving_doctor_speciality }}
                            </p>
                        </div>

                        <div class="patient-referral-card__status">
                            <!-- <span
                                class="referral-status referral-status--{{ $referral->urgency }}"
                            >
                                <span class="visually-hidden">
                                    Urgency:
                                </span>

                                {{ $urgencyLabel }}
                            </span> -->

                            <span
    class="referral-status referral-status--{{ $referral->urgency }}"
>
    @if($referral->urgency === 'emergency')
        <i
            class="ph ph-warning-octagon me-1"
            aria-hidden="true"
        ></i>
    @elseif($referral->urgency === 'urgent')
        <i
            class="ph ph-warning me-1"
            aria-hidden="true"
        ></i>
    @endif

    <span class="visually-hidden">
        Urgency:
    </span>

    {{ $urgencyLabel }}
</span>

                            <span class="referral-type">
                                {{ $referral->referral_type === 'internal'
                                    ? 'Internal CRM referral'
                                    : 'External referral'
                                }}
                            </span>
                        </div>
                    </header>

                    <div class="patient-referral-card__details">
                        <div class="referral-detail">
                            <span class="referral-detail__label">
                                Referral date
                            </span>

                            <span class="referral-detail__value">
                                {{ optional(
                                    $referral->referred_at
                                )->format('d/m/Y, h:i A') ?: 'Not recorded' }}
                            </span>
                        </div>

                        <div class="referral-detail">
                            <span class="referral-detail__label">
                                Referring doctor
                            </span>

                            <span class="referral-detail__value">
                                {{ $referringDoctorName ?: 'Not recorded' }}
                            </span>
                        </div>

                        <div class="referral-detail">
                            <span class="referral-detail__label">
                                Hospital or organisation
                            </span>

                            <span class="referral-detail__value">
                                {{ $referral->receiving_organisation_name ?: 'Not recorded' }}
                            </span>
                        </div>

                        <div class="referral-detail">
                            <span class="referral-detail__label">
                                Contact
                            </span>

                            <span class="referral-detail__value">
                                @if($referral->receiving_doctor_phone)
                                    <a
                                        href="tel:{{ preg_replace(
                                            '/[^0-9+]/',
                                            '',
                                            $referral->receiving_doctor_phone
                                        ) }}"
                                    >
                                        {{ $referral->receiving_doctor_phone }}
                                    </a>
                                @endif

                                @if(
                                    $referral->receiving_doctor_phone &&
                                    $referral->receiving_doctor_email
                                )
                                    <br>
                                @endif

                                @if($referral->receiving_doctor_email)
                                    <a
                                        href="mailto:{{ $referral->receiving_doctor_email }}"
                                    >
                                        {{ $referral->receiving_doctor_email }}
                                    </a>
                                @endif

                                @if(
                                    !$referral->receiving_doctor_phone &&
                                    !$referral->receiving_doctor_email
                                )
                                    Not recorded
                                @endif
                            </span>
                        </div>

                        <div class="referral-detail">
                            <span class="referral-detail__label">
                                Clinic
                            </span>

                            <span class="referral-detail__value">
                                {{ optional(
                                    optional($appointment)
                                        ->cliniccenter
                                )->name ?: 'Not recorded' }}
                            </span>
                        </div>

                        <div class="referral-detail">
                            <span class="referral-detail__label">
                                Service
                            </span>

                            <span class="referral-detail__value">
                                {{ optional(
                                    optional($appointment)
                                        ->clinicservice
                                )->name ?: 'Not recorded' }}
                            </span>
                        </div>
                    </div>

                    <div class="patient-referral-card__clinical">
                        <div class="referral-text-section">
                            <h5>Reason for referral</h5>

                            <p>
                                {!! nl2br(e(
                                    $referral->referral_reason
                                )) !!}
                            </p>
                        </div>

                        <div class="referral-text-section">
                            <h5>Clinical summary</h5>

                            <p>
                                {!! nl2br(e(
                                    $referral->clinical_summary
                                )) !!}
                            </p>
                        </div>

                        @if($referral->diagnosis)
                            <div class="referral-text-section">
                                <h5>Diagnosis</h5>

                                <p>
                                    {!! nl2br(e(
                                        $referral->diagnosis
                                    )) !!}
                                </p>
                            </div>
                        @endif

                        @if($referral->requested_action)
                            <div class="referral-text-section">
                                <h5>Requested action</h5>

                                <p>
                                    {!! nl2br(e(
                                        $referral->requested_action
                                    )) !!}
                                </p>
                            </div>
                        @endif
                    </div>

                    <footer class="patient-referral-card__footer">
                        @if($appointment)
                            <a
                                href="{{ route(
                                    'backend.appointments.referral.pdf',
                                    $appointment->id
                                ) }}"
                                class="patient-referral-download"
                            >
                                <i
                                    class="ph ph-file-pdf"
                                    aria-hidden="true"
                                ></i>

                                Download referral PDF
                            </a>
                        @endif
                    </footer>
                </article>
            @endforeach
        </div>
    @endif
</section>
<style>

    /*
 * Routine: neutral and high contrast.
 */
.referral-status--routine {
    color: #111;
    background-color: #fff;
    border: 2px solid #333;
}

/*
 * Urgent: lighter red warning.
 */
.referral-status--urgent {
    color: #7a1010;
    background-color: #ffe3e3;
    border: 2px solid #c53030;
}

/*
 * Emergency: stronger red warning.
 */
.referral-status--emergency {
    color: #fff;
    background-color: #a40000;
    border: 3px solid #650000;
    font-weight: 800;
    text-transform: uppercase;
}
</style>