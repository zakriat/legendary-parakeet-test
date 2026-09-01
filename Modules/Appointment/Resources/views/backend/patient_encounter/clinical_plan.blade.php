<!-- @php
    $clinicalPlan = $data->clinicalPlan;

    $routeName = Route::has(
        'backend.encounter.save-clinical-plan'
    )
        ? 'backend.encounter.save-clinical-plan'
        : 'encounter.save-clinical-plan';
@endphp -->

@php
    $clinicalPlan = $data->clinicalPlan;

    $clinicalPlanUrl = url(
        'app/encounter/encounter-detail-page/' .
        $data->id .
        '/clinical-plan'
    );
@endphp
<style>
    .clinical-plan {
        background: #ffffff;
        border: 2px solid #202020;
        border-radius: 8px;
        color: #111111;
        margin-top: 24px;
    }

    .clinical-plan,
    .clinical-plan label,
    .clinical-plan h2,
    .clinical-plan h3,
    .clinical-plan p,
    .clinical-plan small {
        color: #111111;
    }

    .clinical-plan__header {
        background: #ffffff;
        border-bottom: 2px solid #202020;
        padding: 20px;
    }

    .clinical-plan__header h2 {
        font-size: 21px;
        font-weight: 700;
        line-height: 1.3;
        margin: 0 0 5px;
    }

    .clinical-plan__body {
        padding: 20px;
    }

    .clinical-plan__section {
        border: 1px solid #666666;
        border-radius: 6px;
        margin-bottom: 22px;
        padding: 18px;
    }

    .clinical-plan__section-title {
        border-bottom: 1px solid #777777;
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 18px;
        padding-bottom: 10px;
    }

    .clinical-plan .form-label {
        color: #111111;
        display: block;
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 7px;
    }

    .clinical-plan .form-control,
    .clinical-plan .form-select {
        background: #ffffff;
        border: 2px solid #555555;
        border-radius: 5px;
        color: #111111;
        font-size: 16px;
        line-height: 1.5;
        min-height: 46px;
    }

    .clinical-plan textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .clinical-plan .form-control::placeholder {
        color: #555555;
        opacity: 1;
    }

    .clinical-plan .form-control:focus,
    .clinical-plan .form-select:focus,
    .clinical-plan .form-check-input:focus,
    .clinical-plan button:focus {
        border-color: #000000;
        box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.25);
        outline: 2px solid transparent;
    }

    .clinical-plan__help {
        color: #333333;
        display: block;
        font-size: 14px;
        margin-top: 6px;
    }

    .clinical-plan__required {
        font-weight: 700;
    }

    .clinical-plan__status {
        border: 1px solid #333333;
        border-radius: 4px;
        color: #111111;
        display: inline-block;
        font-size: 14px;
        font-weight: 700;
        margin-top: 8px;
        padding: 5px 9px;
    }

    .clinical-plan__alert {
        border: 2px solid #222222;
        border-radius: 5px;
        color: #111111;
        margin-bottom: 20px;
        padding: 14px 16px;
    }

    .clinical-plan__alert-success {
        background: #f2f2f2;
    }

    .clinical-plan__alert-error {
        background: #ffffff;
        border-left: 7px solid #000000;
    }

    .clinical-plan__checkbox {
        align-items: flex-start;
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
    }

    .clinical-plan__checkbox input {
        border: 2px solid #333333;
        flex: 0 0 auto;
        height: 22px;
        margin-top: 1px;
        width: 22px;
    }

    .clinical-plan__checkbox label {
        font-size: 16px;
        font-weight: 700;
        line-height: 1.4;
    }

    .clinical-plan__actions {
        align-items: center;
        border-top: 1px solid #777777;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: space-between;
        margin-top: 20px;
        padding-top: 18px;
    }

    .clinical-plan__save {
        background: #111111;
        border: 2px solid #111111;
        border-radius: 5px;
        color: #ffffff;
        cursor: pointer;
        font-size: 16px;
        font-weight: 700;
        min-height: 48px;
        padding: 10px 22px;
    }

    .clinical-plan__save:hover {
        background: #ffffff;
        color: #111111;
    }

    .clinical-plan__metadata {
        color: #222222;
        font-size: 14px;
        line-height: 1.7;
    }

    @media (max-width: 767px) {
        .clinical-plan__body,
        .clinical-plan__header {
            padding: 15px;
        }

        .clinical-plan__section {
            padding: 14px;
        }

        .clinical-plan__save {
            width: 100%;
        }
    }
