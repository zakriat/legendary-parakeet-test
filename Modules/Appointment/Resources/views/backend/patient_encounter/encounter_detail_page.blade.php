@extends('backend.layouts.app')

@section('title')
    {{ __($module_title) }}
@endsection

@section('content')
<div class="row">
    {{-- Left column --}}
    <div class="col-xxl-3 col-lg-4 col-md-5">
        <h4 class="mb-3">
            {{ __('appointment.encounter_detail') }}
        </h4>

        <div class="card">
            <div class="card-header">
                <h4 class="mb-1">
                    {{ __('appointment.about_clinic') }}
                </h4>
            </div>

            <div class="card-body">
                <ul class="list-inline m-0 p-0">
                    <li class="item mb-5 pb-5 border-bottom">
                        <div class="d-flex gap-3 align-items-center">
                            <img
                                src="{{ optional($data->clinic)->file_url }}"
                                alt="{{ optional($data->clinic)->name ?? 'Clinic' }}"
                                class="avatar avatar-64 rounded"
                            >

                            <div class="text-start">
                                <h5 class="m-0">
                                    {{ optional($data->clinic)->name ?? '--' }}
                                </h5>

                                <p class="mb-2">
                                    {{ optional($data->clinic)->email ?? '--' }}
                                </p>

                                <h5 class="m-0">
                                    Dr.
                                    {{ optional($data->doctor)->full_name ?? '--' }}
                                </h5>
                            </div>
                        </div>

                        @if($data->description)
                            <div class="mt-3">
                                {{ $data->description }}
                            </div>
                        @endif
                    </li>

                    <li class="item mb-1">
                        <h4 class="mb-3">
                            {{ __('appointment.about_patient') }}
                        </h4>

                        <div class="d-flex gap-3 align-items-center">
                            <img
                                src="{{ optional($data->user)->profile_image
                                    ?? default_user_avatar() }}"
                                alt="{{ optional($data->user)->full_name
                                    ?? default_user_name() }}"
                                class="avatar avatar-64 rounded-pill"
                            >

                            <div class="text-start">
                                <h5 class="m-0">
                                    {{ optional($data->user)->full_name
                                        ?? default_user_name() }}
                                </h5>

                                <p class="mb-0">
                                    {{ optional($data->user)->email ?? '--' }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="d-flex align-items-center justify-content-between flex-wrap gap-1 mt-4 mb-3"
                        >
                            <p class="mb-0">
                                {{ __('appointment.encounter_date') }}:
                            </p>

                            <span class="heading-color">
                                {{ formatDate($data->encounter_date) ?? '--' }}
                            </span>
                        </div>

                        <div
                            class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-3"
                        >
                            <p class="mb-0">
                                {{ __('service.title') }}:
                            </p>

                            <p class="mb-0 text-end">
                                {{ $data->appointmentdetail
                                    ?->clinicservice
                                    ?->systemservice
                                    ?->name ?? '--' }}
                            </p>
                        </div>

                        <div
                            class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-3"
                        >
                            <p class="mb-0">
                                {{ __('appointment.lbl_appitment_id') }}:
                            </p>

                            <p class="mb-0">
                                {{ $data->appointmentdetail?->id ?? '--' }}
                            </p>
                        </div>

                        @if(
                            !empty($data->user) &&
                            (
                                !empty($data->user->address) ||
                                !empty($data->user->cities?->name) ||
                                !empty($data->user->countries?->name) ||
                                !empty($data->user->pincode)
                            )
                        )
                            <div
                                class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3"
                            >
                                <p class="mb-0">
                                    {{ __('appointment.address') }}:
                                </p>

                                <div class="heading-color text-end encounter-address">
                                    @if($data->user->address)
                                        {{ $data->user->address }}
                                    @endif

                                    @if($data->user->cities?->name)
                                        <br>
                                        {{ $data->user->cities->name }}
                                    @endif

                                    @if($data->user->countries?->name)
                                        ,
                                        {{ $data->user->countries->name }}
                                    @endif

                                    @if($data->user->pincode)
                                        ,
                                        {{ $data->user->pincode }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </li>

                    <li class="item">
                        <div
                            class="d-flex align-items-center justify-content-between flex-wrap gap-1"
                        >
                            <p class="mb-0">
                                {{ __('appointment.encounter_status') }}:
                            </p>

                            @if($data->status == 1)
                                <span class="text-success">
                                    {{ __('appointment.open') }}
                                </span>
                            @else
                                <span class="text-danger">
                                    {{ __('appointment.close') }}
                                </span>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        @if($data['status'] == 1)
            <div class="card encounter-temeplate">
                <div class="card-body">
                    <h6>
                        {{ __('appointment.select_encounter_templates') }}
                    </h6>

                    <select
                        name="template_id"
                        id="template_id"
                        class="select2 form-select"
                        data-filter="select"
                    >
                        <option value="">
                            {{ __('clinic.lbl_select_template') }}
                        </option>

                        @foreach($template_data as $template)
                            <option value="{{ $template->id }}">
                                {{ $template->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        @include(
            'appointment::backend.patient_encounter.partials.appointment_clinical_summary',
            ['data' => $data]
        )
    </div>

    {{-- Right column --}}
    <div class="col-xxl-9 col-lg-8 col-md-7">
        <div
            class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3"
        >
            <h4 class="mb-0">
                {{ __('appointment.other_detail') }}
            </h4>

            @if($data['status'] == 1)
                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="closeEncounterDirectly({{ $data['id'] }})"
                >
                    <span class="d-inline-flex align-items-center gap-1">
                        <i
                            class="ph ph-plus"
                            aria-hidden="true"
                        ></i>

                        {{ __('appointment.close_encounter') }}
                        &amp;
                        {{ __('appointment.check_out') }}
                    </span>
                </button>
            @else
                <a
                    href="{{ url(
                        'app/billing-record/encounter_billing_detail'
                    ) }}?id={{ $data['id'] }}"
                    class="btn btn-primary"
                >
                    <i
                        class="ph ph-file-text me-1"
                        aria-hidden="true"
                    ></i>

                    {{ __('appointment.billing_details') }}
                </a>
            @endif
        </div>

        {{-- Main tab navigation --}}
        <nav
            class="encounter-tabs mb-4"
            aria-label="Encounter sections"
        >
            <div
                class="nav nav-tabs bg-transparent"
                id="nav-tab"
                role="tablist"
            >
                <button
                    class="nav-link active"
                    id="nav-home-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-home"
                    type="button"
                    role="tab"
                    aria-controls="nav-home"
                    aria-selected="true"
                >
                    {{ __('appointment.clinic_details') }}
                </button>

                @if(soap() == 1)
                    <button
                        class="nav-link"
                        id="nav-profile-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#nav-profile"
                        type="button"
                        role="tab"
                        aria-controls="nav-profile"
                        aria-selected="false"
                    >
                        {{ __('appointment.soap') }}
                    </button>
                @endif

                @if(bodychart() == 1)
                    <button
                        class="nav-link"
                        id="nav-contact-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#nav-contact"
                        type="button"
                        role="tab"
                        aria-controls="nav-contact"
                        aria-selected="false"
                    >
                        {{ __('appointment.body_chart') }}
                    </button>
                @endif

                <button
                    class="nav-link"
                    id="nav-patient-history-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-patient-history"
                    type="button"
                    role="tab"
                    aria-controls="nav-patient-history"
                    aria-selected="false"
                >
                    Patient History
                </button>

                <button
                    class="nav-link"
                    id="nav-doctor-assessment-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#nav-doctor-assessment"
                    type="button"
                    role="tab"
                    aria-controls="nav-doctor-assessment"
                    aria-selected="false"
                >
                    Doctor Assessment
                </button>

                @if(count($data['customform']) > 0)
                    <button
                        class="nav-link"
                        id="nav-custom-form-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#nav-custom-form"
                        type="button"
                        role="tab"
                        aria-controls="nav-custom-form"
                        aria-selected="false"
                    >
                        Custom Form
                    </button>
                @endif
            </div>
        </nav>

        {{-- Main tab content --}}
        <div class="card encounter-content-card">
            <div class="card-body">
                <div
                    class="tab-content"
                    id="nav-tabContent"
                >
                    {{-- Clinic Details --}}
                    <div
                        class="tab-pane fade show active"
                        id="nav-home"
                        role="tabpanel"
                        aria-labelledby="nav-home-tab"
                        tabindex="0"
                    >
                        <div class="row">
                            @if(
                                $encounter_data[
                                    'is_encounter_problem'
                                ] == 1
                            )
                                <div
                                    class="col-xl-4 col-lg-6"
                                    id="encounter_problem"
                                >
                                    @include(
                                        'appointment::backend.patient_encounter.component.encounter_problem',
                                        [
                                            'data' => $data,
                                            'problem_list' =>
                                                $problem_list,
                                        ]
                                    )
                                </div>
                            @endif

                            @if(
                                $encounter_data[
                                    'is_encounter_observation'
                                ] == 1
                            )
                                <div
                                    class="col-xl-4 col-lg-6"
                                    id="encounter_observation"
                                >
                                    @include(
                                        'appointment::backend.patient_encounter.component.encounter_observation',
                                        [
                                            'data' => $data,
                                            'observation_list' =>
                                                $observation_list,
                                        ]
                                    )
                                </div>
                            @endif

                            @if(
                                $encounter_data[
                                    'is_encounter_note'
                                ] == 1
                            )
                                <div
                                    class="col-xl-4"
                                    id="encounter_note"
                                >
                                    @include(
                                        'appointment::backend.patient_encounter.component.encounter_note',
                                        ['data' => $data]
                                    )
                                </div>
                            @endif
                        </div>

                        {{-- Medical records --}}
                        <section class="mb-4">
                            <div
                                class="card-header px-0 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-3"
                            >
                                <div>
                                    <h5 class="card-title mb-1">
                                        {{ __('appointment.medical_report') }}
                                    </h5>

                                    <p class="mb-0 text-muted">
                                        Upload medical records, scans,
                                        test results or reports.
                                    </p>
                                </div>

                                @if($data['status'] == 1)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addMedicalreport"
                                    >
                                        <span
                                            class="d-inline-flex align-items-center gap-1"
                                        >
                                            <i
                                                class="ph ph-plus"
                                                aria-hidden="true"
                                            ></i>

                                            {{ __('appointment.add_medical_report') }}
                                        </span>
                                    </button>
                                @endif
                            </div>

                            <div class="card-body bg-body p-1">
                                <div id="medical_report_table">
                                    @include(
                                        'appointment::backend.patient_encounter.component.medical_report_table',
                                        ['data' => $data]
                                    )
                                </div>
                            </div>
                        </section>

                        {{-- Existing prescription --}}
                        @if(prescription() == 1)
                            <section class="mb-4">
                                <div
                                    class="card-header px-0 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-3"
                                >
                                    <h5 class="card-title mb-0">
                                        {{ __('appointment.prescription') }}
                                    </h5>

                                    @if($data['status'] == 1)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addprescription"
                                        >
                                            <span
                                                class="d-inline-flex align-items-center gap-1"
                                            >
                                                <i
                                                    class="ph ph-plus"
                                                    aria-hidden="true"
                                                ></i>

                                                {{ __('appointment.add_prescription') }}
                                            </span>
                                        </button>
                                    @endif
                                </div>

                                <div class="card-body bg-body p-1">
                                    <div id="prescription_table">
                                        @include(
                                            'appointment::backend.patient_encounter.component.prescription_table',
                                            ['data' => $data]
                                        )
                                    </div>
                                </div>
                            </section>
                        @endif

                        {{-- Other information --}}
                        <section class="other-detail">
                            <div class="card-header px-0 mb-3">
                                <h6 class="card-title mb-0">
                                    {{ __('appointment.other_information') }}
                                </h6>
                            </div>

                            <textarea
                                class="form-control h-auto bg-body"
                                rows="3"
                                placeholder="{{ __(
                                    'appointment.enter_other_details'
                                ) }}"
                                name="other_details"
                                id="other_details"
                                @disabled(
                                    isset($data['status']) &&
                                    $data['status'] != 1
                                )
                            >{{ old(
                                'other_details',
                                $data[
                                    'EncounterOtherDetails'
                                ]['other_details'] ?? ''
                            ) }}</textarea>
                        </section>

                        @if($data['status'] == 1)
                            <div
                                class="offcanvas-footer border-top pt-4 mt-4"
                            >
                                <div
                                    class="d-grid d-sm-flex justify-content-sm-end gap-3"
                                >
                                    <button
                                        class="btn btn-secondary"
                                        type="button"
                                        id="save_button"
                                    >
                                        {{ __('messages.save') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- SOAP --}}
                    @if(soap() == 1)
                        <div
                            class="tab-pane fade"
                            id="nav-profile"
                            role="tabpanel"
                            aria-labelledby="nav-profile-tab"
                            tabindex="0"
                        >
                            <div id="soap">
                                @include(
                                    'appointment::backend.patient_encounter.component.soap',
                                    ['data' => $data]
                                )
                            </div>
                        </div>
                    @endif

                    {{-- Body Chart --}}
                    @if(bodychart() == 1)
                        <div
                            class="tab-pane fade"
                            id="nav-contact"
                            role="tabpanel"
                            aria-labelledby="nav-contact-tab"
                            tabindex="0"
                        >
                            <div id="body_chart_list">
                                @include(
                                    'appointment::backend.patient_encounter.component.body_chart_list',
                                    ['data' => $data]
                                )
                            </div>
                        </div>
                    @endif

                    {{-- Patient History --}}
                    <div
                        class="tab-pane fade"
                        id="nav-patient-history"
                        role="tabpanel"
                        aria-labelledby="nav-patient-history-tab"
                        tabindex="0"
                    >
                        @include(
                            'appointment::backend.patient_encounter.patient_clinical_history',
                            ['data' => $data]
                        )
                    </div>

                    {{-- Doctor Assessment --}}
                    <div
                        class="tab-pane fade"
                        id="nav-doctor-assessment"
                        role="tabpanel"
                        aria-labelledby="nav-doctor-assessment-tab"
                        tabindex="0"
                    >
                        @include(
                            'appointment::backend.patient_encounter.clinical_plan',
                            ['data' => $data]
                        )
                    </div>

                    {{-- Custom Form --}}
                    @if(count($data['customform']) > 0)
                        <div
                            class="tab-pane fade"
                            id="nav-custom-form"
                            role="tabpanel"
                            aria-labelledby="nav-custom-form-tab"
                            tabindex="0"
                        >
                            <div id="custom_form">
                                @include(
                                    'appointment::backend.patient_encounter.component.custom_form',
                                    [
                                        'data' =>
                                            $data['customform'],
                                        'encounter_id' =>
                                            $data['id'],
                                        'appointment_id' =>
                                            $data[
                                                'appointment_id'
                                            ],
                                    ]
                                )
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Existing modals --}}
    @include(
        'appointment::backend.patient_encounter.component.prescription',
        ['data' => $data]
    )

    @include(
        'appointment::backend.patient_encounter.component.medical_report',
        ['data' => $data]
    )
