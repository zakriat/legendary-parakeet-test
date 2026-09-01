    @extends('backend.layouts.app')

    @section('title')
        {{ __($module_title) }}
    @endsection

    @section('content')
        <div class="table-content mb-5">
            <x-backend.section-header>
                <div class="d-flex flex-wrap gap-3">
                    @if (auth()->user()->can('delete_clinic_appointment_list'))
                        <x-backend.quick-action url="{{ route('backend.appointments.bulk_action') }}">
                            <div class="">
                                <select name="action_type" class="select2 form-select col-12" id="quick-action-type"
                                    style="width:100%">
                                    <option value="">{{ __('messages.no_action') }}</option>

                                    @can('delete_clinic_appointment_list')
                                        <option value="delete">{{ __('messages.delete') }}</option>
                                    @endcan
                                </select>
                            </div>

                        </x-backend.quick-action>
                    @endif
                    <div>
                        <button type="button" class="btn btn-primary" data-modal="export">
                            <i class="ph ph-export me-1"></i>{{ __('messages.export') }}
                        </button>
                        {{-- <button type="button" class="btn btn-primary" data-modal="import">
                            <i class="ph ph-export me-1"></i>{{ __('messages.import') }}
                        </button> --}}
                    </div>
                </div>
                <x-slot name="toolbar">

                    <div>
                        <div class="datatable-filter status-filter">
                            <select name="column_status" id="column_status" class="select2 form-select" data-filter="select"
                                style="width: 100%">
                                <option value="">{{ __('messages.all') }}</option>
                                <option value="pending" {{ $filter['status'] == 'pending' ? 'selected' : '' }}>
                                    {{ __('appointment.pending') }}
                                </option>
                                <option value="confirmed" {{ $filter['status'] == 'confirmed' ? 'selected' : '' }}>
                                    {{ __('appointment.confirmed') }}
                                </option>
                                <option value="check_in" {{ $filter['status'] == 'check_in' ? 'selected' : '' }}>
                                    {{ __('appointment.check_in') }}
                                </option>
                                <option value="checkout" {{ $filter['status'] == 'checkout' ? 'selected' : '' }}>
                                    {{ __('appointment.checkout') }}
                                </option>
                                <option value="cancelled" {{ $filter['status'] == 'cancelled' ? 'selected' : '' }}>
                                    {{ __('appointment.cancelled') }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group flex-nowrap">
                        <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" class="form-control dt-search" placeholder="{{ __('messages.search') }}..."
                            aria-label="Search" aria-describedby="addon-wrapping">
                    </div>
                    <button class="btn btn-secondary d-flex align-items-center gap-1 btn-group" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasExample" aria-controls="offcanvasExample"><i
                            class="ph ph-funnel"></i>{{ __('messages.advance_filter') }}</button>

                    {{-- @hasPermission('add_clinic_appointment_list')
                        <x-buttons.offcanvas target='#form-offcanvas' title="{{ __('messages.create') }} {{ __($module_title) }}">
                            {{ __('messages.new') }} </x-buttons.offcanvas>
                    @endhasPermission --}}
                    
                    @hasPermission('add_clinic_appointment_list')
                        {{-- Button to open offcanvas --}}
                        <button type="button"
                            class="btn btn-primary"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#form-offcanvas"
                            aria-controls="form-offcanvas">
                            <i class="ph ph-plus-circle me-1"></i>{{ __('messages.new') }}
                        </button>

                        {{-- Offcanvas with form --}}
                        <div class="offcanvas offcanvas-end appointment-booking-offcanvas" tabindex="-1" id="form-offcanvas" aria-labelledby="formOffcanvasLabel">
                            <div class="offcanvas-header border-bottom">
                                <h6 class="m-0 h5" id="formOffcanvasLabel">
                                    {{ __('messages.create') }} {{ __($module_title) }}
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>

                            <div class="offcanvas-body">
                                {{-- Include your form directly --}}
                                @include('appointment::backend.clinic_appointment.new_appoitment', [
                                    'customers' => $customer,   // <- rename while passing
                                ])
                            </div>
                        </div>
                    @endhasPermission

                </x-slot>
            </x-backend.section-header>
            <table id="datatable" class="table position-relative">
            </table>
        </div>
        <div data-render="app">
            <clinic-appointment-offcanvas create-title="{{ __('messages.create') }} {{ __($module_title) }}"
                edit-title="{{ __('messages.edit') }} {{ __($module_title) }}"
                :customefield="{{ json_encode($customefield) }}" :role="{{ json_encode(auth()->user()->role) }}"
                :user-id="{{ auth()->user()->id }}">
            </clinic-appointment-offcanvas>

            <patient-encounter-dashboard create-title="{{ __('appointment.encouter_dashboard') }}">
            </patient-encounter-dashboard>
            <appointment-offcanvas>
            </appointment-offcanvas>

            <appointment-customform>
            </appointment-customform>
        </div>

        <x-backend.advance-filter>
            <x-slot name="title">
                <h4>{{ __('service.lbl_advanced_filter') }}</h4>
            </x-slot>
            <div class="form-group datatable-filter">
                <label class="form-label" for="patient_name">{{ __('clinic.patient') }}</label>
                <select name="patient_name" id="patient_name" class="select2 form-select" data-filter="select">
                    <option value="">{{ __('messages.select_patient') }}</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                            @if($patient->otherPatients->count() > 0)
                                ({{ $patient->otherPatients->count() }} {{ __('appointment.other_patients') }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group datatable-filter d-none" id="other_patient_div">
                <label class="form-label" for="other_patient">{{ __('messages.other_patient') }}</label>
                <select name="other_patient" id="other_patient" class="select2 form-select" data-filter="select">
                    <option value="">{{ __('messages.select_other_patient') }}</option>
                </select>
            </div>
            <div class="form-group datatable-filter">
                <label class="form-label" for="service_name">{{ __('service.singular_title') }}</label>
                <select name="service_name" id="service_name" class="select2 form-select" data-filter="select">
                    <option value="">{{ __('service.all') }} {{ __('service.singular_title') }}</option>
                    @foreach ($service as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="datatable-filter">
                <label class="form-label" for="other_patient">{{ __('messages.status') }}</label>
                <select name="Status" id="appointment_status" class="select2 form-select" data-filter="select">
                    <option value="">{{ __('messages.all') }}</option>
                    <option value="pending">{{ __('appointment.pending') }}</option>
                    <option value="confirmed">{{ __('appointment.confirmed') }}</option>
                    <option value="check_in">{{ __('appointment.check_in') }}</option>
                    <option value="checkout">{{ __('appointment.checkout') }}</option>
                    <option value="cancelled">{{ __('appointment.cancelled') }}</option>
                </select>
            </div>
            
        
            @unless (auth()->user()->hasRole('doctor'))
                <div class="form-group datatable-filter">
                    <label class="form-label" for="doctor_id">{{ __('clinic.doctor_title') }}</label>
                    <select name="doctor_id" id="doctor_id" class="select2 form-select" data-filter="select">
                        <option value="">{{ __('service.lbl_doctor') }} </option>
                        @foreach ($doctor as $doctor)
                            <option value="{{ $doctor->doctor_id }}">{{ optional($doctor->user)->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endunless
            <button type="reset" class="btn btn-danger" id="reset-filter">{{ __('appointment.reset') }}</button>
        </x-backend.advance-filter>
        
        <!-- Include Appointment Details Modal -->
        @include('appointment::backend.appointment.details_modal')
        @include(
    'appointment::backend.clinic_appointment.referral_modal'
)
    @endsection

    @push('after-styles')
        <link rel="stylesheet" href="{{ mix('modules/appointment/style.css') }}">
        <!-- DataTables Core and Extensions -->
        <link rel="stylesheet" href="{{ asset('vendor/datatable/datatables.min.css') }}">
    @endpush
    <style>
        .disabled-cell {
            background-color: #e9ecef;
            pointer-events: none;
            opacity: 0.5;
        }

        /*
        |--------------------------------------------------------------------------
        | Compact appointment actions
        |--------------------------------------------------------------------------
        */

        .appointment-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: nowrap;
            gap: 0.5rem;
            min-width: max-content;
        }

        .appointment-actions > a,
        .appointment-actions > button,
        .appointment-actions > span,
        .appointment-actions > form,
        .appointment-actions > .appointment-more-actions {
            flex: 0 0 auto;
        }

        .appointment-more-actions {
            position: relative;
        }

        .appointment-more-actions > .dropdown-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            min-width: 38px;
            height: 36px;
            padding: 0;
            color: #111;
            background-color: #fff;
            border: 1px solid #777;
            border-radius: 0.35rem;
        }

        .appointment-more-actions > .dropdown-toggle:hover,
        .appointment-more-actions > .dropdown-toggle:focus,
        .appointment-more-actions > .dropdown-toggle[aria-expanded="true"] {
            color: #fff;
            background-color: #111;
            border-color: #111;
        }

        .appointment-more-actions > .dropdown-toggle::after {
            display: none;
        }

        .appointment-more-actions .dropdown-menu {
            min-width: 225px;
            max-width: 300px;
            padding: 0.4rem;
            color: #111;
            background-color: #fff;
            border: 1px solid #666;
            border-radius: 0.4rem;
            box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.2);
            z-index: 1080;
        }

        .appointment-more-actions .appointment-dropdown-item {
            display: flex;
            align-items: center;
            width: 100%;
            min-height: 42px;
            padding: 0.4rem 0.5rem;
            color: #111;
            background-color: #fff;
            border-radius: 0.3rem;
        }

        .appointment-more-actions .appointment-dropdown-item:hover,
        .appointment-more-actions .appointment-dropdown-item:focus-within {
            background-color: #eee;
        }

        .appointment-more-actions .appointment-dropdown-item > a,
        .appointment-more-actions .appointment-dropdown-item > button,
        .appointment-more-actions .appointment-dropdown-item > span {
            flex: 0 0 auto;
            margin: 0;
        }

        .appointment-more-actions .appointment-dropdown-item > form {
            flex: 0 0 auto;
            margin: 0;
        }

        .appointment-more-actions .appointment-dropdown-label {
            flex: 1 1 auto;
            margin-left: 0.6rem;
            color: #111;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.25;
            white-space: normal;
            cursor: pointer;
        }

        /*
        * Keep dropdowns visible beyond the action cell.
        */
        #datatable td:last-child {
            overflow: visible;
            white-space: nowrap;
        }

        #datatable .dropdown-menu.show {
            display: block;
        }
        
        #appointment-referral-modal .iti {
    width: 100%;
}

