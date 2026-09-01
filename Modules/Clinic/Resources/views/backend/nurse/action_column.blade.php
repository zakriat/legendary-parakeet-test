@php
    $isDemo = env('IS_DEMO', false);
@endphp

<div class="d-flex gap-3 align-items-center">
    @hasPermission('edit_clinic_nurse_list')
        <button
            type="button"
            class="btn text-success p-0 fs-5"
            data-crud-id="{{ $data->id }}"
            data-bs-toggle="tooltip"
            title="{{ __('messages.edit') }} {{ __('nurse.singular_title') }}">
            <i class="ph ph-pencil-simple-line align-middle"></i>
        </button>
    @endhasPermission

    <button
        type="button"
        class="btn p-0 fs-6 text-info nurse-change-password-btn"
        data-nurse-id="{{ $data->id }}"
        data-bs-toggle="tooltip"
        title="{{ __('messages.change_password') }}">
        <i class="ph ph-key align-middle"></i>
    </button>

    @hasPermission('delete_clinic_nurse_list')
        @if (! $isDemo)
            <a
                href="{{ route("backend.$module_name.destroy", $data->id) }}"
                id="delete-{{ $module_name }}-{{ $data->id }}"
                class="btn text-danger p-0 fs-5"
                data-type="ajax"
                data-method="DELETE"
                data-token="{{ csrf_token() }}"
                data-bs-toggle="tooltip"
                title="{{ __('messages.delete') }}"
                data-confirm="{{ __('messages.action_warning_message') }}">
                <i class="ph ph-trash align-middle"></i>
            </a>
        @endif
    @endhasPermission
</div>