</div>
@endsection

@push('after-styles')
<style>
    .encounter-address {
        max-width: 70%;
    }

    /*
     * Main tab navigation
     */
    .encounter-tabs {
        background: #ffffff;
        border: 1px solid #dedede;
        border-radius: 8px;
        overflow-x: auto;
        padding: 6px;
        scrollbar-width: thin;
    }

    #nav-tab {
        border: 0;
        display: flex;
        flex-wrap: nowrap;
        gap: 4px;
        min-width: max-content;
    }

    #nav-tab .nav-link {
        background: transparent;
        border: 1px solid transparent;
        border-radius: 6px;
        color: #171717;
        flex: 0 0 auto;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.4;
        min-height: 42px;
        padding: 10px 15px;
        white-space: nowrap;
    }

    #nav-tab .nav-link:hover {
        background: #f3f3f3;
        border-color: #d1d1d1;
        color: #000000;
    }

    #nav-tab .nav-link.active {
        background: #ececec;
        border-color: #777777;
        color: #000000;
        font-weight: 700;
    }

    #nav-tab .nav-link:focus-visible {
        box-shadow: none;
        outline: 3px solid #111111;
        outline-offset: 2px;
    }

    /*
     * Main content
     */
    .encounter-content-card {
        border: 1px solid #dedede;
        border-radius: 8px;
        box-shadow: none;
        overflow: hidden;
    }

    .encounter-content-card > .card-body {
        padding: 20px;
    }

    #nav-tabContent > .tab-pane {
        color: #171717;
        min-height: 250px;
    }

    /*
     * Patient History
     */
    #nav-patient-history .patient-history {
        background: transparent;
        border: 0;
        color: #171717;
        margin: 0;
    }

    #nav-patient-history .patient-history__header {
        background: transparent;
        border-bottom: 1px solid #dedede;
        margin-bottom: 20px;
        padding: 0 0 16px;
    }

    #nav-patient-history .patient-history__grid {
        gap: 16px;
    }

    #nav-patient-history .patient-history__card {
        background: #f8f9fa;
        border: 1px solid #dedede;
        border-radius: 8px;
        box-shadow: none;
        padding: 18px;
    }

    #nav-patient-history .patient-history__warning {
        border: 2px solid #555555;
    }

    #nav-patient-history .patient-history__card h3 {
        border-bottom: 1px solid #d0d0d0;
        color: #171717;
        font-size: 16px;
        font-weight: 700;
        padding-bottom: 10px;
    }

    /*
     * Doctor Assessment
     */
    #nav-doctor-assessment .clinical-plan {
        background: transparent;
        border: 0;
        border-radius: 0;
        box-shadow: none;
        color: #171717;
        margin: 0;
    }

    #nav-doctor-assessment .clinical-plan__header {
        background: transparent;
        border-bottom: 1px solid #dedede;
        padding: 0 0 16px;
    }

    #nav-doctor-assessment .clinical-plan__body {
        padding: 20px 0 0;
    }

    #nav-doctor-assessment .clinical-plan__section {
        background: #f8f9fa;
        border: 1px solid #dedede;
        border-radius: 8px;
        box-shadow: none;
        padding: 18px;
    }

    #nav-doctor-assessment
        .clinical-plan__section-title {
        border-bottom: 1px solid #d0d0d0;
        color: #171717;
        font-size: 16px;
        font-weight: 700;
        padding-bottom: 10px;
    }

    #nav-doctor-assessment .form-label {
        color: #171717;
        font-size: 14px;
        font-weight: 600;
    }

    #nav-doctor-assessment .form-control,
    #nav-doctor-assessment .form-select {
        background: #ffffff;
        border: 1px solid #aaaaaa;
        border-radius: 6px;
        color: #171717;
        font-size: 14px;
        min-height: 42px;
    }

    #nav-doctor-assessment textarea.form-control {
        min-height: 105px;
    }

    #nav-doctor-assessment .form-control:focus,
    #nav-doctor-assessment .form-select:focus {
        border-color: #222222;
        box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.15);
    }

    #nav-doctor-assessment
        .clinical-plan__status {
        background: #eeeeee;
        border: 1px solid #777777;
        color: #171717;
    }

    #nav-doctor-assessment
        .clinical-plan__actions {
        border-top: 1px solid #dedede;
    }

    #nav-doctor-assessment
        .clinical-plan__save {
        background: var(--bs-primary, #dc5263);
        border: 1px solid var(--bs-primary, #dc5263);
        border-radius: 6px;
        color: #ffffff;
        min-height: 42px;
        padding: 9px 18px;
    }

    #nav-doctor-assessment
        .clinical-plan__save:hover {
        filter: brightness(0.9);
    }

    @media (max-width: 767px) {
        .encounter-address {
            max-width: 100%;
            width: 100%;
        }

        .encounter-content-card > .card-body {
            padding: 15px;
        }

        #nav-patient-history
            .patient-history__card,
        #nav-doctor-assessment
            .clinical-plan__section {
            padding: 14px;
        }
    }
</style>
@endpush

@push('after-scripts')
<script>
    function closeEncounterDirectly(encounterId) {
        confirmSwal(
            'Are you sure you want to close this encounter? ' +
            'This action cannot be undone.'
        ).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            const requestData = {
                encounter_id: encounterId,
                _token: '{{ csrf_token() }}'
            };

            fetch(
                '{{ route(
                    'backend.encounter.close-encounter-direct'
                ) }}',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN':
                            requestData._token
                    },
                    body: JSON.stringify(requestData)
                }
            )
            .then(function (response) {
                return response.json();
            })
            .then(function (responseData) {
                if (responseData.success) {
                    window.successSnackbar(
                        'Encounter closed successfully'
                    );

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);

                    return;
                }

                window.errorSnackbar(
                    responseData.message ||
                    'Something went wrong. Please check.'
                );
            })
            .catch(function (error) {
                console.error(error);

                window.errorSnackbar(
                    'Something went wrong. Please check.'
                );
            });
        });
    }

    document.addEventListener(
        'DOMContentLoaded',
        function () {
            /*
             * Save the existing encounter details.
             */
            const saveButton =
                document.getElementById('save_button');

            if (saveButton) {
                saveButton.addEventListener(
                    'click',
                    function () {
                        const templateElement =
                            document.getElementById(
                                'template_id'
                            );

                        const otherDetailsElement =
                            document.getElementById(
                                'other_details'
                            );

                        const requestData = {
                            encounter_id:
                                {{ (int) $data->id }},
                            template_id:
                                templateElement
                                    ? templateElement.value
                                    : '',
                            other_details:
                                otherDetailsElement
                                    ? otherDetailsElement.value
                                    : '',
                            user_id:
                                {{ (int) $data->user_id }},
                            _token:
                                '{{ csrf_token() }}'
                        };

                        fetch(
                            '{{ route(
                                'backend.encounter.save-encounter'
                            ) }}',
                            {
                                method: 'POST',
                                headers: {
                                    'Content-Type':
                                        'application/json',
                                    'X-CSRF-TOKEN':
                                        requestData._token
                                },
                                body: JSON.stringify(
                                    requestData
                                )
                            }
                        )
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error(
                                    'The encounter could not be saved.'
                                );
                            }

                            return response.json();
                        })
                        .then(function () {
                            window.successSnackbar(
                                'Encounter saved successfully'
                            );
                        })
                        .catch(function (error) {
                            console.error(error);

                            window.errorSnackbar(
                                error.message ||
                                'Something went wrong.'
                            );
                        });
                    }
                );
            }

            /*
             * Keep the selected tab in the URL hash.
             */
            const tabButtons =
                document.querySelectorAll(
                    '#nav-tab [data-bs-toggle="tab"]'
                );

            tabButtons.forEach(function (button) {
                button.addEventListener(
                    'shown.bs.tab',
                    function (event) {
                        const target =
                            event.target.getAttribute(
                                'data-bs-target'
                            );

                        if (target) {
                            history.replaceState(
                                null,
                                '',
                                target
                            );
                        }
                    }
                );
            });

            /*
             * Restore the selected tab after refresh/save.
             */
            const hash = window.location.hash;

            if (hash) {
                let matchingButton = null;

                tabButtons.forEach(function (button) {
                    if (
                        button.getAttribute(
                            'data-bs-target'
                        ) === hash
                    ) {
                        matchingButton = button;
                    }
                });

                if (
                    matchingButton &&
                    window.bootstrap &&
                    bootstrap.Tab
                ) {
                    bootstrap.Tab
                        .getOrCreateInstance(
                            matchingButton
                        )
                        .show();
                }
            }
        }
    );

    /*
     * Existing template behaviour.
     */
    $(document).ready(function () {
        const baseUrl = '{{ url('/') }}';

        $('#template_id').on(
            'change',
            function () {
                const templateId = $(this).val();

                if (!templateId) {
                    return;
                }

                const additionalData = {
                    user_id:
                        '{{ $data['user_id'] ?? '' }}',
                    encounter_id:
                        '{{ $data['id'] ?? '' }}',
                    status:
                        '{{ $data['status'] ?? '' }}'
                };

                $.ajax({
                    url:
                        baseUrl +
                        '/app/encounter/get-template-data/' +
                        templateId,
                    type: 'GET',
                    data: additionalData,

                    success: function (response) {
                        if (
                            response.is_encounter_problem
                        ) {
                            $('#encounter_problem').html(
                                response.problem_html
                            );
                        }

                        if (
                            response
                                .is_encounter_observation
                        ) {
                            $('#encounter_observation').html(
                                response.observation_html
                            );
                        }

                        if (
                            response.is_encounter_note
                        ) {
                            $('#encounter_note').html(
                                response.note_html
                            );
                        }

                        if (
                            response
                                .is_encounter_precreption
                        ) {
                            $('#prescription_table').html(
                                response.precreption_html
                            );
                        }

                        if (
                            response
                                .is_encounter_otherdetail
                        ) {
                            $('#other_details').val(
                                response.other_detail_html
                            );
                        }
                    },

                    error: function () {
                        console.error(
                            'Failed to load template data.'
                        );

                        window.errorSnackbar(
                            'The encounter template could not be loaded.'
                        );
                    }
                });
            }
        );
    });
</script>
@endpush