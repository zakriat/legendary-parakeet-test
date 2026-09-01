@php
    $appointment = $data->appointmentdetail;

    $conditions = $appointment?->patientConditions ?? collect();
    $medications = $appointment?->patientMedications ?? collect();
    $allergies = $appointment?->patientAllergies ?? collect();
    $socialHistories = $appointment?->patientSocialHistories ?? collect();
    $familyHistories = $appointment?->patientFamilyHistories ?? collect();
    $observations = $appointment?->patientObservations ?? collect();

    $hasClinicalHistory =
        $conditions->isNotEmpty() ||
        $medications->isNotEmpty() ||
        $allergies->isNotEmpty() ||
        $socialHistories->isNotEmpty() ||
        $familyHistories->isNotEmpty() ||
        $observations->isNotEmpty();
@endphp

<div class="appointment-clinical-summary mt-3">
    <div class="clinical-summary-header">
        <div>
            <div class="clinical-summary-icon">
                <i class="ph ph-first-aid-kit"></i>
            </div>
        </div>

        <div class="flex-grow-1">
            <h5 class="mb-1">
                {{ __('Patient Clinical Summary') }}
            </h5>

            <p class="mb-0">
                {{ __('Information provided during appointment booking') }}
            </p>
        </div>

        @if($appointment)
            <span class="clinical-appointment-badge">
                #{{ $appointment->id }}
            </span>
        @endif
    </div>

    <div class="clinical-summary-body">
        @if(!$appointment)
            <div class="clinical-empty-state">
                <i class="ph ph-calendar-x"></i>

                <h6>{{ __('Appointment not found') }}</h6>

                <p>
                    {{ __('This encounter is not connected to an appointment.') }}
                </p>
            </div>
        @elseif(!$hasClinicalHistory)
            <div class="clinical-empty-state">
                <i class="ph ph-clipboard-text"></i>

                <h6>{{ __('No structured clinical history') }}</h6>

                <p>
                    {{ __('The patient did not provide structured clinical information for this appointment.') }}
                </p>
            </div>
        @else
            {{-- Allergy warning --}}
            @if($allergies->isNotEmpty())
                <div class="clinical-allergy-warning">
                    <div class="clinical-warning-icon">
                        <i class="ph ph-warning-circle"></i>
                    </div>

                    <div class="flex-grow-1">
                        <h6 class="mb-2">
                            {{ __('Allergy Warning') }}
                        </h6>

                        <div class="d-flex flex-wrap gap-2">
                            @foreach($allergies as $allergy)
                                @php
                                    $severity = strtolower(
                                        $allergy->severity ?? 'unknown'
                                    );

                                    $severityClass = match ($severity) {
                                        'severe', 'high' => 'severity-high',
                                        'moderate', 'medium' => 'severity-medium',
                                        'mild', 'low' => 'severity-low',
                                        default => 'severity-unknown',
                                    };
                                @endphp

                                <div class="allergy-item">
                                    <div>
                                        <strong>
                                            {{ $allergy->allergen }}
                                        </strong>

                                        @if($allergy->reaction)
                                            <span>
                                                — {{ $allergy->reaction }}
                                            </span>
                                        @endif
                                    </div>

                                    <span class="severity-badge {{ $severityClass }}">
                                        {{ ucfirst($severity) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="row g-3">
                {{-- Conditions --}}
                <div class="col-12 col-xl-6">
                    <div class="clinical-section-card h-100">
                        <div class="clinical-section-heading">
                            <div class="section-icon condition-icon">
                                <i class="ph ph-heartbeat"></i>
                            </div>

                            <div>
                                <h6>{{ __('Medical Conditions') }}</h6>
                                <small>
                                    {{ $conditions->count() }}
                                    {{ trans_choice('record|records', $conditions->count()) }}
                                </small>
                            </div>
                        </div>

                        @forelse($conditions as $condition)
                            <div class="clinical-record">
                                <div class="clinical-record-main">
                                    <strong>
                                        {{ $condition->condition_name }}
                                    </strong>

                                    <span class="record-status">
                                        {{ ucfirst($condition->status ?? 'active') }}
                                    </span>
                                </div>

                                @if($condition->diagnosed_at)
                                    <small>
                                        <i class="ph ph-calendar-blank"></i>
                                        {{ __('Diagnosed') }}:
                                        {{ \Carbon\Carbon::parse($condition->diagnosed_at)->format('d M Y') }}
                                    </small>
                                @endif

                                @if($condition->notes)
                                    <p>{{ $condition->notes }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="clinical-section-empty">
                                <i class="ph ph-check-circle"></i>
                                {{ __('No conditions recorded') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Medications --}}
                <div class="col-12 col-xl-6">
                    <div class="clinical-section-card h-100">
                        <div class="clinical-section-heading">
                            <div class="section-icon medication-icon">
                                <i class="ph ph-pill"></i>
                            </div>

                            <div>
                                <h6>{{ __('Current Medications') }}</h6>
                                <small>
                                    {{ $medications->count() }}
                                    {{ trans_choice('record|records', $medications->count()) }}
                                </small>
                            </div>
                        </div>

                        @forelse($medications as $medication)
                            <div class="clinical-record">
                                <div class="clinical-record-main">
                                    <strong>
                                        {{ $medication->medication_name }}
                                    </strong>

                                    @if($medication->status)
                                        <span class="record-status">
                                            {{ ucfirst($medication->status) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="clinical-detail-grid">
                                    @if($medication->dose)
                                        <span>
                                            <i class="ph ph-drop"></i>
                                            {{ __('Dose') }}:
                                            {{ $medication->dose }}
                                        </span>
                                    @endif

                                    @if($medication->frequency)
                                        <span>
                                            <i class="ph ph-clock"></i>
                                            {{ $medication->frequency }}
                                        </span>
                                    @endif

                                    @if($medication->route)
                                        <span>
                                            <i class="ph ph-signpost"></i>
                                            {{ ucfirst($medication->route) }}
                                        </span>
                                    @endif
                                </div>

                                @if($medication->notes)
                                    <p>{{ $medication->notes }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="clinical-section-empty">
                                <i class="ph ph-check-circle"></i>
                                {{ __('No medications recorded') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Social history --}}
                <div class="col-12 col-xl-6">
                    <div class="clinical-section-card h-100">
                        <div class="clinical-section-heading">
                            <div class="section-icon social-icon">
                                <i class="ph ph-user-focus"></i>
                            </div>

                            <div>
                                <h6>{{ __('Social History') }}</h6>
                                <small>{{ __('Lifestyle information') }}</small>
                            </div>
                        </div>

                        @forelse($socialHistories as $social)
                            <div class="social-history-grid">
                                <div class="social-history-item">
                                    <i class="ph ph-cigarette"></i>

                                    <div>
                                        <small>{{ __('Smoking') }}</small>

                                        <strong>
                                            {{ ucfirst($social->smoking_status ?? 'Not provided') }}
                                        </strong>
                                    </div>
                                </div>

                                <div class="social-history-item">
                                    <i class="ph ph-wine"></i>

                                    <div>
                                        <small>{{ __('Alcohol') }}</small>

                                        <strong>
                                            {{ ucfirst($social->alcohol_status ?? 'Not provided') }}
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            @if($social->notes)
                                <div class="clinical-notes">
                                    <strong>{{ __('Other information') }}</strong>
                                    <p>{{ $social->notes }}</p>
                                </div>
                            @endif
                        @empty
                            <div class="clinical-section-empty">
                                <i class="ph ph-info"></i>
                                {{ __('No social history provided') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Family history --}}
                <div class="col-12 col-xl-6">
                    <div class="clinical-section-card h-100">
                        <div class="clinical-section-heading">
                            <div class="section-icon family-icon">
                                <i class="ph ph-users-three"></i>
                            </div>

                            <div>
                                <h6>{{ __('Family History') }}</h6>
                                <small>
                                    {{ $familyHistories->count() }}
                                    {{ trans_choice('record|records', $familyHistories->count()) }}
                                </small>
                            </div>
                        </div>

                        @forelse($familyHistories as $familyHistory)
                            <div class="clinical-record">
                                <div class="clinical-record-main">
                                    <strong>
                                        {{ $familyHistory->condition_name }}
                                    </strong>

                                    @if($familyHistory->relationship)
                                        <span class="relationship-badge">
                                            {{ ucfirst($familyHistory->relationship) }}
                                        </span>
                                    @endif
                                </div>

                                @if($familyHistory->age_at_diagnosis)
                                    <small>
                                        {{ __('Age at diagnosis') }}:
                                        {{ $familyHistory->age_at_diagnosis }}
                                    </small>
                                @endif

                                @if($familyHistory->notes)
                                    <p>{{ $familyHistory->notes }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="clinical-section-empty">
                                <i class="ph ph-info"></i>
                                {{ __('No family history recorded') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Observations --}}
                <div class="col-12">
                    <div class="clinical-section-card">
                        <div class="clinical-section-heading">
                            <div class="section-icon observation-icon">
                                <i class="ph ph-activity"></i>
                            </div>

                            <div>
                                <h6>{{ __('Patient Observations') }}</h6>
                                <small>{{ __('Measurements provided during booking') }}</small>
                            </div>
                        </div>

                        @forelse($observations as $observation)
                            <div class="observation-grid">
                                <div class="observation-item">
                                    <span>{{ __('Height') }}</span>

                                    <strong>
                                        {{ $observation->height_cm
                                            ? $observation->height_cm . ' cm'
                                            : '—' }}
                                    </strong>
                                </div>

                                <div class="observation-item">
                                    <span>{{ __('Weight') }}</span>

                                    <strong>
                                        {{ $observation->weight_kg
                                            ? $observation->weight_kg . ' kg'
                                            : '—' }}
                                    </strong>
                                </div>

                                <div class="observation-item">
                                    <span>{{ __('BMI') }}</span>

                                    <strong>
                                        {{ $observation->bmi ?: '—' }}
                                    </strong>
                                </div>

                                <div class="observation-item">
                                    <span>{{ __('Blood Pressure') }}</span>

                                    <strong>
                                        @if(
                                            $observation->systolic ||
                                            $observation->diastolic
                                        )
                                            {{ $observation->systolic ?: '—' }}
                                            /
                                            {{ $observation->diastolic ?: '—' }}
                                            mmHg
                                        @else
                                            —
                                        @endif
                                    </strong>
                                </div>
                            </div>

                            @if($observation->notes)
                                <div class="clinical-notes mt-3">
                                    <strong>{{ __('Observation notes') }}</strong>
                                    <p>{{ $observation->notes }}</p>
                                </div>
                            @endif
                        @empty
                            <div class="clinical-section-empty">
                                <i class="ph ph-info"></i>
                                {{ __('No observations recorded') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- Existing free-text booking notes --}}
        @if($appointment?->appointment_extra_info)
            <div class="booking-notes-card">
                <div class="booking-notes-icon">
                    <i class="ph ph-note-pencil"></i>
                </div>

                <div>
                    <h6>{{ __('Presenting Complaint / Booking Notes') }}</h6>
                    <p>{{ $appointment->appointment_extra_info }}</p>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .appointment-clinical-summary {
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: 14px;
        box-shadow: 0 6px 22px rgba(31, 41, 55, 0.06);
        overflow: hidden;
    }

    .clinical-summary-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        color: #fff;
        background: linear-gradient(135deg, #5263e3, #7556cc);
    }

    .clinical-summary-header h5 {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
    }

    .clinical-summary-header p {
        color: rgba(255, 255, 255, 0.82);
        font-size: 12px;
    }

    .clinical-summary-icon {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        border-radius: 12px;
        font-size: 22px;
        background: rgba(255, 255, 255, 0.18);
    }

    .clinical-appointment-badge {
        padding: 5px 9px;
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.14);
    }

    .clinical-summary-body {
        padding: 14px;
    }

    .clinical-allergy-warning {
        display: flex;
        gap: 12px;
        margin-bottom: 14px;
        padding: 13px;
        color: #7f1d1d;
        border: 1px solid #fecaca;
        border-radius: 12px;
        background: #fff5f5;
    }

    .clinical-warning-icon {
        font-size: 24px;
        color: #dc2626;
    }

    .allergy-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 9px;
        border-radius: 8px;
        background: #fff;
    }

    .severity-badge,
    .record-status,
    .relationship-badge {
        display: inline-flex;
        padding: 3px 7px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }

    .severity-high {
        color: #fff;
        background: #dc2626;
    }

    .severity-medium {
        color: #92400e;
        background: #fef3c7;
    }

    .severity-low {
        color: #166534;
        background: #dcfce7;
    }

    .severity-unknown {
        color: #475569;
        background: #e2e8f0;
    }

    .clinical-section-card {
        padding: 13px;
        border: 1px solid #e8edf3;
        border-radius: 12px;
        background: #fbfcfe;
    }

    .clinical-section-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .clinical-section-heading h6 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
    }

    .clinical-section-heading small {
        color: #87909e;
        font-size: 10px;
    }

    .section-icon {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border-radius: 9px;
        font-size: 17px;
    }

    .condition-icon {
        color: #dc2626;
        background: #fee2e2;
    }

    .medication-icon {
        color: #2563eb;
        background: #dbeafe;
    }

    .social-icon {
        color: #7c3aed;
        background: #ede9fe;
    }

    .family-icon {
        color: #c2410c;
        background: #ffedd5;
    }

    .observation-icon {
        color: #047857;
        background: #d1fae5;
    }

    .clinical-record {
        padding: 9px 0;
        border-top: 1px solid #edf0f4;
    }

    .clinical-record:first-of-type {
        border-top: 0;
    }

    .clinical-record-main {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .clinical-record-main strong {
        color: #1e293b;
        font-size: 12px;
    }

    .record-status {
        color: #166534;
        background: #dcfce7;
    }

    .relationship-badge {
        color: #4338ca;
        background: #e0e7ff;
    }

    .clinical-record small,
    .clinical-detail-grid {
        color: #64748b;
        font-size: 10px;
    }

    .clinical-record p,
    .clinical-notes p,
    .booking-notes-card p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 11px;
        line-height: 1.5;
        white-space: pre-line;
    }

    .clinical-detail-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px 14px;
        margin-top: 5px;
    }

    .social-history-grid,
    .observation-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .social-history-item,
    .observation-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px;
        border-radius: 9px;
        background: #fff;
    }

    .social-history-item > i {
        font-size: 18px;
        color: #7c3aed;
    }

    .social-history-item small,
    .observation-item span {
        display: block;
        color: #87909e;
        font-size: 9px;
    }

    .social-history-item strong,
    .observation-item strong {
        color: #1e293b;
        font-size: 11px;
    }

    .observation-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .observation-item {
        display: block;
        border: 1px solid #edf0f4;
    }

    .clinical-section-empty,
    .clinical-empty-state {
        color: #87909e;
        font-size: 11px;
        text-align: center;
    }

    .clinical-empty-state {
        padding: 25px 10px;
    }

    .clinical-empty-state > i {
        display: block;
        margin-bottom: 8px;
        font-size: 32px;
        color: #a5adba;
    }

    .clinical-empty-state h6 {
        margin-bottom: 5px;
        color: #475569;
    }

    .clinical-empty-state p {
        margin: 0;
    }

    .booking-notes-card {
        display: flex;
        gap: 10px;
        margin-top: 14px;
        padding: 12px;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        background: #eff6ff;
    }

    .booking-notes-icon {
        font-size: 21px;
        color: #2563eb;
    }

    .booking-notes-card h6 {
        margin: 0;
        color: #1e40af;
        font-size: 12px;
        font-weight: 700;
    }

    @media (max-width: 1199.98px) {
        .observation-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .clinical-summary-header {
            align-items: flex-start;
        }

        .clinical-appointment-badge {
            display: none;
        }

        .social-history-grid,
        .observation-grid {
            grid-template-columns: 1fr;
        }
    }
</style>