</style>

<section
    class="clinical-plan"
    aria-labelledby="clinical-plan-heading"
>
    <header class="clinical-plan__header">
        <h2 id="clinical-plan-heading">
            Doctor Clinical Assessment
        </h2>

        <p class="mb-0">
            Record the examination, diagnosis, treatment,
            advice and follow-up plan for this encounter.
        </p>

        <span class="clinical-plan__status">
            {{ $clinicalPlan
                ? 'Assessment recorded'
                : 'Assessment not yet recorded'
            }}
        </span>
    </header>

    <div class="clinical-plan__body">
        @if(session('clinical_plan_success'))
            <div
                class="clinical-plan__alert clinical-plan__alert-success"
                role="status"
            >
                {{ session('clinical_plan_success') }}
            </div>
        @endif

        @if($errors->any())
            <div
                class="clinical-plan__alert clinical-plan__alert-error"
                role="alert"
                aria-labelledby="clinical-errors-heading"
            >
                <strong id="clinical-errors-heading">
                    The assessment could not be saved:
                </strong>

                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ $clinicalPlanUrl }}"
        >
            @csrf

            <section
                class="clinical-plan__section"
                aria-labelledby="history-heading"
            >
                <h3
                    id="history-heading"
                    class="clinical-plan__section-title"
                >
                    Clinical history and examination
                </h3>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <label
                            for="doctor_history"
                            class="form-label"
                        >
                            Doctor history
                        </label>

                        <textarea
                            id="doctor_history"
                            name="doctor_history"
                            class="form-control"
                            rows="6"
                            placeholder="Relevant clinical history reported by the patient"
                        >{{ old(
                            'doctor_history',
                            $clinicalPlan?->doctor_history
                        ) }}</textarea>
                    </div>

                    <div class="col-lg-6">
                        <label
                            for="examination_findings"
                            class="form-label"
                        >
                            Examination findings
                        </label>

                        <textarea
                            id="examination_findings"
                            name="examination_findings"
                            class="form-control"
                            rows="6"
                            placeholder="Physical examination and clinical findings"
                        >{{ old(
                            'examination_findings',
                            $clinicalPlan?->examination_findings
                        ) }}</textarea>
                    </div>
                </div>
            </section>

            <section
                class="clinical-plan__section"
                aria-labelledby="assessment-heading"
            >
                <h3
                    id="assessment-heading"
                    class="clinical-plan__section-title"
                >
                    Assessment and Treatment plan
                </h3>

                <div class="row g-4">
                    <div class="col-12">
                        <label
                            for="diagnosis"
                            class="form-label"
                        >
                            Diagnosis
                        </label>

                        <textarea
                            id="diagnosis"
                            name="diagnosis"
                            class="form-control"
                            rows="5"
                            placeholder="Enter the provisional or confirmed diagnosis"
                        >{{ old(
                            'diagnosis',
                            $clinicalPlan?->diagnosis
                        ) }}</textarea>
                    </div>

                    <div class="col-lg-6">
                        <label
                            for="treatment"
                            class="form-label"
                        >
                            Treatment
                        </label>

                        <textarea
                            id="treatment"
                            name="treatment"
                            class="form-control"
                            rows="6"
                            placeholder="Treatment provided or recommended"
                        >{{ old(
                            'treatment',
                            $clinicalPlan?->treatment
                        ) }}</textarea>
                    </div>

                    <div class="col-lg-6">
                        <label
                            for="advice"
                            class="form-label"
                        >
                            Advice to patient
                        </label>

                        <textarea
                            id="advice"
                            name="advice"
                            class="form-control"
                            rows="6"
                            placeholder="Advice, warning signs and when to seek urgent help"
                        >{{ old(
                            'advice',
                            $clinicalPlan?->advice
                        ) }}</textarea>
                    </div>
                </div>
            </section>

            <section
                class="clinical-plan__section"
                aria-labelledby="follow-up-heading"
            >
                <h3
                    id="follow-up-heading"
                    class="clinical-plan__section-title"
                >
                    Follow-up
                </h3>

                <div class="clinical-plan__checkbox">
                    <input
                        type="checkbox"
                        id="follow_up_required"
                        name="follow_up_required"
                        value="1"
                        @checked(
                            old(
                                'follow_up_required',
                                $clinicalPlan?->follow_up_required
                            )
                        )
                    >

                    <label for="follow_up_required">
                        This patient requires follow-up
                    </label>
                </div>

                <div
                    id="follow-up-fields"
                    aria-live="polite"
                >
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label
                                for="follow_up_interval"
                                class="form-label"
                            >
                                Follow-up after
                            </label>

                            <input
                                type="number"
                                id="follow_up_interval"
                                name="follow_up_interval"
                                class="form-control"
                                min="1"
                                max="3650"
                                value="{{ old(
                                    'follow_up_interval',
                                    $clinicalPlan?->follow_up_interval
                                ) }}"
                                placeholder="For example: 2"
                            >
                        </div>

                        <div class="col-md-4">
                            <label
                                for="follow_up_interval_unit"
                                class="form-label"
                            >
                                Interval unit
                            </label>

                            <select
                                id="follow_up_interval_unit"
                                name="follow_up_interval_unit"
                                class="form-select"
                            >
                                @php
                                    $selectedUnit = old(
                                        'follow_up_interval_unit',
                                        $clinicalPlan
                                            ?->follow_up_interval_unit
                                    );
                                @endphp

                                <option value="">
                                    Select unit
                                </option>

                                <option
                                    value="days"
                                    @selected(
                                        $selectedUnit === 'days'
                                    )
                                >
                                    Days
                                </option>

                                <option
                                    value="weeks"
                                    @selected(
                                        $selectedUnit === 'weeks'
                                    )
                                >
                                    Weeks
                                </option>

                                <option
                                    value="months"
                                    @selected(
                                        $selectedUnit === 'months'
                                    )
                                >
                                    Months
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label
                                for="follow_up_date"
                                class="form-label"
                            >
                                Follow-up date
                            </label>

                            <input
                                type="date"
                                id="follow_up_date"
                                name="follow_up_date"
                                class="form-control"
                                value="{{ old(
                                    'follow_up_date',
                                    $clinicalPlan
                                        ?->follow_up_date
                                        ?->format('Y-m-d')
                                ) }}"
                            >
                        </div>

                        <div class="col-md-6">
                            <label
                                for="follow_up_status"
                                class="form-label"
                            >
                                Follow-up status
                            </label>

                            @php
                                $followUpStatus = old(
                                    'follow_up_status',
                                    $clinicalPlan
                                        ?->follow_up_status
                                        ?? 'planned'
                                );
                            @endphp

                            <select
                                id="follow_up_status"
                                name="follow_up_status"
                                class="form-select"
                            >
                                <option
                                    value="planned"
                                    @selected(
                                        $followUpStatus === 'planned'
                                    )
                                >
                                    Planned
                                </option>

                                <option
                                    value="booked"
                                    @selected(
                                        $followUpStatus === 'booked'
                                    )
                                >
                                    Appointment booked
                                </option>

                                <option
                                    value="completed"
                                    @selected(
                                        $followUpStatus === 'completed'
                                    )
                                >
                                    Completed
                                </option>

                                <option
                                    value="cancelled"
                                    @selected(
                                        $followUpStatus === 'cancelled'
                                    )
                                >
                                    Cancelled
                                </option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label
                                for="follow_up_reason"
                                class="form-label"
                            >
                                Follow-up reason and instructions
                            </label>

                            <textarea
                                id="follow_up_reason"
                                name="follow_up_reason"
                                class="form-control"
                                rows="4"
                                placeholder="Reason for follow-up and what should be reviewed"
                            >{{ old(
                                'follow_up_reason',
                                $clinicalPlan?->follow_up_reason
                            ) }}</textarea>

                            <small class="clinical-plan__help">
                                For “follow up in two weeks”,
                                enter 2 and select Weeks.
                            </small>
                        </div>
                    </div>
                </div>
            </section>

            <div class="clinical-plan__actions">
                <div class="clinical-plan__metadata">
                    @if($clinicalPlan?->prescriber_name)
                        <div>
                            <strong>Recorded by:</strong>
                            {{ $clinicalPlan->prescriber_name }}
                        </div>
                    @endif

                    @if($clinicalPlan?->prescriber_gmc_number)
                        <div>
                            <strong>GMC number:</strong>
                            {{ $clinicalPlan
                                ->prescriber_gmc_number }}
                        </div>
                    @endif

                    @if($clinicalPlan?->recorded_at)
                        <div>
                            <strong>Last recorded:</strong>
                            {{ $clinicalPlan->recorded_at
                                ->format('d/m/Y H:i') }}
                        </div>
                    @endif
                </div>

                <button
                    type="submit"
                    class="clinical-plan__save"
                >
                    Save clinical assessment
                </button>
            </div>
        </form>
    </div>
</section>