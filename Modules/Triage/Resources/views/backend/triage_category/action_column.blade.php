<div class="d-flex align-items-center gap-3">
    @hasPermission('edit_triage_category')
        <button
            type="button"
            class="btn text-success p-0 fs-5"
            onclick="editCategory({{ $data->id }})"
            data-bs-toggle="tooltip"
            title="{{ __('messages.edit') }}"
            aria-label="{{ __('messages.edit') }}"
        >
            <i class="ph ph-pencil-simple-line align-middle"></i>
        </button>
    @endhasPermission

    @hasPermission('delete_triage_category')
        <a
            href="{{ route('backend.triage-category.destroy', $data->id) }}"
            id="delete-{{ $module_name }}-{{ $data->id }}"
            class="btn text-danger p-0 fs-5"
            data-type="ajax"
            data-method="DELETE"
            data-token="{{ csrf_token() }}"
            data-bs-toggle="tooltip"
            title="{{ __('messages.delete') }}"
            aria-label="{{ __('messages.delete') }}"
            data-confirm="{{ __('messages.are_you_sure?', [
                'form' => $data->name ?? __('Unknown'),
                'module' => __('triage.category'),
            ]) }}"
        >
            <i class="ph ph-trash align-middle"></i>
        </a>
    @endhasPermission
</div>
