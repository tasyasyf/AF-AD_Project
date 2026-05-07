<x-layouts.app title="Edit Claim">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Edit Claim – {{ $claim->claim_reference }}</h5>
    <a href="{{ route('afad.claims.show', $claim) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('afad.claims.update', $claim) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Appointment</label>
                        <input type="text" class="form-control" disabled
                            value="{{ $claim->appointment->course_code }} – {{ $claim->appointment->course_name }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Claim Type <span class="text-danger">*</span></label>
                        <select name="claim_type" class="form-select @error('claim_type') is-invalid @enderror" required>
                            @foreach(['teaching'=>'Teaching','marking'=>'Marking','module_development'=>'Module Development','consultation'=>'Consultation'] as $val => $label)
                                <option value="{{ $val }}" {{ old('claim_type', $claim->claim_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('claim_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Hours <span class="text-danger">*</span></label>
                            <input type="number" name="total_hours" id="total_hours"
                                class="form-control @error('total_hours') is-invalid @enderror"
                                value="{{ old('total_hours', $claim->total_hours) }}" min="0.5" step="0.5" required>
                            @error('total_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rate per Hour (RM) <span class="text-danger">*</span></label>
                            <input type="number" name="rate_per_hour" id="rate_per_hour"
                                class="form-control @error('rate_per_hour') is-invalid @enderror"
                                value="{{ old('rate_per_hour', $claim->rate_per_hour) }}" min="0" step="0.01" required>
                            @error('rate_per_hour') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Submission Checklist</label>
                        <div class="row g-2">
                            @foreach([
                                'has_mark_entry_forms' => 'Mark-entry Forms',
                                'has_graded_scripts' => 'Graded Scripts',
                                'has_qa' => 'Question Paper & Answer Sheet',
                            ] as $field => $label)
                                @php($isSubmitted = $claim->{$field} || ($submissionChecklist[$field] ?? false))
                                <div class="col-md-6">
                                    <div class="form-check border rounded px-3 py-2 h-100 {{ $isSubmitted ? 'bg-light' : '' }}">
                                        <input type="checkbox" value="1"
                                            class="form-check-input ms-0 me-2" id="{{ $field }}"
                                            {{ $isSubmitted ? 'checked' : '' }} disabled>
                                        <label class="form-check-label ps-2" for="{{ $field }}">
                                            {{ $label }}
                                            <span class="text-muted small">({{ $isSubmitted ? 'submitted' : 'not submitted yet' }})</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="alert alert-light border d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-calculator text-primary fs-5"></i>
                        <div>
                            <span class="text-muted small">Estimated Total Amount:</span>
                            <strong class="ms-1" id="total-display">RM {{ number_format($claim->total_amount, 2) }}</strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateTotal() {
    const hours = parseFloat(document.getElementById('total_hours').value) || 0;
    const rate  = parseFloat(document.getElementById('rate_per_hour').value) || 0;
    document.getElementById('total-display').textContent = 'RM ' + (hours * rate).toFixed(2);
}
document.getElementById('total_hours').addEventListener('input', updateTotal);
document.getElementById('rate_per_hour').addEventListener('input', updateTotal);
</script>

</x-layouts.app>
