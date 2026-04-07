@props(['document', 'claim', 'editable' => false])

<div class="d-flex align-items-center justify-content-between py-2 border-bottom">
    <div class="d-flex align-items-center gap-2">
        @if($document->is_uploaded)
            <i class="bi bi-file-earmark-check-fill text-success fs-5"></i>
        @elseif($document->is_required)
            <i class="bi bi-file-earmark-x-fill text-danger fs-5"></i>
        @else
            <i class="bi bi-file-earmark text-muted fs-5"></i>
        @endif
        <div>
            <span class="fw-semibold">{{ $document->label }}</span>
            @if($document->is_required)
                <span class="badge bg-danger ms-1" style="font-size:0.65rem;">Required</span>
            @else
                <span class="badge bg-light text-muted ms-1 border" style="font-size:0.65rem;">Optional</span>
            @endif
            @if($document->is_uploaded)
                <div class="text-muted small">
                    {{ $document->file_original_name }}
                    &bull; {{ number_format($document->file_size / 1024, 1) }} KB
                    &bull; {{ $document->uploaded_at?->format('d M Y H:i') }}
                </div>
            @endif
        </div>
    </div>

    @if($editable)
        <div class="d-flex gap-2">
            @if($document->is_uploaded)
                <form method="POST" action="{{ route('afad.claims.documents.remove', [$claim, $document]) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            @endif
            <form method="POST" action="{{ route('afad.claims.documents.upload', [$claim, $document]) }}"
                enctype="multipart/form-data" class="d-flex gap-1">
                @csrf
                <input type="file" name="document_file" class="form-control form-control-sm"
                    style="max-width: 200px;" accept=".pdf,.jpg,.jpeg,.png" required>
                <button type="submit" class="btn btn-sm btn-primary text-nowrap">
                    <i class="bi bi-upload"></i> Upload
                </button>
            </form>
        </div>
    @else
        @if($document->is_uploaded)
            <span class="text-success small"><i class="bi bi-check2"></i> Uploaded</span>
        @else
            <span class="text-muted small">—</span>
        @endif
    @endif
</div>
