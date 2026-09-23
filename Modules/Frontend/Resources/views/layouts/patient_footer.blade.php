<footer class="patient-compact-footer">
    <div>© {{ now()->year }} {{ app_name() }}. All Rights Reserved</div>
    <div>Powered by <a href="https://divigor.com">Divigor</a></div>
</footer>

<style>
    .patient-compact-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        padding: 10px 24px;
        background: #f8f9fa;
        border-top: 1px solid #e5e7eb;
        color: #6c757d;
        font-size: 13px;
    }

    .patient-compact-footer a {
        color: #0043a1;
        text-decoration: none;
    }

    @media (max-width: 576px) {
        .patient-compact-footer {
            padding: 10px 14px;
            font-size: 11px;
        }
    }
</style>
