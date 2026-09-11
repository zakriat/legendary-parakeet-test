@extends('frontend::layouts.patient_layout')

@section('title', $pageTitle)

@push('after-styles')
<style>
    #patient-section-records .badge {
        color: #fff !important;
    }

    .patient-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid patient-main-content">
    <div class="patient-section-heading">
        <div>
            <a href="{{ route('patient.dashboard') }}"
               class="text-decoration-none small">
                <i class="ph ph-arrow-left me-1" aria-hidden="true"></i>
                Back to dashboard
            </a>

            <h1 class="h4 mt-2 mb-0">
                <i class="ph {{ $pageIcon }} me-2" aria-hidden="true"></i>
                {{ $pageTitle }}
            </h1>
        </div>

        @if($endpointRoute === 'patient.dashboard.appointments')
            <a href="{{ route('services') }}" class="btn btn-primary">
                <i class="ph ph-calendar-plus me-2" aria-hidden="true"></i>
                Book appointment
            </a>
        @endif
    </div>

    <div id="patient-section-records"
         aria-live="polite"
         aria-busy="true">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading records…</span>
                </div>
                <p class="mt-3 mb-0">Loading your records…</p>
            </div>
        </div>
    </div>

    <div id="patient-section-error"
         class="alert alert-danger d-none"
         role="alert">
        <p class="mb-2">Your records could not be loaded. Please try again.</p>
        <button type="button"
                id="patient-section-retry"
                class="btn btn-outline-danger btn-sm">
            Try again
        </button>
    </div>
</div>
@endsection

@push('after-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const records = document.getElementById('patient-section-records');
    const error = document.getElementById('patient-section-error');
    const endpoint = @json(route($endpointRoute));

    async function loadRecords() {
        error.classList.add('d-none');
        records.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(endpoint, {
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Unable to load records');
            }

            const data = await response.json();

            if (data.status === false) {
                throw new Error('Unable to load records');
            }

            records.innerHTML = data.html ||
                '<div class="card"><div class="card-body">' +
                'No records found.</div></div>';
        } catch (exception) {
            records.innerHTML = '';
            error.classList.remove('d-none');
        } finally {
            records.setAttribute('aria-busy', 'false');
        }
    }

    document.getElementById('patient-section-retry')
        .addEventListener('click', loadRecords);

    loadRecords();
});
</script>
@endpush