<x-layouts.app title="New Claim">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Submit New Claim</h5>
    <a href="{{ route('afad.claims.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('afad.claims.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Appointment <span class="text-danger">*</span></label>
                        <select name="appointment_id" class="form-select @error('appointment_id') is-invalid @enderror" required>
                            <option value="">Select appointment...</option>
                            @foreach($appointments as $appt)
                                <option value="{{ $appt->id }}" {{ old('appointment_id') == $appt->id ? 'selected' : '' }}>
                                    {{ $appt->course_code }} – {{ $appt->course_name }} ({{ strtoupper($appt->role_type) }}, {{ $appt->semester }})
                                </option>
                            @endforeach
                        </select>
                        @error('appointment_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if($appointments->isEmpty())
                            <div class="form-text text-danger">No active appointments found.</div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Claim Type <span class="text-danger">*</span></label>
                        <select name="claim_type" class="form-select @error('claim_type') is-invalid @enderror" required>
                            <option value="">Select type...</option>
                            @foreach(['teaching'=>'Teaching','marking'=>'Marking','module_development'=>'Module Development','consultation'=>'Consultation'] as $val => $label)
                                <option value="{{ $val }}" {{ old('claim_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('claim_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Period From <span class="text-danger">*</span></label>
                            <input type="date" name="period_from" class="form-control @error('period_from') is-invalid @enderror"
                                value="{{ old('period_from') }}" required>
                            @error('period_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Period To <span class="text-danger">*</span></label>
                            <input type="date" name="period_to" class="form-control @error('period_to') is-invalid @enderror"
                                value="{{ old('period_to') }}" required>
                            @error('period_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Hours <span class="text-danger">*</span></label>
                            <input type="number" name="total_hours" id="total_hours"
                                class="form-control @error('total_hours') is-invalid @enderror"
                                value="{{ old('total_hours') }}" min="0.5" step="0.5" required>
                            @error('total_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rate per Hour (RM) <span class="text-danger">*</span></label>
                            <input type="number" name="rate_per_hour" id="rate_per_hour"
                                class="form-control @error('rate_per_hour') is-invalid @enderror"
                                value="{{ old('rate_per_hour') }}" min="0" step="0.01" required>
                            @error('rate_per_hour') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="alert alert-light border d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-calculator text-primary fs-5"></i>
                        <div>
                            <span class="text-muted small">Estimated Total Amount:</span>
                            <strong class="ms-1" id="total-display">RM 0.00</strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save as Draft
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
