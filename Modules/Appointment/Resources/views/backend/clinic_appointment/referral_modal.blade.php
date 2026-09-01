<div
    class="modal fade"
    id="appointment-referral-modal"
    tabindex="-1"
    aria-labelledby="appointment-referral-title"
    aria-hidden="true"
>
    <div
        class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
    >
        <div class="modal-content text-dark">
            <form
                id="appointment-referral-form"
                novalidate
            >
                @csrf

                <div class="modal-header">
                    <div>
                        <h5
                            class="modal-title fw-bold text-dark"
                            id="appointment-referral-title"
                        >
                            Refer appointment
                        </h5>

                        <p class="mb-0 mt-1 text-dark">
                            Refer this patient to a CRM doctor
                            or an external healthcare professional.
                        </p>
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close referral form"
                    ></button>
                </div>

                <div class="modal-body">
                    <input
                        type="hidden"
                        id="referral-appointment-id"
                    >

                    <div
                        id="referral-form-errors"
                        class="alert alert-danger d-none"
                        role="alert"
                        aria-live="assertive"
                    ></div>

                    <div class="row g-3">
                        {{-- Referral type --}}
                        <div class="col-12">
                            <fieldset class="referral-fieldset">
                                <legend class="form-label fw-bold text-dark">
                                    Where is the patient being referred?
                                    <span
                                        class="text-danger"
                                        aria-hidden="true"
                                    >
                                        *
                                    </span>
                                </legend>

                                <div class="d-flex flex-wrap gap-4">
                                    <label class="form-check referral-type-option">
                                        <input
                                            class="form-check-input referral-type-input"
                                            type="radio"
                                            name="referral_type"
                                            value="external"
                                            checked
                                        >

                                        <span class="form-check-label text-dark">
                                            External doctor
                                        </span>
                                    </label>

                                    <label class="form-check referral-type-option">
                                        <input
                                            class="form-check-input referral-type-input"
                                            type="radio"
                                            name="referral_type"
                                            value="internal"
                                        >

                                        <span class="form-check-label text-dark">
                                            Doctor registered in CRM
                                        </span>
                                    </label>
                                </div>
                            </fieldset>
                        </div>

                        {{-- Internal receiving doctor --}}
                        <div
                            class="col-12 d-none"
                            id="internal-doctor-section"
                        >
                            <label
                                for="receiving_doctor_id"
                                class="form-label fw-bold text-dark"
                            >
                                Receiving CRM doctor
                                <span
                                    class="text-danger"
                                    aria-hidden="true"
                                >
                                    *
                                </span>
                            </label>

                            <select
                                class="form-select"
                                id="receiving_doctor_id"
                                name="receiving_doctor_id"
                                aria-describedby="receiving-doctor-help"
                            >
                                <option value="">
                                    Search or select a doctor
                                </option>
                            </select>

                            <small
                                class="form-text text-dark"
                                id="receiving-doctor-help"
                            >
                                Select a doctor who is registered
                                in the CRM.
                            </small>
                        </div>

                        {{-- External receiving doctor name --}}
                        <div
                            class="col-md-6"
                            id="receiving-doctor-name-section"
                        >
                            <label
                                for="receiving_doctor_name"
                                class="form-label fw-bold text-dark"
                            >
                                Receiving doctor’s name
                                <span
                                    class="text-danger"
                                    aria-hidden="true"
                                >
                                    *
                                </span>
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="receiving_doctor_name"
                                name="receiving_doctor_name"
                                maxlength="255"
                                autocomplete="name"
                                placeholder="Doctor’s full name"
                            >
                        </div>

                        {{-- Searchable specialty --}}
                        <div class="col-md-6">
                            <label
                                for="referral_specialty_id"
                                class="form-label fw-bold text-dark"
                            >
                                Speciality
                                <span
                                    class="text-danger"
                                    aria-hidden="true"
                                >
                                    *
                                </span>
                            </label>

                            <select
                                class="form-select"
                                id="referral_specialty_id"
                                name="referral_specialty_id"
                                required
                                aria-required="true"
                                aria-describedby="referral-specialty-help"
                            >
                                <option value="">
                                    Search or select a speciality
                                </option>
                            </select>

                            <small
                                class="form-text text-dark"
                                id="referral-specialty-help"
                            >
                                Type part of a specialty name,
                                such as cardiology, neurology or MRI.
                            </small>

                            {{--
                                Historical snapshot used by PDFs.
                                PHP must still obtain and verify the
                                specialty name from the database.
                            --}}
                            <input
                                type="hidden"
                                id="receiving_doctor_speciality"
                                name="receiving_doctor_speciality"
                            >
                        </div>

                        {{-- Organisation --}}
                        <div class="col-md-6">
                            <label
                                for="receiving_organisation_name"
                                class="form-label fw-bold text-dark"
                            >
                                Hospital or organisation
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="receiving_organisation_name"
                                name="receiving_organisation_name"
                                maxlength="255"
                                autocomplete="organization"
                                placeholder="Hospital, clinic or organisation"
                            >
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label
                                for="receiving_doctor_email"
                                class="form-label fw-bold text-dark"
                            >
                                Email
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="receiving_doctor_email"
                                name="receiving_doctor_email"
                                maxlength="255"
                                autocomplete="email"
                                placeholder="doctor@example.com"
                            >
                        </div>

                        {{-- Telephone --}}
                        <div class="col-md-6">
                            <label
                                for="receiving_doctor_phone"
                                class="form-label fw-bold text-dark"
                            >
                                Telephone
                            </label>

                            <input
                                type="tel"
                                class="form-control"
                                id="receiving_doctor_phone"
                                name="receiving_doctor_phone"
                                maxlength="40"
                                autocomplete="tel"
                                placeholder="Contact telephone number"
                            >
                            <input
    type="hidden"
    id="receiving_doctor_phone_country"
    name="receiving_doctor_phone_country"
