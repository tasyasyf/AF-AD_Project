<x-layouts.app title="Certificates (View)">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Certificates</h5>
        <span class="badge bg-secondary"><i class="bi bi-eye"></i> Read-only view · granted by administrator</span>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" style="max-width:220px" placeholder="Title / institution / AF/AD...">
        <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>AF/AD</th>
                        <th>Institution</th>
                        <th>Year</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $certificate)
                        <tr class="detail-trigger" data-detail="detail-{{ $certificate->id }}" data-title="{{ $certificate->title }}">
                            <td class="fw-semibold small">{{ $certificate->title }}</td>
                            <td class="small">{{ $certificate->profile?->full_name }}</td>
                            <td class="small">{{ $certificate->issuing_institution }}</td>
                            <td class="small">{{ $certificate->year_obtained }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">No certificates found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">{{ $certificates->links() }}</div>

{{-- Hidden detail blocks (rendered into the modal on row click) --}}
<div class="d-none">
    @foreach($certificates as $certificate)
        <div id="detail-{{ $certificate->id }}">
            <dl class="row mb-0">
                <dt class="col-sm-4 text-muted">Title</dt>
                <dd class="col-sm-8">{{ $certificate->title }}</dd>
                <dt class="col-sm-4 text-muted">AF/AD</dt>
                <dd class="col-sm-8">{{ $certificate->profile?->full_name ?? '—' }}</dd>
                <dt class="col-sm-4 text-muted">Institution</dt>
                <dd class="col-sm-8">{{ $certificate->issuing_institution ?: '—' }}</dd>
                <dt class="col-sm-4 text-muted">Year</dt>
                <dd class="col-sm-8">{{ $certificate->year_obtained ?: '—' }}</dd>
                @if($certificate->file_original_name)
                    <dt class="col-sm-4 text-muted">File</dt>
                    <dd class="col-sm-8"><i class="bi bi-file-earmark"></i> {{ $certificate->file_original_name }}</dd>
                @endif
            </dl>
        </div>
    @endforeach
</div>

@include('access.partials.detail-modal')

</x-layouts.app>
