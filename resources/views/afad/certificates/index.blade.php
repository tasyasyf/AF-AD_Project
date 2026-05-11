<x-layouts.app title="My Certificates">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">My Certificates</h5>
    <a href="{{ route('afad.certificates.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Add Certificate
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($certificates->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-award fs-1 d-block mb-2"></i>
                No certificates added yet.
                <a href="{{ route('afad.certificates.create') }}">Add your first certificate</a>.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Institution</th>
                            <th>Year</th>
                            <th>File</th>
                            <th>Profile Review</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificates as $cert)
                        <tr>
                            <td class="fw-semibold">{{ $cert->title }}</td>
                            <td>{{ $cert->issuing_institution }}</td>
                            <td>{{ $cert->year_obtained }}</td>
                            <td>
                                @if($cert->file_original_name)
                                    <span class="text-success small"><i class="bi bi-file-earmark-check"></i> {{ $cert->file_original_name }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $profile->status === 'verified' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $profile->status === 'verified' ? 'Verified with Profile' : 'Pending Profile Review' }}
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('afad.certificates.destroy', $cert) }}"
                                    onsubmit="return confirm('Remove this certificate?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

</x-layouts.app>
