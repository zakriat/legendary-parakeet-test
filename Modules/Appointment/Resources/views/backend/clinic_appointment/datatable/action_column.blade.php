<!-- <div class="text-end d-flex gap-3 align-items-center"> -->
<div
    class="appointment-actions"
    data-appointment-id="{{ $data->id }}">
   @php
    $appointmentDate = \Carbon\Carbon::parse(
        $data->appointment_date
    );

    $googleMeetSetting = \App\Models\Setting::where(
        'name',
        'google_meet_method'
    )->first();

    $googleMeetEnabled =
        (int) optional($googleMeetSetting)->val === 1;

    $zoomSetting = \App\Models\Setting::where(
        'name',
        'is_zoom'
    )->first();

    $zoomEnabled =
        (int) optional($zoomSetting)->val === 1;

    $isVideoAppointment =
        $data->consultation_mode === 'video' ||
        (int) optional(
            $data->clinicservice
        )->is_video_consultancy === 1;

    $canUseVideo = !in_array(
        $data->status,
        [
            'cancel',
            'cancelled',
            'completed',
            'checkout',
            'dna',
        ],
        true
    );

    $pay_status = optional($data->payment)
        ->payment_status;
@endphp


<!-- online appoinment -->

    {{-- NEW: View Full Appointment Details Button --}}
    <button type="button" class="btn btn-icon text-primary p-0 fs-5" data-bs-toggle="tooltip" title="View Full Details" onclick="viewAppointmentDetails({{ $data->id }})">
        <i class="ph ph-eye"></i>
    </button>

    {{-- @if (!in_array($data->status, ['pending', 'confirm', 'cancel']) && $data->status !== null)
    @if($data->patientEncounter !=null)
    <button type='button' data-assign-module="{{$data->patientEncounter->id}}" data-assign-target='#patient-encounter-offcanvas' data-assign-event='patient-dashboard' class='btn text-primary p-0 fs-5' data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('appointment.patient_encounter')}}"><i class="icon ph ph-squares-four align-middle"></i></button>
    @endif
    @endif --}}



    @if (!in_array($data->status, ['pending', 'confirm', 'cancel']) && $data->status !== null)
    @if($data->patientEncounter !=null)
    <a href="{{ route('backend.encounter.encounter-detail-page', ['id' => $data->patientEncounter->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('appointment.patient_encounter') }}"><i class="icon ph ph-squares-four align-middle"></i></a>
    @endif
    @endif


    {{-- @if(optional($data->clinicservice)->is_video_consultancy == 1 && $appointmentDate->isToday())
    @if($googleMeetEnabled && $data->meet_link != null)
    <a href="{{ route("backend.google_connect", ['id' => $data->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('clinic.google_meet') }}"><i class="fa-solid fa-video"></i></a>
    @endif --}}


    <!-- new appointment changes -->

    @if(
    $isVideoAppointment &&
    $canUseVideo &&
    $appointmentDate->isToday()
)
    @if($googleMeetEnabled && filled($data->meet_link))
        <a
            href="{{ route(
                'backend.google_connect',
                ['id' => $data->id]
            ) }}"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-sm btn-dark d-inline-flex align-items-center gap-2 px-3"
            data-bs-toggle="tooltip"
            title="Start Google Meet consultation"
            aria-label="Start Google Meet consultation"
        >
            <i class="fa-solid fa-video"></i>
            <span class="d-none d-xl-inline">
                Start call
            </span>
        </a>
    @elseif($zoomEnabled && filled($data->start_video_link))
        <a
            href="{{ route(
                'backend.zoom_connect',
                ['id' => $data->id]
            ) }}"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-sm btn-dark d-inline-flex align-items-center gap-2 px-3"
            data-bs-toggle="tooltip"
            title="Start Zoom consultation"
            aria-label="Start Zoom consultation"
        >
            <i class="fa-solid fa-video"></i>
            <span class="d-none d-xl-inline">
                Start call
            </span>
        </a>
    @else
        <span
            class="badge border text-dark bg-white px-3 py-2"
            data-bs-toggle="tooltip"
            title="A video meeting link has not been generated"
        >
            <i class="fa-solid fa-video-slash me-1"></i>
            Link unavailable
        </span>
    @endif
