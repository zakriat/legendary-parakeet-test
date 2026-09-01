@php
    /*
     * Open Doctor Assessment after saving or validation failure.
     * Otherwise show Patient History first.
     */
    $activeClinicalTab =
        session('clinical_plan_success') ||
        $errors->any()
            ? 'doctor-assessment'
            : 'patient-history';
@endphp

<style>
    .encounter-clinical-tabs {
        background: #ffffff;
        border: 2px solid #222222;
        border-radius: 8px;
        color: #111111;
        margin-top: 24px;
    }

    .encounter-clinical-tabs,
    .encounter-clinical-tabs h1,
    .encounter-clinical-tabs h2,
    .encounter-clinical-tabs h3,
    .encounter-clinical-tabs p,
    .encounter-clinical-tabs label,
    .encounter-clinical-tabs small {
        color: #111111;
    }

    .encounter-clinical-tabs__header {
        border-bottom: 1px solid #555555;
        padding: 20px 20px 0;
    }

    .encounter-clinical-tabs__title {
        font-size: 22px;
        font-weight: 700;
        line-height: 1.3;
        margin: 0 0 5px;
    }

    .encounter-clinical-tabs__description {
        color: #222222;
        font-size: 15px;
        margin-bottom: 18px;
    }

    .encounter-clinical-tabs .nav-tabs {
        border-bottom: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .encounter-clinical-tabs .nav-link {
        background: #ffffff;
        border: 2px solid transparent;
        border-bottom: 4px solid transparent;
        border-radius: 5px 5px 0 0;
        color: #111111;
        cursor: pointer;
        font-size: 16px;
        font-weight: 700;
        min-height: 48px;
        padding: 10px 18px;
    }

    .encounter-clinical-tabs .nav-link:hover {
        background: #f2f2f2;
        border-color: #555555;
        color: #000000;
    }

    .encounter-clinical-tabs .nav-link.active {
        background: #ffffff;
        border-color: #111111;
        border-bottom: 5px solid #111111;
        color: #000000;
    }

    .encounter-clinical-tabs .nav-link:focus-visible {
        box-shadow: 0 0 0 4px rgba(0, 0, 0, 0.25);
        outline: 3px solid #000000;
        outline-offset: 2px;
    }

    .encounter-clinical-tabs__content {
        padding: 20px;
    }

    .encounter-clinical-tabs .tab-pane {
        color: #111111;
    }

    /*
     * The clinical-plan partial already has its own border.
     * Remove its outside margin when shown inside a tab.
     */
    .encounter-clinical-tabs .clinical-plan {
        margin-top: 0;
    }

    .encounter-tab-section {
        background: #ffffff;
        color: #111111;
    }

    .encounter-tab-heading {
        align-items: center;
        border-bottom: 2px solid #333333;
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 14px;
    }

    .encounter-tab-heading h2 {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .encounter-tab-heading p {
        margin: 0;
    }

    @media (max-width: 767px) {
        .encounter-clinical-tabs__header {
            padding: 15px 15px 0;
        }

        .encounter-clinical-tabs__content {
            padding: 15px;
        }

        .encounter-clinical-tabs .nav-tabs {
            display: block;
        }

        .encounter-clinical-tabs .nav-item {
            width: 100%;
        }

        .encounter-clinical-tabs .nav-link {
            border: 2px solid #444444;
            border-radius: 5px;
            margin-bottom: 7px;
            text-align: left;
            width: 100%;
        }

        .encounter-clinical-tabs .nav-link.active {
            border: 3px solid #000000;
        }
    }
</style>

<section
    class="encounter-clinical-tabs"
    aria-labelledby="clinical-record-heading"
>
    <header class="encounter-clinical-tabs__header">
        <h1
            id="clinical-record-heading"
            class="encounter-clinical-tabs__title"
        >
            Patient Clinical Record
        </h1>

        <p class="encounter-clinical-tabs__description">
            Review the patient's history, record the doctor's
            assessment and manage prescriptions.
        </p>

        <ul
            class="nav nav-tabs"
            id="clinical-record-tabs"
            role="tablist"
        >
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link {{ $activeClinicalTab === 'patient-history' ? 'active' : '' }}"
                    id="patient-history-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#patient-history-panel"
                    type="button"
                    role="tab"
                    aria-controls="patient-history-panel"
                    aria-selected="{{ $activeClinicalTab === 'patient-history' ? 'true' : 'false' }}"
                >
                    Patient History
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button
                    class="nav-link {{ $activeClinicalTab === 'doctor-assessment' ? 'active' : '' }}"
                    id="doctor-assessment-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#doctor-assessment-panel"
                    type="button"
                    role="tab"
                    aria-controls="doctor-assessment-panel"
                    aria-selected="{{ $activeClinicalTab === 'doctor-assessment' ? 'true' : 'false' }}"
                >
                    Doctor Assessment

                    @if($data->clinicalPlan)
                        <span class="visually-hidden">
                            Assessment recorded
                        </span>
                    @endif
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button
                    class="nav-link"
                    id="prescription-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#prescription-panel"
                    type="button"
                    role="tab"
                    aria-controls="prescription-panel"
                    aria-selected="false"
                >
                    Prescription
                </button>
            </li>
        </ul>
    </header>

    <div
        class="tab-content encounter-clinical-tabs__content"
        id="clinical-record-tab-content"
    >
        <div
            class="tab-pane fade {{ $activeClinicalTab === 'patient-history' ? 'show active' : '' }}"
            id="patient-history-panel"
            role="tabpanel"
            aria-labelledby="patient-history-tab"
            tabindex="0"
        >
            @include(
                'appointment::backend.patient_encounter.patient_clinical_history'
            )
        </div>

        <div
            class="tab-pane fade {{ $activeClinicalTab === 'doctor-assessment' ? 'show active' : '' }}"
            id="doctor-assessment-panel"
            role="tabpanel"
            aria-labelledby="doctor-assessment-tab"
            tabindex="0"
        >
            @include(
                'appointment::backend.patient_encounter.clinical_plan'
            )
        </div>

        <div
            class="tab-pane fade"
            id="prescription-panel"
            role="tabpanel"
            aria-labelledby="prescription-tab"
            tabindex="0"
        >
            @include(
                'appointment::backend.patient_encounter.prescription_tab'
            )
        </div>
    </div>
</section>