#appointment-referral-modal .iti__tel-input {
    width: 100%;
    min-height: 44px;
}

        @media (max-width: 767.98px) {
            .appointment-actions {
                gap: 0.35rem;
            }

            .appointment-more-actions .dropdown-menu {
                min-width: 200px;
            }
        }
    </style>
    @push('after-scripts')
        <script src="{{ mix('modules/appointment/script.js') }}"></script>
        <script src="{{ asset('js/form-offcanvas/index.js') }}" defer></script>
        <script src="{{ asset('js/form-modal/index.js') }}" defer></script>


        <script>
            let referralStatusSelect = null
            let referralPreviousStatus = null
            let referralDoctorsLoaded = false
            let referralSpecialtiesLoaded = false
            let referralWasSaved = false
            let referralModalIsOpening = false

            const referralRoutes = {
                doctors: @json(
                    route(
                        'backend.appointments.referral.doctors'
                    )
                ),

                showTemplate: @json(
                    route(
                        'backend.appointments.referral.show',
                        ['appointment' => '__APPOINTMENT__']
                    )
                ),

                storeTemplate: @json(
                    route(
                        'backend.appointments.referral.store',
                        ['appointment' => '__APPOINTMENT__']
                    )
                ),
            }

            function referralUrl(
                template,
                appointmentId
            ) {
                return template.replace(
                    '__APPOINTMENT__',
                    String(appointmentId)
                )
            }

            function getReferralModalElement() {
                return document.getElementById(
                    'appointment-referral-modal'
                )
            }

            function getReferralModal() {
                const element =
                    getReferralModalElement()

                if (!element) {
                    return null
                }

                return bootstrap.Modal.getOrCreateInstance(
                    element,
                    {
                        backdrop: 'static',
                        keyboard: true,
                        focus: true,
                    }
                )
            }

            function reloadAppointmentTable() {
                if (
                    window.renderedDataTable &&
                    window.renderedDataTable.ajax
                ) {
                    window.renderedDataTable.ajax.reload(
                        null,
                        false
                    )

                    return
                }

                if (
                    window.jQuery &&
                    $.fn.DataTable.isDataTable(
                        '#datatable'
                    )
                ) {
                    $('#datatable')
                        .DataTable()
                        .ajax.reload(null, false)

                    return
                }

                window.location.reload()
            }

            function selectedReferralType() {
                return document.querySelector(
                    'input[name="referral_type"]:checked'
                )?.value || 'external'
            }

            function updateReferralTypeInterface() {
                const internal =
                    selectedReferralType() === 'internal'

                const internalSection =
                    document.getElementById(
                        'internal-doctor-section'
                    )

                const externalNameSection =
                    document.getElementById(
                        'receiving-doctor-name-section'
                    )

                const internalDoctorInput =
                    document.getElementById(
                        'receiving_doctor_id'
                    )

                const externalNameInput =
                    document.getElementById(
                        'receiving_doctor_name'
                    )

                internalSection?.classList.toggle(
                    'd-none',
                    !internal
                )

                externalNameSection?.classList.toggle(
                    'd-none',
                    internal
                )

                if (internalDoctorInput) {
                    internalDoctorInput.required =
                        internal

                    if (!internal) {
                        internalDoctorInput.value = ''
                    }
                }

                if (externalNameInput) {
                    externalNameInput.required =
                        !internal
                }
            }

            function displayReferralErrors(messages) {
                const errorBox =
                    document.getElementById(
                        'referral-form-errors'
                    )

                if (!errorBox) {
                    return
                }

                const messageList =
                    Array.isArray(messages)
                        ? messages
                        : [messages]

                errorBox.textContent =
                    messageList
                        .filter(Boolean)
                        .join('\n')

                errorBox.classList.remove('d-none')

                errorBox.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                })
            }

            function clearReferralErrors() {
                const errorBox =
                    document.getElementById(
                        'referral-form-errors'
                    )

                if (!errorBox) {
                    return
                }

                errorBox.textContent = ''
                errorBox.classList.add('d-none')
            }

            // async function loadReferralDoctors() {
            //     if (referralDoctorsLoaded) {
            //         return
            //     }

            //     const response = await fetch(
            //         referralRoutes.doctors,
            //         {
            //             method: 'GET',
            //             headers: {
            //                 Accept: 'application/json',
            //                 'X-Requested-With':
            //                     'XMLHttpRequest',
            //             },
            //         }
            //     )

            //     if (!response.ok) {
            //         throw new Error(
            //             'CRM doctors could not be loaded.'
            //         )
            //     }

            //     const result = await response.json()

            //     const select =
            //         document.getElementById(
            //             'receiving_doctor_id'
            //         )

            //     if (!select) {
            //         throw new Error(
            //             'Receiving-doctor field was not found.'
            //         )
            //     }

            //     /*
            //     * Retain only the placeholder before
            //     * inserting the doctors.
            //     */
            //     select
            //         .querySelectorAll(
            //             'option:not(:first-child)'
            //         )
            //         .forEach(option => option.remove())

            //     ;(result.doctors || [])
            //         .forEach(doctor => {
            //             const option =
            //                 document.createElement(
            //                     'option'
            //                 )

            //             option.value = doctor.id

            //             option.textContent =
            //                 doctor.name +
            //                 (
            //                     doctor.email
            //                         ? ` - ${doctor.email}`
            //                         : ''
            //                 )

            //             option.dataset.name =
            //                 doctor.name || ''

            //             option.dataset.email =
            //                 doctor.email || ''

            //             option.dataset.phone =
            //                 doctor.phone || ''

            //             select.appendChild(option)
            //         })

            //     referralDoctorsLoaded = true
            // }

            function destroyReferralSelect2(
    selectElement
) {
    if (
        !window.jQuery ||
        !$.fn.select2 ||
        !selectElement
    ) {
        return
    }

    const select =
        $(selectElement)

    if (
        select.hasClass(
            'select2-hidden-accessible'
        )
    ) {
        select.select2('destroy')
    }
}

