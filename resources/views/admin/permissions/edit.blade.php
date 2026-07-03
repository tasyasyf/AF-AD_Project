<x-layouts.app title="Manage Permissions">

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Additional Access — {{ $user->name }}</h5>
        <div class="text-muted small">{{ $user->email }} · <span class="text-uppercase">{{ $user->role }}</span></div>
    </div>
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('admin.permissions.update', $user) }}">
    @csrf
    @method('PUT')

    {{-- Master toggle --}}
    <div class="card mb-3">
        <div class="card-body d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div>
                <div class="fw-semibold">Additional Access</div>
                <div class="text-muted small">Master switch. Turn OFF to disable all extra access at once without losing the selections below.</div>
            </div>
            <div class="form-check form-switch fs-4 mb-0">
                <input class="form-check-input" type="checkbox" role="switch" id="master" name="additional_access_enabled" value="1"
                    {{ $user->additional_access_enabled ? 'checked' : '' }}>
                <label class="form-check-label visually-hidden" for="master">Additional Access master switch</label>
            </div>
        </div>
    </div>

    {{-- Per-function toggles --}}
    <div class="card">
        <div class="card-header bg-white fw-semibold">Viewable Functions (read-only)</div>
        <div class="list-group list-group-flush" id="function-list">
            @foreach($catalog as $key => $meta)
                <div class="list-group-item d-flex align-items-center justify-content-between gap-3 py-3">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-{{ $meta['icon'] }} fs-4 text-primary"></i>
                        <div>
                            <div class="fw-semibold">{{ $meta['label'] }}</div>
                            <div class="text-muted small">{{ $meta['description'] }}</div>
                        </div>
                    </div>
                    <div class="form-check form-switch fs-4 mb-0">
                        <input class="form-check-input function-toggle" type="checkbox" role="switch"
                            id="perm-{{ $key }}" name="permissions[]" value="{{ $key }}"
                            {{ in_array($key, $granted, true) ? 'checked' : '' }}>
                        <label class="form-check-label visually-hidden" for="perm-{{ $key }}">{{ $meta['label'] }}</label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Changes</button>
    </div>
</form>

{{-- Dim the function list when the master switch is OFF (visual cue only; server still stores selections) --}}
<script>
    (function () {
        const master = document.getElementById('master');
        const list = document.getElementById('function-list');
        function sync() {
            list.style.opacity = master.checked ? '1' : '0.5';
        }
        master.addEventListener('change', sync);
        sync();
    })();
</script>

</x-layouts.app>
