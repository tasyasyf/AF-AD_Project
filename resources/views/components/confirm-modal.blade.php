@props(['id', 'title' => 'Confirm Action', 'message' => 'Are you sure?', 'action', 'method' => 'POST', 'confirmText' => 'Confirm', 'confirmClass' => 'btn-danger', 'fields' => []])

<div class="modal fade" id="{{ $id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ $action }}">
                @csrf
                @if($method !== 'POST')
                    @method($method)
                @endif
                <div class="modal-body">
                    <p class="mb-0">{{ $message }}</p>
                    @foreach($fields as $field)
                        <div class="mt-3">
                            <label for="{{ $id }}_{{ $field['name'] }}" class="form-label fw-semibold">
                                {{ $field['label'] }}
                                @if($field['required'] ?? false) <span class="text-danger">*</span> @endif
                            </label>
                            <textarea class="form-control" id="{{ $id }}_{{ $field['name'] }}"
                                name="{{ $field['name'] }}" rows="3"
                                @if($field['required'] ?? false) required @endif
                                placeholder="{{ $field['placeholder'] ?? '' }}"></textarea>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn {{ $confirmClass }}">{{ $confirmText }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
