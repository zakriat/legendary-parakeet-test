@php
    $appointment = $data->appointmentdetail;

    /*
     * Support common relationship-name variations.
     */
    $getRelation = function ($model, array $names) {
        if (!$model) {
            return collect();
        }

        foreach ($names as $name) {
            if (method_exists($model, $name)) {
                return $model->{$name} ?? collect();
            }

            if ($model->relationLoaded($name)) {
                return $model->getRelation($name) ?? collect();
            }
        }

        return collect();
    };

    $conditions = $getRelation($appointment, [
        'conditions',
        'patientConditions',
    ]);

    $medications = $getRelation($appointment, [
        'medications',
        'patientMedications',
    ]);

    $allergies = $getRelation($appointment, [
        'allergies',
        'patientAllergies',
    ]);

    $socialHistories = $getRelation($appointment, [
        'socialHistories',
        'socialHistory',
        'patientSocialHistories',
        'patientSocialHistory',
    ]);

    $familyHistories = $getRelation($appointment, [
        'familyHistories',
        'familyHistory',
        'patientFamilyHistories',
    ]);

    $observations = $getRelation($appointment, [
        'observations',
        'patientObservations',
    ]);

    /*
     * A HasOne relationship returns one model rather than a
     * collection. Convert it into a collection for display.
     */
    if (
        $socialHistories &&
        !($socialHistories instanceof
            \Illuminate\Support\Collection) &&
        !($socialHistories instanceof
            \Illuminate\Database\Eloquent\Collection)
    ) {
        $socialHistories = collect([$socialHistories]);
    }

    if (
        $observations &&
        !($observations instanceof
            \Illuminate\Support\Collection) &&
        !($observations instanceof
            \Illuminate\Database\Eloquent\Collection)
    ) {
        $observations = collect([$observations]);
    }
@endphp