@endif
    <!-- ends -->

    {{--@if($zoomEnabled && $data->start_video_link != null)
    <a href="{{ route("backend.zoom_connect", ['id' => $data->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('clinic.zoom_meet') }}"><i class="fa-solid fa-video"></i></a>
    @endif
    @endif
    @if(setting('view_patient_soap') == 1)
    <a href="{{route("backend.patient-record", ['id' => $data->id])}}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('clinic.appointment_patient_records') }}"><i class="fa-solid fa-notepad"></i></a>
    @endif --}}

    <!-- <button type='button' data-assign-module="{{$data->id}}" data-assign-target='#appointment-offcanvas' data-assign-event='appointment-details' class='btn text-primary p-0 fs-5' data-bs-toggle='tooltip' title='Clinic Session'><i class="fa-solid fa-eye"></i></a> -->
    <!-- </button> -->
    @if($pay_status == 1 && $data->status == 'checkout')
    <a href="{{ route('backend.appointments.invoice_detail', ['id' => $data->id]) }}" data-type="ajax" class='btn text-info p-0 fs-5' data-bs-toggle="tooltip" title="{{ __('clinic.invoice_detail') }}">
    <i class="ph ph-file-pdf"></i>
    </a>
    @endif
    @unless(auth()->user()->hasRole('doctor'))
    @hasPermission('delete_clinic_appointment_list')
    <a href="{{ route('backend.appointment.destroy', $data->id) }}"
       id="delete-{{ $module_name }}-{{ $data->id }}"
       class="btn text-danger p-0 fs-5"
       data-type="ajax"
       data-method="DELETE"
       data-token="{{ csrf_token() }}"
       data-bs-toggle="tooltip"
       title="{{ __('messages.delete') }}"
       data-confirm="{{ __('messages.are_you_sure?', ['form' => ($data->user->full_name ?? default_user_name()), 'module' => __('appointment.singular_title')]) }}">
       <i class="ph ph-trash"></i>
    </a>
    @endhasPermission
@endunless


@if($customform)

    @foreach($customform as $form)

        @php
        $formdata=json_decode($form->formdata);
        $appointment_status= json_decode($form->appointment_status);

        // Normalize the appointment status and adjust 'confirm' to 'confirmed'
        $AppointmentStatus = array_map(function ($status) {
            return strtolower(trim($status)) === 'confirm' ? 'confirmed' : strtolower(trim($status));
        }, (array) $appointment_status);
        @endphp

        @if (in_array(strtolower($data->status), $AppointmentStatus) && $data->status !== null)

        <button type="button"
                data-assign-target="#appointment-customform"
                data-assign-event="custom_form_assign"
                data-appointment-type="appointment"
                data-appointment-id="{{ $data->id }}"
                data-form-id="{{ $form->id }}"
                class="btn text-info p-0 fs-5"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="{{ $formdata->form_title }}"
                onclick="dispatchCustomEvent(this)">
                <i class="icon ph ph-file align-middle"></i>
            </button>
        @endif
    @endforeach
@endif


@if($data->status === 'referred')
    @if($data->referral)
        <button
            type="button"
            class="btn btn-sm btn-outline-dark"
            onclick="openReferralModal({{ $data->id }})"
            title="View or edit referral"
            aria-label="View or edit referral for appointment {{ $data->id }}"
        >
            <i class="fa-solid fa-user-doctor"></i>
            Referral
        </button>

        <a
            href="{{ route(
                'backend.appointments.referral.pdf',
                $data->id
            ) }}"
            class="btn btn-sm btn-dark"
            title="Download referral PDF"
            aria-label="Download referral PDF for appointment {{ $data->id }}"
        >
            <i class="fa-solid fa-file-pdf"></i>
            PDF
        </a>
    @else
        <button
            type="button"
            class="btn btn-sm btn-outline-dark"
            onclick="openReferralModal({{ $data->id }})"
        >
            Complete referral
        </button>
    @endif
@endif

</div>