>
                        </div>

                        {{-- Urgency --}}
                        <div class="col-md-6">
                            <label
                                for="urgency"
                                class="form-label fw-bold text-dark"
                            >
                                Urgency
                                <span
                                    class="text-danger"
                                    aria-hidden="true"
                                >
                                    *
                                </span>
                            </label>

                            <select
                                class="form-select"
                                id="urgency"
                                name="urgency"
                                required
                                aria-required="true"
                            >
                                <option value="routine">
                                    Routine
                                </option>

                                <option value="urgent">
                                    Urgent
                                </option>

                                <option value="emergency">
                                    Emergency
                                </option>
                            </select>
                        </div>

                        {{-- Address --}}
                        <div class="col-12">
                            <label
                                for="receiving_doctor_address"
                                class="form-label fw-bold text-dark"
                            >
                                Address
                            </label>

                            <textarea
                                class="form-control"
                                id="receiving_doctor_address"
                                name="receiving_doctor_address"
                                rows="2"
                                maxlength="2000"
                                autocomplete="street-address"
                                placeholder="Hospital or clinic address"
                            ></textarea>
                        </div>

                        {{-- Referral reason --}}
                        <div class="col-12">
                            <label
                                for="referral_reason"
                                class="form-label fw-bold text-dark"
                            >
                                Reason for referral
                                <span
                                    class="text-danger"
                                    aria-hidden="true"
                                >
                                    *
                                </span>
                            </label>

                            <textarea
                                class="form-control"
                                id="referral_reason"
                                name="referral_reason"
                                rows="3"
                                maxlength="5000"
                                required
                                aria-required="true"
                                placeholder="Explain why the patient is being referred"
                            ></textarea>
                        </div>

                        {{-- Clinical summary --}}
                        <div class="col-12">
                            <label
                                for="clinical_summary"
                                class="form-label fw-bold text-dark"
                            >
                                Clinical summary
                                <span
                                    class="text-danger"
                                    aria-hidden="true"
                                >
                                    *
                                </span>
                            </label>

                            <textarea
                                class="form-control"
                                id="clinical_summary"
                                name="clinical_summary"
                                rows="5"
                                maxlength="15000"
                                required
                                aria-required="true"
                                placeholder="Relevant symptoms, history, examination findings, tests and treatment"
                            ></textarea>
                        </div>

                        {{-- Diagnosis --}}
                        <div class="col-md-6">
                            <label
                                for="diagnosis"
                                class="form-label fw-bold text-dark"
                            >
                                Diagnosis
                            </label>

                            <textarea
                                class="form-control"
                                id="diagnosis"
                                name="diagnosis"
                                rows="3"
                                maxlength="5000"
                                placeholder="Provisional or confirmed diagnosis"
                            ></textarea>
                        </div>

                        {{-- Requested action --}}
                        <div class="col-md-6">
                            <label
                                for="requested_action"
                                class="form-label fw-bold text-dark"
                            >
                                Requested action
                            </label>

                            <textarea
                                class="form-control"
                                id="requested_action"
                                name="requested_action"
                                rows="3"
                                maxlength="255"
                                placeholder="Assessment, tests, treatment or advice requested"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <p class="referral-required-note me-auto mb-0">
                        <span
                            class="text-danger"
                            aria-hidden="true"
                        >
                            *
                        </span>
                        Required fields
                    </p>

                    <button
                        type="button"
                        class="btn btn-outline-dark"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn btn-dark"
                        id="save-appointment-referral"
                    >
                        <i
                            class="ph ph-floppy-disk me-1"
                            aria-hidden="true"
                        ></i>

                        Save referral
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /*
    |--------------------------------------------------------------------------
    | Referral modal layout
    |--------------------------------------------------------------------------
    */

    #appointment-referral-modal {
        overflow: hidden;
    }

    #appointment-referral-modal .modal-dialog {
        width: min(
            1140px,
            calc(100vw - 2rem)
        );

        height: calc(100vh - 2rem);
        max-height: calc(100vh - 2rem);
        margin: 1rem auto;
    }

    #appointment-referral-modal .modal-content {
        height: 100%;
        max-height: calc(100vh - 2rem);
        overflow: hidden;
        color: #111;
        background-color: #fff;
        border: 2px solid #444;
        border-radius: 0.5rem;
    }

    /*
     * The form sits between modal-content and
     * modal-body, so it also requires the flex
     * layout for scrolling to work.
     */
    #appointment-referral-form {
        display: flex;
        flex: 1 1 auto;
        flex-direction: column;
        width: 100%;
        height: 100%;
        min-height: 0;
        overflow: hidden;
    }

    #appointment-referral-modal .modal-header,
    #appointment-referral-modal .modal-footer {
        flex: 0 0 auto;
        background-color: #fff;
    }

    #appointment-referral-modal .modal-header {
        border-bottom: 1px solid #666;
    }

    #appointment-referral-modal .modal-footer {
        border-top: 1px solid #666;
    }

    /*
     * Only the middle form area scrolls.
     */
    #appointment-referral-modal .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        padding: 1.25rem;
        overflow-x: hidden;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-gutter: stable;
        -webkit-overflow-scrolling: touch;
    }

    /*
    |--------------------------------------------------------------------------
    | Readability and accessible form controls
    |--------------------------------------------------------------------------
    */

    #appointment-referral-modal label,
    #appointment-referral-modal legend,
    #appointment-referral-modal .modal-title,
    #appointment-referral-modal .form-label,
    #appointment-referral-modal .form-check-label,
    #appointment-referral-modal .form-text,
    #appointment-referral-modal .referral-required-note {
        color: #111;
    }

    #appointment-referral-modal .form-label,
    #appointment-referral-modal legend {
        margin-bottom: 0.4rem;
        font-size: 1rem;
        font-weight: 700;
    }

    #appointment-referral-modal .form-control,
    #appointment-referral-modal .form-select {
        min-height: 44px;
        color: #111;
        background-color: #fff;
        border: 1px solid #555;
        font-size: 1rem;
    }

    #appointment-referral-modal textarea.form-control {
        min-height: auto;
    }

    #appointment-referral-modal .form-control::placeholder {
        color: #555;
        opacity: 1;
    }

    #appointment-referral-modal .form-control:focus,
    #appointment-referral-modal .form-select:focus {
        color: #111;
        border-color: #111;
        box-shadow:
            0 0 0 0.18rem
            rgba(0, 0, 0, 0.2);
    }

    .referral-fieldset {
        padding: 1rem;
        background-color: #fafafa;
        border: 1px solid #777;
        border-radius: 0.4rem;
    }

    .referral-type-option {
        display: inline-flex;
        align-items: center;
        min-height: 42px;
        margin: 0;
        padding: 0.45rem 0.75rem 0.45rem 2.15rem;
        background-color: #fff;
        border: 1px solid #777;
        border-radius: 0.35rem;
        cursor: pointer;
    }

    .referral-type-option:focus-within {
        outline: 2px solid #111;
        outline-offset: 2px;
    }

    #referral-form-errors {
        color: #111;
        background-color: #fff;
        border: 2px solid #a00000;
        white-space: pre-line;
    }

    /*
    |--------------------------------------------------------------------------
    | Select2 specialty and doctor search
    |--------------------------------------------------------------------------
    */

    #appointment-referral-modal
    .select2-container {
        width: 100% !important;
    }

    #appointment-referral-modal
    .select2-container
    .select2-selection--single {
        display: flex;
        align-items: center;
        min-height: 44px;
        color: #111;
        background-color: #fff;
        border: 1px solid #555;
        border-radius: 0.375rem;
    }

    #appointment-referral-modal
    .select2-container
    .select2-selection--single
    .select2-selection__rendered {
        padding-right: 2.25rem;
        padding-left: 0.75rem;
        color: #111;
        line-height: normal;
    }

    #appointment-referral-modal
    .select2-container
    .select2-selection--single
    .select2-selection__arrow {
        top: 50%;
        right: 0.5rem;
        transform: translateY(-50%);
    }

    #appointment-referral-modal
    .select2-container--focus
    .select2-selection--single,
    #appointment-referral-modal
    .select2-container--open
    .select2-selection--single {
        border-color: #111;
        box-shadow:
            0 0 0 0.18rem
            rgba(0, 0, 0, 0.2);
    }

    /*
     * The dropdown is appended to the modal by
     * dropdownParent in the JavaScript.
     */
    #appointment-referral-modal
    .select2-dropdown {
        color: #111;
        background-color: #fff;
        border: 1px solid #555;
        z-index: 1090;
    }

    #appointment-referral-modal
    .select2-search__field {
        min-height: 42px;
        color: #111;
        background-color: #fff;
        border: 1px solid #555;
        font-size: 1rem;
    }

    #appointment-referral-modal
    .select2-results__option {
        padding: 0.65rem 0.75rem;
        color: #111;
        font-size: 1rem;
    }

    #appointment-referral-modal
    .select2-results__group {
        padding: 0.7rem 0.75rem 0.4rem;
        color: #111;
        background-color: #eee;
        font-weight: 700;
    }

    #appointment-referral-modal
    .select2-results__option--highlighted {
        color: #fff;
        background-color: #111;
    }

    /*
    |--------------------------------------------------------------------------
    | Mobile layout
    |--------------------------------------------------------------------------
    */

    @media (max-width: 767.98px) {
        #appointment-referral-modal
        .modal-dialog {
            width: calc(100vw - 1rem);
            height: calc(100vh - 1rem);
            max-height:
                calc(100vh - 1rem);
            margin: 0.5rem auto;
        }

        #appointment-referral-modal
        .modal-content {
            max-height:
                calc(100vh - 1rem);
        }

        #appointment-referral-modal
        .modal-body {
            padding: 1rem;
        }

        #appointment-referral-modal
        .modal-footer {
            align-items: stretch;
        }

        #appointment-referral-modal
        .modal-footer .btn {
            min-height: 44px;
        }

        #appointment-referral-modal
        .referral-required-note {
            width: 100%;
            margin-bottom: 0.5rem !important;
        }
    }
    /*
 * Always display search fields for referral
 * specialty and CRM-doctor Select2 dropdowns.
 */