function resetReferralSelect(
    selectElement,
    placeholder
) {
    if (!selectElement) {
        return
    }

    destroyReferralSelect2(
        selectElement
    )

    selectElement.innerHTML = ''

    const placeholderOption =
        document.createElement('option')

    placeholderOption.value = ''
    placeholderOption.textContent =
        placeholder

    selectElement.appendChild(
        placeholderOption
    )
}

function initializeReceivingDoctorSearch() {
    const element =
        document.getElementById(
            'receiving_doctor_id'
        )

    if (!element) {
        return
    }

    if (
        !window.jQuery ||
        !$.fn.select2
    ) {
        console.warn(
            'Select2 is not available for the doctor dropdown.'
        )

        return
    }

    destroyReferralSelect2(element)

    $(element).select2({
        width: '100%',
        placeholder:
            'Search or select a doctor',
        allowClear: true,

        /*
         * Always show the search input.
         */
        minimumResultsForSearch: 0,

        /*
         * Prevent the dropdown appearing behind
         * the Bootstrap modal.
         */
        dropdownParent:
            $('#appointment-referral-modal'),
    })
}

function initializeReferralSpecialtySearch() {
    const element =
        document.getElementById(
            'referral_specialty_id'
        )

    if (!element) {
        return
    }

    if (
        !window.jQuery ||
        !$.fn.select2
    ) {
        console.warn(
            'Select2 is not available for the specialty dropdown.'
        )

        return
    }

    destroyReferralSelect2(element)

    $(element).select2({
        width: '100%',
        placeholder:
            'Search or select a speciality',
        allowClear: true,

        /*
         * Force Select2 to show its search field.
         */
        minimumResultsForSearch: 0,

        dropdownParent:
            $('#appointment-referral-modal'),

        /*
         * Search specialties inside their category
         * optgroups.
         */
        matcher: function (
            parameters,
            data
        ) {
            const searchTerm =
                String(
                    parameters.term || ''
                )
                    .trim()
                    .toLowerCase()

            if (!searchTerm) {
                return data
            }

            /*
             * Optgroup records contain children.
             */
            if (
                Array.isArray(
                    data.children
                )
            ) {
                const matchingChildren =
                    data.children.filter(
                        child => {
                            return String(
                                child.text || ''
                            )
                                .toLowerCase()
                                .includes(
                                    searchTerm
                                )
                        }
                    )

                if (
                    matchingChildren.length ===
                    0
                ) {
                    return null
                }

                return {
                    ...data,
                    children:
                        matchingChildren,
                }
            }

            const optionText =
                String(data.text || '')
                    .toLowerCase()

            return optionText.includes(
                searchTerm
            )
                ? data
                : null
        },
    })
}

function populateReferralDoctors(
    doctors
) {
    const select =
        document.getElementById(
            'receiving_doctor_id'
        )

    if (!select) {
        throw new Error(
            'Receiving CRM doctor field was not found.'
        )
    }

    resetReferralSelect(
        select,
        'Search or select a doctor'
    )

    doctors.forEach(doctor => {
        const option =
            document.createElement(
                'option'
            )

        option.value =
            String(doctor.id)

        option.textContent =
            doctor.name +
            (
                doctor.email
                    ? ` - ${doctor.email}`
                    : ''
            )

        option.dataset.name =
            doctor.name || ''

        option.dataset.email =
            doctor.email || ''

        option.dataset.phone =
            doctor.phone || ''

        option.dataset.gmcNumber =
            doctor.gmc_number || ''

        select.appendChild(option)
    })

    referralDoctorsLoaded = true

    initializeReceivingDoctorSearch()

    console.log(
        'CRM doctors loaded:',
        doctors.length
    )
}

function populateReferralSpecialties(specialtyGroups) {
    const select = document.getElementById(
        'referral_specialty_id'
    );

    if (!select) {
        throw new Error(
            'Referral specialty field was not found.'
        );
    }

    resetReferralSelect(
        select,
        'Search or select a speciality'
    );

    specialtyGroups.forEach(function (group) {
        const specialties = Array.isArray(group.items)
            ? group.items
            : [];

        specialties.forEach(function (specialty) {
            const option = document.createElement('option');

            option.value = String(specialty.id);
            option.textContent = specialty.name;
            option.dataset.specialtyName = specialty.name;

            select.appendChild(option);
        });
    });

    referralSpecialtiesLoaded = true;

    initializeReferralSpecialtySearch();

    console.log(
        'Referral specialties loaded:',
        select.querySelectorAll('option:not([value=""])').length
    );
}

