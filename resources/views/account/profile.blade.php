<x-layouts.app title="My Profile">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">My Profile</h5>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Account Information</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Name</dt>
                    <dd class="col-sm-8">{{ $user->name }}</dd>
                    <dt class="col-sm-4 text-muted">Email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>
                    <dt class="col-sm-4 text-muted">Role</dt>
                    <dd class="col-sm-8">
                        @if($user->isExecutive())
                            School Executive
                        @elseif($user->isProgramCoordinator())
                            Program Coordinator
                        @else
                            {{ strtoupper($user->role) }}
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Profile Photo</div>
            <div class="card-body text-center">
                <form method="POST" action="{{ route($user->isExecutive() ? 'executive.profile.update' : 'pc.profile.update') }}" enctype="multipart/form-data" class="text-start">
                    @csrf
                    @method('PUT')

                    <x-profile-photo-uploader :user="$user" :photo-alt="$user->name" :show-remove="true" />

                    <button type="submit" class="btn btn-primary w-100 mt-3">
                        <i class="bi bi-save me-1"></i> Save Photo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