<style>
    .patient-history {
        background: #ffffff;
        color: #111111;
    }

    .patient-history,
    .patient-history h2,
    .patient-history h3,
    .patient-history p,
    .patient-history dt,
    .patient-history dd,
    .patient-history span,
    .patient-history strong {
        color: #111111;
    }

    .patient-history__header {
        border-bottom: 2px solid #222222;
        margin-bottom: 20px;
        padding-bottom: 14px;
    }

    .patient-history__header h2 {
        font-size: 21px;
        font-weight: 700;
        margin: 0 0 5px;
    }

    .patient-history__header p {
        font-size: 15px;
        margin: 0;
    }

    .patient-history__grid {
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .patient-history__card {
        background: #ffffff;
        border: 2px solid #444444;
        border-radius: 6px;
        padding: 18px;
    }

    .patient-history__card--wide {
        grid-column: 1 / -1;
    }

    .patient-history__card h3 {
        border-bottom: 1px solid #555555;
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 15px;
        padding-bottom: 10px;
    }

    .patient-history__item {
        border-bottom: 1px solid #aaaaaa;
        line-height: 1.6;
        padding: 10px 0;
    }

    .patient-history__item:first-of-type {
        padding-top: 0;
    }

    .patient-history__item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .patient-history__primary {
        display: block;
        font-size: 16px;
        font-weight: 700;
    }

    .patient-history__details {
        display: block;
        font-size: 15px;
        margin-top: 3px;
    }

    .patient-history__empty {
        border: 1px dashed #555555;
        border-radius: 4px;
        color: #222222;
        margin: 0;
        padding: 12px;
    }

    .patient-history__warning {
        border: 3px solid #111111;
    }

    .patient-history__warning h3::before {
        content: "Important: ";
        font-weight: 800;
    }

    .patient-history__observations {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(
            auto-fit,
            minmax(150px, 1fr)
        );
    }

    .patient-history__measurement {
        border: 1px solid #555555;
        border-radius: 4px;
        padding: 10px;
    }

    .patient-history__measurement dt {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .patient-history__measurement dd {
        font-size: 16px;
        margin: 0;
    }

    @media (max-width: 900px) {
        .patient-history__grid {
            grid-template-columns: 1fr;
        }

        .patient-history__card--wide {
            grid-column: auto;
        }
    }
</style>

<section
    class="patient-history"
    aria-labelledby="patient-history-heading"
>
    <header class="patient-history__header">
        <h2 id="patient-history-heading">
            Patient History
        </h2>

        <p>
            Information supplied during appointment booking
            for appointment
            <strong>
                #{{ $appointment?->id ?? 'Not available' }}
            </strong>.
        </p>
    </header>

    @if(!$appointment)
        <div
            class="patient-history__empty"
            role="status"
        >
            No appointment is connected to this encounter.
        </div>
    @else
        <div class="patient-history__grid">
            <section
                class="patient-history__card patient-history__warning"
                aria-labelledby="allergies-heading"
            >
                <h3 id="allergies-heading">
                    Allergies
                </h3>

                @forelse($allergies as $allergy)
                    <div class="patient-history__item">
                        <span class="patient-history__primary">
                            {{ $allergy->allergen
                                ?: 'Unnamed allergy' }}
                        </span>

                        <span class="patient-history__details">
                            <strong>Reaction:</strong>
                            {{ $allergy->reaction ?: 'Not recorded' }}
                        </span>

                        <span class="patient-history__details">
                            <strong>Severity:</strong>
                            {{ ucfirst(
                                $allergy->severity ?? 'unknown'
                            ) }}
                        </span>

                        @if($allergy->notes)
                            <span class="patient-history__details">
                                <strong>Notes:</strong>
                                {{ $allergy->notes }}
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="patient-history__empty">
                        No allergies were recorded.
                    </p>
                @endforelse
            </section>

            <section
                class="patient-history__card"
                aria-labelledby="conditions-heading"
            >
                <h3 id="conditions-heading">
                    Medical Conditions
                </h3>

                @forelse($conditions as $condition)
                    <div class="patient-history__item">
                        <span class="patient-history__primary">
                            {{ $condition->condition_name
                                ?: 'Unnamed condition' }}
                        </span>

                        <span class="patient-history__details">
                            <strong>Status:</strong>
                            {{ ucfirst(
                                $condition->status ?? 'active'
                            ) }}
                        </span>

                        @if($condition->diagnosed_at)
                            <span class="patient-history__details">
                                <strong>Diagnosed:</strong>
                                {{ \Carbon\Carbon::parse(
                                    $condition->diagnosed_at
                                )->format('d/m/Y') }}
                            </span>
                        @endif

                        @if($condition->notes)
                            <span class="patient-history__details">
                                <strong>Notes:</strong>
                                {{ $condition->notes }}
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="patient-history__empty">
                        No medical conditions were recorded.
                    </p>
                @endforelse
            </section>

<!-- for presenting complain -->
                    @php
            $presentingComplaint = optional(
                $data->appointmentdetail
            )->presenting_complaint;
        @endphp

        <section
            class="border rounded p-3 mb-4"
            aria-labelledby="presenting-complaint-heading"
        >
            <h5
                id="presenting-complaint-heading"
                class="mb-2 text-dark"
            >
                Presenting complaint
            </h5>

            @if(filled($presentingComplaint))
                <div
                    class="text-dark"
                    style="white-space: pre-wrap;"
                >{{ $presentingComplaint }}</div>
            @else
                <p class="mb-0 text-dark">
                    No presenting complaint was recorded for this
                    appointment.
                </p>
            @endif
        </section>

        <!-- ends -->

            <section
                class="patient-history__card"
                aria-labelledby="medications-heading"
            >
                <h3 id="medications-heading">
                    Current Medication
                </h3>

                @forelse($medications as $medication)
                    <div class="patient-history__item">
                        <span class="patient-history__primary">
                            {{ $medication->medication_name
                                ?: 'Unnamed medication' }}
                        </span>

                        <span class="patient-history__details">
                            <strong>Dose:</strong>
                            {{ $medication->dose ?: 'Not recorded' }}
                        </span>

                        <span class="patient-history__details">
                            <strong>Frequency:</strong>
                            {{ $medication->frequency
                                ?: 'Not recorded' }}
                        </span>

                        @if($medication->route)
                            <span class="patient-history__details">
                                <strong>Route:</strong>
                                {{ $medication->route }}
                            </span>
                        @endif

                        @if($medication->notes)
                            <span class="patient-history__details">
                                <strong>Notes:</strong>
                                {{ $medication->notes }}
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="patient-history__empty">
                        No medication was recorded.
                    </p>
                @endforelse
            </section>

            <section
                class="patient-history__card"
                aria-labelledby="social-history-heading"
            >
                <h3 id="social-history-heading">
                    Social History
                </h3>

                @forelse($socialHistories as $social)
                    <div class="patient-history__item">
                        <span class="patient-history__details">
                            <strong>Smoking:</strong>
                            {{ ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    $social->smoking_status
                                        ?? 'not recorded'
                                )
                            ) }}
                        </span>

                        <span class="patient-history__details">
                            <strong>Alcohol:</strong>
                            {{ ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    $social->alcohol_status
                                        ?? 'not recorded'
                                )
                            ) }}
                        </span>

                        @if($social->notes ?? null)
                            <span class="patient-history__details">
                                <strong>Other information:</strong>
                                {{ $social->notes }}
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="patient-history__empty">
                        No social history was recorded.
                    </p>
                @endforelse
            </section>

            <section
                class="patient-history__card"
                aria-labelledby="family-history-heading"
            >
                <h3 id="family-history-heading">
                    Family History
                </h3>

                @forelse($familyHistories as $family)
                    <div class="patient-history__item">
                        <span class="patient-history__primary">
                            {{ $family->condition_name
                                ?: 'Unnamed condition' }}
                        </span>

                        <span class="patient-history__details">
                            <strong>Relationship:</strong>
                            {{ $family->relationship
                                ?: 'Not recorded' }}
                        </span>

                        @if($family->age_at_diagnosis)
                            <span class="patient-history__details">
                                <strong>Age at diagnosis:</strong>
                                {{ $family->age_at_diagnosis }}
                            </span>
                        @endif

                        @if($family->notes)
                            <span class="patient-history__details">
                                <strong>Notes:</strong>
                                {{ $family->notes }}
                            </span>
                        @endif
                    </div>
                @empty
                    <p class="patient-history__empty">
                        No family history was recorded.
                    </p>
                @endforelse
            </section>

            <section
                class="patient-history__card patient-history__card--wide"
                aria-labelledby="observations-heading"
            >
                <h3 id="observations-heading">
                    Patient Observations
                </h3>

                @forelse($observations as $observation)
                    <dl class="patient-history__observations">
                        <div class="patient-history__measurement">
                            <dt>Height</dt>
                            <dd>
                                {{ $observation->height_cm
                                    ? $observation->height_cm . ' cm'
                                    : 'Not recorded' }}
                            </dd>
                        </div>

                        <div class="patient-history__measurement">
                            <dt>Weight</dt>
                            <dd>
                                {{ $observation->weight_kg
                                    ? $observation->weight_kg . ' kg'
                                    : 'Not recorded' }}
                            </dd>
                        </div>

                        <div class="patient-history__measurement">
                            <dt>BMI</dt>
                            <dd>
                                {{ $observation->bmi
                                    ?: 'Not recorded' }}
                            </dd>
                        </div>

                        <div class="patient-history__measurement">
                            <dt>Blood pressure</dt>
                            <dd>
                                @if(
                                    $observation->systolic_bp &&
                                    $observation->diastolic_bp
                                )
                                    {{ $observation->systolic_bp }}/{{ $observation->diastolic_bp }}
                                    mmHg
                                @else
                                    Not recorded
                                @endif
                            </dd>
                        </div>

                        <div class="patient-history__measurement">
                            <dt>Pulse</dt>
                            <dd>
                                {{ $observation->pulse
                                    ? $observation->pulse . ' bpm'
                                    : 'Not recorded' }}
                            </dd>
                        </div>

                        <div class="patient-history__measurement">
                            <dt>Temperature</dt>
                            <dd>
                                {{ $observation->temperature_c
                                    ? $observation->temperature_c . ' °C'
                                    : 'Not recorded' }}
                            </dd>
                        </div>

                        <div class="patient-history__measurement">
                            <dt>Oxygen saturation</dt>
                            <dd>
                                {{ $observation->spo2
                                    ? $observation->spo2 . '%'
                                    : 'Not recorded' }}
                            </dd>
                        </div>
                    </dl>
                @empty
                    <p class="patient-history__empty">
                        No observations were recorded.
                    </p>
                @endforelse
            </section>
        </div>
    @endif
</section>