async function loadReferralDoctors() {
    /*
     * Do not return until both collections
     * have been loaded.
     */
    if (
        referralDoctorsLoaded &&
        referralSpecialtiesLoaded
    ) {
        return
    }

    const response = await fetch(
        referralRoutes.doctors,
        {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                Accept:
                    'application/json',

                'X-Requested-With':
                    'XMLHttpRequest',
            },
        }
    )

    const responseText =
        await response.text()

    let result

    try {
        result = JSON.parse(
            responseText
        )
    } catch (error) {
        console.error(
            'Invalid referral-options response:',
            responseText
        )

        throw new Error(
            `Referral options returned invalid JSON (${response.status}).`
        )
    }

    if (!response.ok) {
        console.error(
            'Referral-options error:',
            result
        )

        throw new Error(
            result.message ||
            `Referral options could not be loaded (${response.status}).`
        )
    }

    const doctors =
        Array.isArray(
            result.doctors
        )
            ? result.doctors
            : []

    const specialties =
        Array.isArray(
            result.specialties
        )
            ? result.specialties
            : []

    console.log(
        'Referral options received:',
        {
            doctors:
                doctors.length,

            specialtyGroups:
                specialties.length,

            specialties:
                specialties.reduce(
                    (
                        total,
                        group
                    ) => {
                        return total +
                            (
                                Array.isArray(
                                    group.items
                                )
                                    ? group
                                        .items
                                        .length
                                    : 0
                            )
                    },
                    0
                ),
        }
    )

    populateReferralDoctors(
        doctors
    )

    populateReferralSpecialties(
        specialties
    )
}
            function clearReferralForm() {
                const form =
                    document.getElementById(
                        'appointment-referral-form'
                    )

                if (!form) {
                    return
                }
form.reset()
clearReferralErrors()

/*
 * Clear Select2 selections while retaining
 * the loaded doctor and specialty options.
 */
const doctorSelect =
    $('#receiving_doctor_id')

if (
    doctorSelect.hasClass(
        'select2-hidden-accessible'
    )
) {
    doctorSelect
        .val(null)
        .trigger('change.select2')
}

const specialtySelect =
    $('#referral_specialty_id')

if (
    specialtySelect.hasClass(
        'select2-hidden-accessible'
    )
) {
    specialtySelect
        .val(null)
        .trigger('change')
}

const specialtySnapshot =
    document.getElementById(
        'receiving_doctor_speciality'
    )

if (specialtySnapshot) {
    specialtySnapshot.value = ''
}

                const externalRadio =
                    form.querySelector(
                        'input[name="referral_type"]' +
                        '[value="external"]'
                    )

                if (externalRadio) {
                    externalRadio.checked = true
                }

                updateReferralTypeInterface()

                /*
                * Return the scrollable body to the top.
                */
                form.querySelector(
                    '.modal-body'
                )?.scrollTo({
                    top: 0,
                    behavior: 'instant',
                })
            }

            function fillReferralForm(referral) {
                if (!referral) {
                    return
                }

                const type =
                    referral.referral_type ||
                    'external'

                const typeRadio =
                    document.querySelector(
                        'input[name="referral_type"]' +
                        `[value="${type}"]`
                    )

                if (typeRadio) {
                    typeRadio.checked = true
                }

                const fields = [
                    'receiving_doctor_id',
                    'referral_specialty_id',
                    'receiving_doctor_name',
                    'receiving_doctor_speciality',

                    'receiving_organisation_name',
                    'receiving_doctor_email',
                    'receiving_doctor_phone',
                    'receiving_doctor_address',
                    'referral_reason',
                    'clinical_summary',
                    'diagnosis',
                    'requested_action',
                    'urgency',
                ]

                fields.forEach(field => {
                    const input =
                        document.getElementById(field)

                    if (input) {
                        input.value =
                            referral[field] ?? ''
                    }
                })

                /*
 * Select2 needs an explicit change event to
 * display loaded saved values.
 */
if (
    referral.receiving_doctor_id
) {
    $('#receiving_doctor_id')
        .val(
            String(
                referral
                    .receiving_doctor_id
            )
        )
        .trigger('change')
}

if (
    referral.referral_specialty_id
) {
    $('#referral_specialty_id')
        .val(
            String(
                referral
                    .referral_specialty_id
            )
        )
        .trigger('change')
}

                updateReferralTypeInterface()
            }

            window.openReferralModal =
                async function (
                    appointmentId,
                    statusSelect = null,
                    previousStatus = null
                ) {
                    const modalElement =
                        getReferralModalElement()

                    if (!modalElement) {
                        console.error(
                            'The appointment referral modal was not found.'
                        )

                        return
                    }

                    /*
                    * Prevent Select2 and native change events
                    * opening the modal twice.
                    */
                    if (
                        referralModalIsOpening ||
                        modalElement.classList.contains(
                            'show'
                        )
                    ) {
                        return
                    }

                    referralModalIsOpening = true
                    referralWasSaved = false
                    referralStatusSelect =
                        statusSelect || null

                    referralPreviousStatus =
                        previousStatus ?? null

                    clearReferralForm()

                    const appointmentInput =
                        document.getElementById(
                            'referral-appointment-id'
                        )

                    if (appointmentInput) {
                        appointmentInput.value =
                            appointmentId
                    }

                    /*
                    * Show the modal immediately. Data can load
                    * while the user sees the form.
                    */
                    const modal = getReferralModal()
                    modal?.show()

                    try {
                        await loadReferralDoctors()

                        const response = await fetch(
                            referralUrl(
                                referralRoutes.showTemplate,
                                appointmentId
                            ),
                            {
                                method: 'GET',
                                headers: {
                                    Accept:
                                        'application/json',
                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },
                            }
                        )

                        if (!response.ok) {
                            throw new Error(
                                'Existing referral details could not be loaded.'
                            )
                        }

                        const result =
                            await response.json()

                        /*
                        * Existing referred appointments are
                        * populated for editing.
                        */
                        fillReferralForm(
                            result.referral
                        )
                    } catch (error) {
                        console.error(error)

                        /*
                        * Failing to find an existing referral is
                        * harmless for a new referral. Only show
                        * errors other than an ordinary empty result.
                        */
                        if (
                            !String(error.message)
                                .includes(
                                    'Existing referral'
                                )
                        ) {
                            displayReferralErrors(
                                error.message
                            )
                        }
                    } finally {
                        referralModalIsOpening = false
                    }
                }

            document
                .querySelectorAll(
                    '.referral-type-input'
                )
                .forEach(input => {
                    input.addEventListener(
                        'change',
                        updateReferralTypeInterface
                    )
                })

            document
                .getElementById(
                    'receiving_doctor_id'
                )
                ?.addEventListener(
                    'change',
                    function () {
                        const option =
                            this.options[
                                this.selectedIndex
                            ]

                        if (!option?.value) {
                            return
                        }

                        const nameInput =
                            document.getElementById(
                                'receiving_doctor_name'
                            )

                        const emailInput =
                            document.getElementById(
                                'receiving_doctor_email'
                            )

                        const phoneInput =
                            document.getElementById(
                                'receiving_doctor_phone'
                            )

                        if (nameInput) {
                            nameInput.value =
                                option.dataset.name || ''
                        }

                        if (emailInput) {
                            emailInput.value =
                                option.dataset.email || ''
                        }

                        if (phoneInput) {
                            phoneInput.value =
                                option.dataset.phone || ''
                        }
                    }
                )

            document
                .getElementById(
                    'appointment-referral-form'
                )
                ?.addEventListener(
                    'submit',
                    async function (event) {
                        event.preventDefault()
                        clearReferralErrors()

                        const appointmentId =
                            document.getElementById(
                                'referral-appointment-id'
                            )?.value

                        const button =
                            document.getElementById(
                                'save-appointment-referral'
                            )

                        if (
                            !appointmentId ||
                            !button
                        ) {
                            displayReferralErrors(
                                'The appointment could not be identified.'
                            )

                            return
                        }

                        button.disabled = true

                        const originalButtonText =
                            button.textContent

                        button.textContent =
                            'Saving referral...'

                        try {
                            const response = await fetch(
                                referralUrl(
                                    referralRoutes
                                        .storeTemplate,
                                    appointmentId
                                ),
                                {
                                    method: 'POST',
                                    headers: {
                                        Accept:
                                            'application/json',

                                        'X-Requested-With':
                                            'XMLHttpRequest',

                                        'X-CSRF-TOKEN':
                                            document
                                                .querySelector(
                                                    'meta[name="csrf-token"]'
                                                )
                                                ?.getAttribute(
                                                    'content'
                                                ) || '',
                                    },

                                    body: new FormData(
                                        this
                                    ),
                                }
                            )

                            const result =
                                await response.json()

                            if (!response.ok) {
                                const messages =
                                    result.errors
                                        ? Object.values(
                                            result.errors
                                        ).flat()
                                        : [
                                            result.message ||
                                            'The referral could not be saved.',
                                        ]

                                displayReferralErrors(
                                    messages
                                )

                                return
                            }

                            referralWasSaved = true

                            /*
                            * Clear these before closing. Otherwise
                            * the hidden handler restores the old
                            * appointment status.
                            */
                            referralStatusSelect = null
                            referralPreviousStatus = null

                            if (
                                window.successSnackbar
                            ) {
                                window.successSnackbar(
                                    result.message ||
                                    'Referral saved successfully.'
                                )
                            }

                            getReferralModal()?.hide()
                        } catch (error) {
                            console.error(error)

                            displayReferralErrors(
                                'The referral could not be saved. Please try again.'
                            )
                        } finally {
                            button.disabled = false
                            button.textContent =
                                originalButtonText
                        }
                    }
                )

            getReferralModalElement()
                ?.addEventListener(
                    'hidden.bs.modal',
                    function () {
                        referralModalIsOpening = false

                        if (referralWasSaved) {
                            referralWasSaved = false
                            referralStatusSelect = null
                            referralPreviousStatus =
                                null

                            /*
                            * Reload after the closing animation,
                            * retaining the current DataTable page.
                            */
                            reloadAppointmentTable()

                            return
                        }

                        /*
                        * Modal was cancelled. Restore the status
                        * displayed before Referred was selected.
                        */
                        if (
                            referralStatusSelect &&
                            referralPreviousStatus !==
                                null &&
                            referralPreviousStatus !==
                                undefined
                        ) {
                            $(referralStatusSelect)
                                .val(
                                    referralPreviousStatus
                                )
                                .trigger(
                                    'change.select2'
                                )
                        }

                        referralStatusSelect = null
                        referralPreviousStatus = null
                    }
                )

        </script>

        <!-- DataTables Core and Extensions -->
        <script type="text/javascript" src="{{ asset('vendor/datatable/datatables.min.js') }}"></script>
        <script>
            const userRoles = @json(auth()->user()->roles->pluck('name')->toArray());
        </script>

                    <script>
                        /*
                        * Number of actions that remain directly visible.
                        * All remaining actions are moved into More.
                        */
                        const appointmentVisibleActionLimit = 3

                        function appointmentActionLabel(
                            action
                        ) {
                            const labelledElement =
                                action.matches(
                                    '[title], [aria-label]'
                                )
                                    ? action
                                    : action.querySelector(
                                        '[title], [aria-label]'
                                    )

                            const title =
                                labelledElement?.getAttribute(
                                    'title'
                                )?.trim()

                            const ariaLabel =
                                labelledElement?.getAttribute(
                                    'aria-label'
                                )?.trim()

                            const visibleText =
                                action.textContent
                                    ?.replace(/\s+/g, ' ')
                                    .trim()

                            return (
                                title ||
                                ariaLabel ||
                                visibleText ||
                                'Appointment action'
                            )
                        }

                        function appointmentActionClickable(
                            action
                        ) {
                            if (
                                action.matches('a, button')
                            ) {
                                return action
                            }

                            return action.querySelector(
                                'a, button'
                            )
                        }

                        function createAppointmentMoreMenu(
                            appointmentId
                        ) {
                            const wrapper =
                                document.createElement('div')

                            wrapper.className =
                                'dropdown dropstart appointment-more-actions'

                            const toggle =
                                document.createElement('button')

                            toggle.type = 'button'
                            toggle.className =
                                'btn dropdown-toggle'

                            toggle.id =
                                `appointment-more-${appointmentId}`

                            toggle.title =
                                'More appointment actions'

                            toggle.setAttribute(
                                'aria-label',
                                'More appointment actions'
                            )

                            toggle.setAttribute(
                                'aria-expanded',
                                'false'
                            )

                            toggle.setAttribute(
                                'data-bs-toggle',
                                'dropdown'
                            )

                            toggle.setAttribute(
                                'data-bs-auto-close',
                                'outside'
                            )

                            toggle.setAttribute(
                                'data-bs-boundary',
                                'viewport'
                            )

                            toggle.innerHTML = `
                                <i
                                    class="ph ph-dots-three-vertical fs-5"
                                    aria-hidden="true"
                                ></i>

                                <span class="visually-hidden">
                                    More appointment actions
                                </span>
                            `

                            const menu =
                                document.createElement('ul')

                            menu.className =
                                'dropdown-menu dropdown-menu-end'

                            menu.setAttribute(
                                'aria-labelledby',
                                toggle.id
                            )

                            wrapper.appendChild(toggle)
                            wrapper.appendChild(menu)

                            return {
                                wrapper,
                                menu,
                            }
                        }

                        function moveAppointmentActionToMenu(
                            action,
                            menu
                        ) {
                            const item =
                                document.createElement('li')

                            item.className =
                                'appointment-dropdown-item'

                            const label =
                                document.createElement('span')

                            label.className =
                                'appointment-dropdown-label'

                            label.textContent =
                                appointmentActionLabel(action)

                            /*
                            * Move the real element, rather than cloning it.
                            * Existing URLs, onclick handlers, AJAX attributes
                            * and permission-related behavior are retained.
                            */
                            item.appendChild(action)
                            item.appendChild(label)
                            menu.appendChild(item)

                            /*
                            * Allow clicking the readable label as well as
                            * the original icon or button.
                            */
                            label.addEventListener(
                                'click',
                                function () {
                                    const clickable =
                                        appointmentActionClickable(
                                            action
                                        )

                                    clickable?.click()
                                }
                            )
                        }

                        function collapseAppointmentActions() {
                            document
                                .querySelectorAll(
                                    '.appointment-actions'
                                )
                                .forEach(container => {
                                    /*
                                    * Avoid processing an already-collapsed
                                    * row more than once.
                                    */
                                    if (
                                        container.dataset
                                            .actionsCollapsed ===
                                        'true'
                                    ) {
                                        return
                                    }

                                    /*
                                    * Every direct HTML child represents one
                                    * available action. Blade/PHP directives
                                    * do not appear as browser elements.
                                    */
                                    const actions =
                                        Array.from(
                                            container.children
                                        ).filter(element => {
                                            return !element.classList
                                                .contains(
                                                    'appointment-more-actions'
                                                )
                                        })

                                    if (
                                        actions.length <=
                                        appointmentVisibleActionLimit
                                    ) {
                                        container.dataset
                                            .actionsCollapsed =
                                            'true'

                                        return
                                    }

                                    const appointmentId =
                                        container.dataset
                                            .appointmentId ||
                                        Math.random()
                                            .toString(36)
                                            .slice(2)

                                    const moreMenu =
                                        createAppointmentMoreMenu(
                                            appointmentId
                                        )

                                    actions
                                        .slice(
                                            appointmentVisibleActionLimit
                                        )
                                        .forEach(action => {
                                            moveAppointmentActionToMenu(
                                                action,
                                                moreMenu.menu
                                            )
                                        })

                                    container.appendChild(
                                        moreMenu.wrapper
                                    )

                                    container.dataset
                                        .actionsCollapsed =
                                        'true'
                                })
                        }
                    </script>



        <script type="text/javascript" defer>
            const columns = [
                @unless (auth()->user()->hasRole('doctor') || auth()->user()->hasRole('receptionist') || auth()->user()->hasRole('user'))
                    {
                        name: 'check',
                        data: 'check',
                        title: '<input type="checkbox" class="form-check-input" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                        width: '0%',
                        exportable: false,
                        orderable: false,
                        searchable: false,
                    },
                @endunless {
                    data: 'id',
                    name: 'id',
                    title: "{{ __('appointment.lbl_id') }}",
                    searchable: false,
                    orderable: true,

                },
                {
                    data: 'user_id',
                    name: 'user_id',
                    title: "{{ __('sidebar.patient') }}",
                    orderable: true,
                    searchable: true,
                },
                {
                    data: 'start_date_time',
                    name: 'start_date_time',
                    title: "{{ __('appointment.lbl_date_time') }}",
                    orderable: true,
                },
                {
                    data: 'services',
                    name: 'services',
                    title: "{{ __('appointment.lbl_service') }}",
                    orderable: true,
                    searchable: true,
                    width: '10%'
                },

                    {
                data: 'triage_status',
                name: 'triage_status',
                title: 'Triage',
                orderable: false,
                searchable: false,
            },
               
            // {
            //     data: 'service_amount',
            //     name: 'service_amount',
            //     title: "{{ __('appointment.price') }}",
            //     orderable: true,
            //     searchable: true,
            // },
            @if(!auth()->user()->hasRole('doctor') && auth()->user()->user_type !== 'doctor')
            {
                data: 'service_amount',
                name: 'service_amount',
                title: "{{ __('appointment.price') }}",
                orderable: true,
                searchable: true,
            },
            @endif
                // {
                //     data: 'service_amount',
                //     name: 'service_amount',
                //     title: "{{ __('appointment.price') }}",
                //     orderable: true,
                //     searchable: true,
                // },
                @unless (auth()->user()->hasRole('doctor'))
                    {
                        data: 'doctor_id',
                        name: 'doctor_id',
                        title: "{{ __('appointment.lbl_doctor') }}",
                        orderable: true,
                        searchable: true,
                    },
                @endunless
                 {
                    data: 'updated_at',
                    name: 'updated_at',
                    title: "{{ __('appointment.lbl_update_at') }}",
                    orderable: true,
                    visible: false,
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    searchable: true,
                    title: "{{ __('appointment.lbl_status') }}",
                    width: '12%',
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (userRoles.includes('user')) {
                            $(td).addClass('disabled-cell');
                            $(td).attr('title', '{{ __('messages.no_permission_edit_field') }}');
                        }
                    }
                },
                {
                    data: 'payment_status',
                    name: 'payment_status',
                    orderable: false,
                    searchable: false,
                    title: "{{ __('appointment.lbl_payment_status') }}",
                    width: '10%',
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (userRoles.includes('user')) {
                            $(td).addClass('disabled-cell');
                            $(td).attr('title', '{{ __('messages.no_permission_edit_field') }}');
                        }
                    }
                },

            ]


            const actionColumn = [
                @unless (auth()->user()->hasRole('user'))
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        title: "{{ __('appointment.lbl_action') }}",
                        width: '5%'
                    }
                @endunless
            ]


            const customFieldColumns = JSON.parse(@json($columns))

            let finalColumns = [
                ...columns,
                ...customFieldColumns,
                ...actionColumn
            ]

            document.addEventListener('DOMContentLoaded', (event) => {
                // Initialize datatable first
                initDatatable({
                    url: '{{ route("backend.$module_name.index_data", ['user_id' => $user_id, 'doctor_id' => $doctor_id, 'clinic_id' => $clinic_id]) }}',
                    finalColumns,
                    orderColumn: @if (auth()->user()->hasRole('doctor'))
                        [
                            [5, "desc"]
                        ]
                    @else
                        [
                            [7, "desc"]
                        ]
                    @endif ,
                    advanceFilter: () => {
                        return {
                            doctor_id: $('#doctor_id').val(),
                            service_id: $('#service_name').val(),
                            other_patient_id: $('#other_patient').val(),
                            patient_id: $('#patient_name').val(),
                            status: $('#appointment_status').val()
                        }
                    },
                  drawCallback: () => {
                            /*
                            * Reinitialize Select2 for status fields generated
                            * by the latest DataTable draw.
                            */
                            $('.change-select').each(function () {
                                if (
                                    !$(this).hasClass(
                                        'select2-hidden-accessible'
                                    )
                                ) {
                                    $(this).select2({
                                        width: '100%',
                                        minimumResultsForSearch: -1,
                                    })
                                }
                            })

                            /*
                            * Collapse action columns after every AJAX load,
                            * search, sort, filter and pagination event.
                            */
                            collapseAppointmentActions()

                            /*
                            * Initialize tooltips inside newly rendered rows.
                            */
                            document
                                .querySelectorAll(
                                    '#datatable [data-bs-toggle="tooltip"]'
                                )
                                .forEach(element => {
                                    bootstrap.Tooltip
                                        .getOrCreateInstance(element)
                                })
                        }
                });

                // Add change event listeners for all filter dropdowns
                $('#patient_name, #doctor_id, #service_name, #appointment_status, #other_patient').on('change', function() {
                    if (window.renderedDataTable) {
                        window.renderedDataTable.ajax.reload(null, false);
                    }
                });

                // Reset filter functionality
                $('#reset-filter').on('click', function(e) {
                    e.preventDefault();
                    $('#doctor_id, #patient_name, #service_name, #appointment_status').val('').trigger('change');
                    $('#other_patient').val('').trigger('change');
                    $('#other_patient_div').addClass('d-none');
                    if (window.renderedDataTable) {
                        window.renderedDataTable.ajax.reload(null, false);
                    }
                });
            });

            function resetQuickAction() {
                const actionValue = $('#quick-action-type').val();
                if (actionValue != '') {
                    $('#quick-action-apply').removeAttr('disabled');

                    if (actionValue == 'change-status') {
                        $('.quick-action-field').addClass('d-none');
                        $('#change-status-action').removeClass('d-none');
                    } else {
                        $('.quick-action-field').addClass('d-none');
                    }
                } else {
                    $('#quick-action-apply').attr('disabled', true);
                    $('.quick-action-field').addClass('d-none');
                }
            }

            $('#quick-action-type').change(function() {
                resetQuickAction()
            });

            function dispatchCustomEvent(button) {
                const event = new CustomEvent('custom_form_assign', {
                    detail: {
                        appointment_type: button.getAttribute('data-appointment-type'),
                        appointment_id: button.getAttribute('data-appointment-id'),
                        form_id: button.getAttribute('data-form-id')
                    }
                });

                document.dispatchEvent(event);

                const offcanvasSelector = button.getAttribute('data-assign-target');
                const offcanvasElement = document.querySelector(offcanvasSelector);
                if (offcanvasElement) {
                    const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
                    offcanvas.show();
                }
            }
        </script>
        
        <script>
            $(document).ready(function() {
                // Initialize select2
                $('.select2').select2();
                // Handle patient selection change
                $('#patient_name').on('change', function() {
                    const patientId = $(this).val();
                    const $otherPatientDiv = $('#other_patient_div');
                    const $otherPatientSelect = $('#other_patient');

                    if (!patientId) {
                        $otherPatientDiv.addClass('d-none');
                        $otherPatientSelect.empty();
                        return;
                    }

                    // Fetch other patients for selected patient
                    $.ajax({
                        url: '{{ route('backend.appointment.other_patientlist') }}',
                        method: 'GET',
                        data: {
                            patient_id: patientId
                        },
                        success: function(response) {
                            $otherPatientSelect.empty().append(
                                '<option value="">{{ __('messages.select_other_patient') }}</option>'
                            );

                            if (response && response.length > 0) {
                                $otherPatientSelect.append(`
                                        <option value="">{{ __('messages.all_patients') }}</option>
                                        <option value="you">{{ __('messages.you') }}</option>
                                    `);
                                response.forEach(function(patient) {
                                    // Create the option with image and name
                                    const option = `
                                                                        <option value="${patient.id}" >
                                            ${patient.first_name}
                                        </option>
                                    `;
                                    $otherPatientSelect.append(option);
                                });

                                // Initialize Select2 with custom template
                                $otherPatientSelect.select2({
                                    templateResult: formatPatient,
                                    templateSelection: formatPatient
                                });

                                $otherPatientDiv.removeClass('d-none');
                            } else {
                                $otherPatientDiv.addClass('d-none');
                            }
                        },
                        error: function() {
                            console.error('Failed to fetch other patients');
                            $otherPatientDiv.addClass('d-none');
                        }
                    });
                });

                // Add custom format function
                function formatPatient(patient) {
                    if (!patient.id) {
                        return patient.text;
                    }

                    const $container = $(
                        `<div class="select2-patient-option d-flex align-items-center gap-2">
                            <img src="${$(patient.element).data('image')}" class="patient-avatar" style="width: 30px; height: 30px; border-radius: 50%;"/>
                            <span>${patient.text}</span>
                        </div>`
                    );

                    return $container;
                }
            });
        </script>
        <script>
            let previousStatus = null;
            let currentSelect = null;
            let selectedStatus = null;
            let appointmentId = null;
            let token = null;
            let cancellation_charge = @json(setting('cancellation_charge'));
            let cancelltion_Type = @json(setting('cancellation_type'));

            /*
 * Capture the status before Select2 changes it.
 */