#appointment-referral-modal
.select2-search--dropdown {
    display: block !important;
    padding: 0.65rem;
}

#appointment-referral-modal
.select2-search--dropdown
.select2-search__field {
    display: block !important;
    width: 100% !important;
    min-height: 44px;
    padding: 0.5rem 0.7rem;
    color: #111;
    background-color: #fff;
    border: 2px solid #555;
    font-size: 1rem;
}

#appointment-referral-modal
.select2-results {
    max-height: 360px;
    overflow-y: auto;
}

#appointment-referral-modal
.select2-results__options {
    max-height: 350px;
}

#appointment-referral-modal
.select2-container--open {
    z-index: 1095;
}
</style>

@push('after-styles')
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/css/intlTelInput.css"
>
<style>
    #appointment-referral-modal .iti {
        width: 100%;
    }

    #appointment-referral-modal .iti__tel-input {
        width: 100%;
        min-height: 44px;
    }
</style>
@endpush

@push('after-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/intlTelInput.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var phoneInput = document.getElementById('receiving_doctor_phone');
    var referralForm = document.getElementById('appointment-referral-form');
    var referralModal = document.getElementById('appointment-referral-modal');

    if (!phoneInput || !window.intlTelInput) {
        return;
    }

    var phoneIti = window.intlTelInput(phoneInput, {
        initialCountry: 'gb',
        separateDialCode: true,
        nationalMode: false,
        autoPlaceholder: 'aggressive',
        utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.1/js/utils.js'
    });

    if (referralModal) {
        referralModal.addEventListener('shown.bs.modal', function () {
            if (phoneInput.value) {
                phoneIti.setNumber(phoneInput.value);
            }
        });
    }

    /* Runs before the existing form submit code */
    if (referralForm) {
        referralForm.addEventListener('submit', function () {
            if (phoneInput.value.trim()) {
                phoneInput.value = phoneIti.getNumber();
            }
        }, true);
    }
});
</script>
@endpush
