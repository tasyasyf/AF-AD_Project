@props([
    'user' => null,
    'photoAlt' => 'Profile photo',
    'showRemove' => false,
])

@php
    $uploaderId = 'profile-photo-uploader-' . str_replace('.', '-', uniqid('', true));
    $currentPhotoUrl = $user?->profile_photo_path ? route('profile-photo.show', $user) : null;
@endphp

<div id="{{ $uploaderId }}" class="profile-photo-uploader text-center" data-profile-photo-uploader>
    <div class="mb-3">
        @if($currentPhotoUrl)
            <button type="button" class="profile-photo-preview-button" data-bs-toggle="modal" data-bs-target="#{{ $uploaderId }}-modal">
                <img src="{{ $currentPhotoUrl }}" alt="{{ $photoAlt }}" class="profile-photo-lg">
            </button>
        @else
            <span class="profile-photo-lg profile-photo-placeholder">
                <i class="bi bi-person"></i>
            </span>
        @endif
    </div>

    <div class="photo-cropper d-none mb-3" data-cropper>
        <div class="photo-crop-frame mx-auto" data-crop-frame>
            <img src="" alt="Selected profile photo" data-crop-image>
        </div>
        <div class="mt-3 text-start">
            <label class="form-label fw-semibold small">Zoom</label>
            <input type="range" class="form-range" min="1" max="3" step="0.01" value="1" data-crop-zoom>
        </div>
    </div>

    <div class="text-start">
        <input type="file" name="profile_photo" class="d-none @error('profile_photo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-photo-input>
        <button type="button" class="btn btn-outline-primary w-100" data-change-photo>
            <i class="bi bi-camera me-1"></i> Change Photo
        </button>
        <div class="form-text">JPG, PNG, or WEBP - max 10MB. Drag the preview and adjust zoom before saving.</div>
        @error('profile_photo') <div class="invalid-feedback">{{ $message }}</div> @enderror

        @if($showRemove && $currentPhotoUrl)
            <div class="form-check mt-3">
                <input type="checkbox" name="remove_profile_photo" value="1" class="form-check-input" id="{{ $uploaderId }}-remove">
                <label class="form-check-label" for="{{ $uploaderId }}-remove">Remove current photo</label>
            </div>
        @endif
    </div>

    @if($currentPhotoUrl)
        <div class="modal fade" id="{{ $uploaderId }}-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Profile Photo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ $currentPhotoUrl }}" alt="{{ $photoAlt }}" class="profile-photo-modal-image mb-3">
                        <button type="button" class="btn btn-primary w-100" data-change-photo data-bs-dismiss="modal">
                            <i class="bi bi-camera me-1"></i> Change Photo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@once
    <style>
        .profile-photo-preview-button {
            padding: 0;
            border: 0;
            background: transparent;
            border-radius: 50%;
            line-height: 0;
        }
        .profile-photo-preview-button:focus-visible {
            outline: 3px solid rgba(195, 18, 31, 0.22);
            outline-offset: 4px;
        }
        .profile-photo-modal-image {
            width: min(320px, 78vw);
            height: min(320px, 78vw);
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--portal-border);
        }
        .photo-crop-frame {
            width: 220px;
            height: 220px;
            position: relative;
            overflow: hidden;
            border-radius: 50%;
            border: 2px solid var(--portal-border);
            background: var(--portal-soft);
            cursor: grab;
            touch-action: none;
        }
        .photo-crop-frame:active {
            cursor: grabbing;
        }
        .photo-crop-frame img {
            position: absolute;
            top: 50%;
            left: 50%;
            max-width: none;
            user-select: none;
            pointer-events: none;
            transform-origin: center;
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const maxPhotoSize = 10 * 1024 * 1024;

        document.querySelectorAll('[data-profile-photo-uploader]').forEach(function (uploader) {
            const input = uploader.querySelector('[data-photo-input]');
            const changeButtons = uploader.querySelectorAll('[data-change-photo]');
            const cropper = uploader.querySelector('[data-cropper]');
            const frame = uploader.querySelector('[data-crop-frame]');
            const image = uploader.querySelector('[data-crop-image]');
            const zoom = uploader.querySelector('[data-crop-zoom]');
            const form = uploader.closest('form');

            let imageUrl = null;
            let baseScale = 1;
            let userScale = 1;
            let offsetX = 0;
            let offsetY = 0;
            let dragStart = null;
            let imageReady = false;

            function applyTransform() {
                image.style.transform = `translate(calc(-50% + ${offsetX}px), calc(-50% + ${offsetY}px)) scale(${userScale})`;
            }

            function resetCrop() {
                userScale = 1;
                offsetX = 0;
                offsetY = 0;
                zoom.value = '1';
                applyTransform();
            }

            function loadImage(file) {
                if (imageUrl) {
                    URL.revokeObjectURL(imageUrl);
                }

                imageReady = false;
                imageUrl = URL.createObjectURL(file);
                image.src = imageUrl;
                cropper.classList.remove('d-none');
                cropper.scrollIntoView({ block: 'nearest', behavior: 'smooth' });

                image.onload = function () {
                    const frameWidth = frame.clientWidth;
                    const frameHeight = frame.clientHeight;
                    baseScale = Math.max(frameWidth / image.naturalWidth, frameHeight / image.naturalHeight);
                    image.style.width = `${image.naturalWidth * baseScale}px`;
                    image.style.height = `${image.naturalHeight * baseScale}px`;
                    resetCrop();
                    imageReady = true;
                };
            }

            changeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    input.click();
                });
            });

            input.addEventListener('change', function () {
                const file = input.files[0];
                form?.removeAttribute('data-profile-photo-adjusted');

                if (!file) {
                    cropper.classList.add('d-none');
                    return;
                }

                if (file.size > maxPhotoSize) {
                    input.value = '';
                    cropper.classList.add('d-none');
                    alert('Profile photo must be 10MB or smaller.');
                    return;
                }

                loadImage(file);
            });

            zoom.addEventListener('input', function () {
                userScale = Number(zoom.value);
                applyTransform();
            });

            frame.addEventListener('pointerdown', function (event) {
                if (!imageReady) return;
                dragStart = {
                    pointerId: event.pointerId,
                    x: event.clientX,
                    y: event.clientY,
                    offsetX,
                    offsetY,
                };
                frame.setPointerCapture(event.pointerId);
            });

            frame.addEventListener('pointermove', function (event) {
                if (!dragStart || dragStart.pointerId !== event.pointerId) return;
                offsetX = dragStart.offsetX + event.clientX - dragStart.x;
                offsetY = dragStart.offsetY + event.clientY - dragStart.y;
                applyTransform();
            });

            frame.addEventListener('pointerup', function (event) {
                if (dragStart?.pointerId === event.pointerId) {
                    dragStart = null;
                }
            });

            frame.addEventListener('pointercancel', function () {
                dragStart = null;
            });

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (!input.files.length || !imageReady || form.dataset.profilePhotoAdjusted === '1') {
                        return;
                    }

                    event.preventDefault();

                    const frameWidth = frame.clientWidth;
                    const frameHeight = frame.clientHeight;
                    const outputSize = 512;
                    const canvas = document.createElement('canvas');
                    canvas.width = outputSize;
                    canvas.height = outputSize;

                    const context = canvas.getContext('2d');
                    context.fillStyle = '#ffffff';
                    context.fillRect(0, 0, outputSize, outputSize);

                    const displayedWidth = image.naturalWidth * baseScale * userScale;
                    const displayedHeight = image.naturalHeight * baseScale * userScale;
                    const outputScale = outputSize / frameWidth;
                    const drawX = (frameWidth / 2 + offsetX - displayedWidth / 2) * outputScale;
                    const drawY = (frameHeight / 2 + offsetY - displayedHeight / 2) * outputScale;

                    context.drawImage(
                        image,
                        drawX,
                        drawY,
                        displayedWidth * outputScale,
                        displayedHeight * outputScale
                    );

                    canvas.toBlob(function (blob) {
                        if (!blob) {
                            form.dataset.profilePhotoAdjusted = '1';
                            form.submit();
                            return;
                        }

                        const originalName = input.files[0].name.replace(/\.[^.]+$/, '');
                        const adjustedFile = new File([blob], `${originalName}-profile.jpg`, { type: 'image/jpeg' });
                        const transfer = new DataTransfer();
                        transfer.items.add(adjustedFile);
                        input.files = transfer.files;
                        form.dataset.profilePhotoAdjusted = '1';
                        form.submit();
                    }, 'image/jpeg', 0.9);
                });
            }
        });
    });
    </script>
@endonce