$(document).on(
    'select2:opening',
    '.change-select',
    function () {
        previousStatus = $(this).val()
    }
)

/*
 * Fallback for normal non-Select2 fields.
 */
$(document).on(
    'focus pointerdown',
    '.change-select',
    function () {
        if (
            !$(this).hasClass(
                'select2-hidden-accessible'
            )
        ) {
            previousStatus = $(this).val()
        }
    }
)

function handleAppointmentStatusSelection(
    selectElement
) {
    currentSelect = $(selectElement)
    selectedStatus = currentSelect.val()
    appointmentId =
        currentSelect.data('id')

    token = currentSelect.data('token')

    /*
     * Referral status is saved through the modal,
     * not through updateStatus().
     */
    if (selectedStatus === 'referred') {
        openReferralModal(
            appointmentId,
            currentSelect,
            previousStatus
        )

        return
    }

    if (selectedStatus === 'cancelled') {
        const charge =
            Number(
                currentSelect.data('charge')
                || 0
            )

        if (charge > 0) {
            $('#cancel_charge_info').html(
                `{{ __('messages.cancellation_charges_applied') }}: ` +
                `<strong>${currencyFormat(charge)}</strong>`
            )
        } else {
            $('#cancel_charge_info').html(
                `{{ __('messages.no_cancellation_charge') }}`
            )
        }

        $('#cancelModal')
            .css('display', 'flex')
            .fadeIn()

        return
    }

    updateStatus(selectedStatus)
}

