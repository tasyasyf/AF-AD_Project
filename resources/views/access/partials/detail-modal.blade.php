{{-- Reusable read-only detail modal. Any table row with class "detail-trigger",
     a data-detail="<id of a hidden detail block>" and data-title fills and opens it. --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalTitle">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody"></div>
            <div class="modal-footer">
                <span class="badge bg-secondary me-auto"><i class="bi bi-eye"></i> Read-only</span>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Runs after the DOM is parsed and the Bootstrap bundle (loaded at the end
    // of <body>) is available, so bootstrap.Modal is defined by this point.
    document.addEventListener('DOMContentLoaded', function () {
        const modalEl = document.getElementById('detailModal');
        if (!modalEl || typeof bootstrap === 'undefined') return;
        const body = document.getElementById('detailModalBody');
        const title = document.getElementById('detailModalTitle');

        document.querySelectorAll('.detail-trigger').forEach(function (row) {
            row.style.cursor = 'pointer';
            row.addEventListener('click', function () {
                const src = document.getElementById(row.dataset.detail);
                if (!src) return;
                body.innerHTML = src.innerHTML;
                title.textContent = row.dataset.title || 'Details';
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            });
        });
    });
</script>