/*
 * Select2 appointment-status handling.
 */
$(document).on(
    'select2:select',
    '.change-select',
    function () {
        handleAppointmentStatusSelection(
            this
        )
    }
)

/*
 * Native-select fallback. It is deliberately disabled
 * when Select2 owns the field, preventing duplicate calls.
 */
$(document).on(
    'change',
    '.change-select',
    function () {
        if (
            $(this).hasClass(
                'select2-hidden-accessible'
            )
        ) {
            return
        }

        handleAppointmentStatusSelection(
            this
        )
    }
)

            function updateStatus(status, reason = null, charge = 0) {


                let url = "{{ route('backend.appointments.updateStatus', ['id' => '__id__', 'action_type' => 'update-status']) }}".replace('__id__', appointmentId);

                $.ajax({
                type: 'POST',
                url: url,
                headers: { 'X-CSRF-TOKEN': token },
                data: {
                    value: status,
                    ...(status === 'cancelled' ? {
                    reason: reason,
                    cancellation_charge_amount: charge,
                    cancellation_type: cancelltion_Type,
                    cancellation_charge: cancellation_charge
                    } : {})
                },
                success: function (res) {
                    window.successSnackbar(res.message);
                    closeCancelModal();
                    $('#datatable').DataTable().ajax.reload();
                },
                error: function () {
                    console.error('{{ __('messages.something_went_wrong') }}');
                }
                });
            }

            function closeCancelModal() {
                $('#cancelModal').hide();
                $('#cancel_reason').val('');
            }

            function cancelAbort() {
                closeCancelModal();
                $('#datatable').DataTable().ajax.reload();
            }

            function submitCancellation() {
                const reason = $('#cancel_reason').val();
                let charge = currentSelect.data('charge');

                $('#cancel_reason').removeClass('is-invalid');
                $('#cancel_reason_error').remove();

                if (!reason) {
                $('#cancel_reason').addClass('is-invalid');
                $('#cancel_reason').after('<div id="cancel_reason_error" class="invalid-feedback d-block">{{ __("messages.please_enter_reason") }}</div>');
                return;
                }

                $('#confirm_btn').html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __('appointment.loading') }}');

                updateStatus('cancelled', reason, charge);
            }

            // Expose cancel and submit functions globally if needed
            window.cancelAbort = cancelAbort;
            window.submitCancellation = submitCancellation;

            // Use global Select2 system - no custom initialization needed
            // The global system in backend-custom.js will handle .select2 elements automatically

        </script>
        
        <script>
        // Function to view appointment details in modal
        function viewAppointmentDetails(appointmentId) {
            const modal = new bootstrap.Modal(document.getElementById('appointmentDetailsModal'));
            const contentDiv = document.getElementById('appointmentDetailsContent');
            
            // Show loading state
            contentDiv.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading appointment details...</p>
                </div>
            `;
            
            modal.show();
            
            // Fetch appointment details
            fetch(`{{ url('app/appointment/view-details') }}/${appointmentId}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status) {
                        contentDiv.innerHTML = renderAppointmentDetails(result.data);
                    } else {
                        contentDiv.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="ph ph-warning-circle me-2"></i>
                                Failed to load appointment details. Please try again.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    contentDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="ph ph-warning-circle me-2"></i>
                            An error occurred while loading appointment details.
                        </div>
                    `;
                });
        }
        

            // new data for appointmets
            function escapeClinicalHtml(value) {
            const element = document.createElement('div')
            element.textContent = value == null ? '' : String(value)
            return element.innerHTML
            }

            function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;')
            }

        function renderAppointmentDetails(data) {
            const patientName = data.other_patient ? data.other_patient.name : data.patient.name;
            const patientEmail = data.other_patient ? data.other_patient.email : data.patient.email;
            const patientPhone = data.other_patient ? data.other_patient.phone : data.patient.phone;
            
            let html = `
                <div class="row">
                    <!-- Appointment Information -->
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h6><i class="ph ph-calendar-check me-2"></i>Appointment Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Appointment ID:</span>
                                <span class="detail-value">#${data.appointment.id}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Date:</span>
                                <span class="detail-value">${data.appointment.appointment_date}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Time:</span>
                                <span class="detail-value">${data.appointment.appointment_time}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Duration:</span>
                                <span class="detail-value">${data.appointment.duration} minutes</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Status:</span>
                                <span class="detail-value">
                                    <span class="status-badge bg-${getStatusColor(data.appointment.status)}">${data.appointment.status}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Patient Information -->
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h6><i class="ph ph-user me-2"></i>Patient Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Name:</span>
                                <span class="detail-value">${patientName}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">${patientEmail || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value">${patientPhone || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Doctor & Service Information -->
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h6><i class="ph ph-user-circle me-2"></i>Doctor Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Doctor:</span>
                                <span class="detail-value">${data.doctor.name}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">${data.doctor.email || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h6><i class="ph ph-hospital me-2"></i>Service & Clinic</h6>
                            <div class="detail-row">
                                <span class="detail-label">Service:</span>
                                <span class="detail-value">${data.service.name || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Category:</span>
                                <span class="detail-value">${data.category.name || 'N/A'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Clinic:</span>
                                <span class="detail-value">${data.clinic.name || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Information -->
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h6><i class="ph ph-currency-circle-dollar me-2"></i>Payment Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Total Amount:</span>
                                <span class="detail-value">${data.appointment.total_amount || '0'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Advance Paid:</span>
                                <span class="detail-value">${data.appointment.advance_paid_amount || '0'}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Payment Status:</span>
                                <span class="detail-value">
                                    <span class="status-badge bg-${data.payment.payment_status == 1 ? 'success' : 'warning'}">
                                        ${data.payment.payment_status == 1 ? 'Paid' : 'Pending'}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Medical History -->
                    <div class="col-12">
                        <div class="detail-card">
                            <h6><i class="ph ph-file-text me-2"></i>Medical History</h6>
                            ${data.medical_data.medical_history_text ? 
                                `<div class="medical-history-text">${data.medical_data.medical_history_text}</div>` : 
                                '<div class="no-data">No medical history recorded</div>'
                            }
                        </div>
                    </div>
                    
                    <!-- Audio Recordings -->
                    ${data.medical_data.audio_recordings && data.medical_data.audio_recordings.length > 0 ? `
                        <div class="col-12">
                            <div class="detail-card">
                                <h6><i class="ph ph-microphone me-2"></i>Audio Recordings</h6>
                                ${data.medical_data.audio_recordings.map((recording, index) => `
                                    <div class="mb-3">
                                        <label class="form-label">Recording ${index + 1}</label>
                                        <audio controls class="audio-player">
                                            <source src="${recording.url}" type="audio/webm">
                                            <source src="${recording.url}" type="audio/wav">
                                            Your browser does not support the audio element.
                                        </audio>
                                     ${recording.transcription ? `
                                        <div class="mt-2">
                                            <small class="text-muted">Transcription:</small>

                                            ${(() => {
                                                const highlighted = highlightMedicalTerms(recording.transcription);

                                                return `
                                                    <div class="medical-history-text mb-2">
                                                        ${highlighted.html}
                                                    </div>

                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                        <span class="badge bg-danger text-white">Symptoms: ${highlighted.counts.Symptoms || 0}</span>
                                                        <span class="badge bg-warning text-dark">Allergies: ${highlighted.counts.Allergies || 0}</span>
                                                        <span class="badge bg-info text-white">Medications: ${highlighted.counts.Medications || 0}</span>
                                                        <span class="badge bg-success text-white">Triggers: ${highlighted.counts.Triggers || 0}</span>
                                                    </div>
                                                `;
                                            })()}
                                        </div>
                                    ` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    
                    <!-- Documents -->
                    ${data.documents && data.documents.length > 0 ? `
                        <div class="col-12">
                            <div class="detail-card">
                                <h6><i class="ph ph-file me-2"></i>Uploaded Documents</h6>
                                ${data.documents.map(doc => `
                                    <div class="document-item">
                                        <i class="ph ph-file-pdf document-icon"></i>
                                        <div class="document-info">
                                            <div class="document-name">${doc.name}</div>
                                            <div class="document-size">${formatFileSize(doc.size)}</div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="${doc.url}" target="_blank" class="btn btn-sm btn-info">
                                                <i class="ph ph-eye"></i> View
                                            </a>
                                            <a href="${doc.download_url}" class="btn btn-sm btn-primary" download>
                                                <i class="ph ph-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                    
                    <!-- Additional Notes -->
                    ${data.appointment.appointment_extra_info ? `
                        <div class="col-12">
                            <div class="detail-card">
                                <h6><i class="ph ph-note me-2"></i>Additional Notes</h6>
                                <div class="medical-history-text">${data.appointment.appointment_extra_info}</div>
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
            const clinical = data.clinical_history || {}

const conditions = clinical.conditions || []
const medications = clinical.medications || []
const allergies = clinical.allergies || []
const familyHistory = clinical.family_history || []
const observations = clinical.observations || []
const social = clinical.social_history || null

html += `
  <div class="row mt-3">
    <div class="col-12">
      <div class="detail-card">
        <h6>
          <i class="ph ph-first-aid me-2"></i>
          Structured Clinical History
        </h6>

        ${allergies.length > 0 ? `
          <div class="alert alert-danger">
            <strong>Allergy warning</strong>

            ${allergies.map(item => `
              <div>
                ${escapeClinicalHtml(item.allergen)}
                ${item.reaction
                  ? `— ${escapeClinicalHtml(item.reaction)}`
                  : ''
                }
                (${escapeClinicalHtml(item.severity)})
              </div>
            `).join('')}
          </div>
        ` : ''}

        <div class="row">
          <div class="col-md-6">
            <h6>Conditions</h6>

            ${conditions.length > 0
              ? conditions.map(item => `
                  <p class="mb-1">
                    <strong>
                      ${escapeClinicalHtml(item.condition_name)}
                    </strong>
                    — ${escapeClinicalHtml(item.status)}
                  </p>
                `).join('')
              : '<p class="text-muted">No conditions recorded.</p>'
            }
          </div>

          <div class="col-md-6">
            <h6>Medication</h6>

            ${medications.length > 0
              ? medications.map(item => `
                  <p class="mb-1">
                    <strong>
                      ${escapeClinicalHtml(item.medication_name)}
                    </strong>
                    ${item.dose
                      ? escapeClinicalHtml(item.dose)
                      : ''
                    }
                    ${item.frequency
                      ? `— ${escapeClinicalHtml(item.frequency)}`
                      : ''
                    }
                  </p>
                `).join('')
              : '<p class="text-muted">No medications recorded.</p>'
            }
          </div>

          <div class="col-md-6 mt-3">
            <h6>Social history</h6>

            ${social ? `
              <p class="mb-1">
                Smoking:
                ${escapeClinicalHtml(social.smoking_status)}
              </p>

              <p class="mb-1">
                Alcohol:
                ${escapeClinicalHtml(social.alcohol_status)}
              </p>
            ` : '<p class="text-muted">No social history.</p>'}
          </div>

          <div class="col-md-6 mt-3">
            <h6>Family history</h6>

            ${familyHistory.length > 0
              ? familyHistory.map(item => `
                  <p class="mb-1">
                    <strong>
                      ${escapeClinicalHtml(item.relationship)}:
                    </strong>
                    ${escapeClinicalHtml(item.condition_name)}
                  </p>
                `).join('')
              : '<p class="text-muted">No family history.</p>'
            }
          </div>

          <div class="col-12 mt-3">
            <h6>Observations</h6>

            ${observations.length > 0 ? `
              <p>
                Height: ${observations[0].height_cm || '—'} cm |
                Weight: ${observations[0].weight_kg || '—'} kg |
                BMI: ${observations[0].bmi || '—'} |
                BP:
                ${observations[0].systolic || '—'}/
                ${observations[0].diastolic || '—'}
              </p>
            ` : '<p class="text-muted">No observations recorded.</p>'}
          </div>
        </div>
      </div>
    </div>
  </div>
`
            
            return html;
        }
        
        function getStatusColor(status) {
            const colors = {
                'pending': 'warning',
                'confirmed': 'info',
                'check_in': 'primary',
                'checkout': 'success',
                'cancelled': 'danger',
                'rejected': 'danger'
            };
            return colors[status] || 'secondary';
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

       function highlightMedicalTerms(text) {
    if (!text) {
        return {
            html: '',
            counts: {}
        };
    }

    let safeText = String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    const categories = {
        Symptoms: {
            className: 'bg-danger text-white',
            words: ['pain', 'ache', 'fever', 'cough', 'bleeding', 'swelling', 'vomiting', 'nausea', 'dizziness', 'headache', 'tooth', 'teeth', 'sensitive', 'sensitivity']
        },
        Allergies: {
            className: 'bg-warning text-dark',
            words: ['allergy', 'allergic', 'rash', 'reaction']
        },
        Medications: {
            className: 'bg-info text-white',
            words: ['medicine', 'medication', 'antibiotic', 'tablet', 'numbing', 'solution']
        },
        Triggers: {
            className: 'bg-success text-white',
            words: ['hot', 'cold', 'drink', 'eating']
        }
    };

    const counts = {};

    Object.entries(categories).forEach(([categoryName, category]) => {
        counts[categoryName] = 0;

        category.words.forEach(word => {
            const regex = new RegExp(`\\b(${word})\\b`, 'gi');

            safeText = safeText.replace(regex, function(match) {
                counts[categoryName]++;
                return `<span class="badge ${category.className} mx-1">${match}</span>`;
            });
        });
    });

    return {
        html: safeText,
        counts
    };
}
        </script>

    @endpush
