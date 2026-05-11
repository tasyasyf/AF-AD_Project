<x-layouts.app title="My Profile">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">My Profile</h5>
    <a href="{{ route('afad.profile.edit') }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-pencil me-1"></i> Edit Profile
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Personal Information</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Full Name</dt>
                    <dd class="col-sm-8">{{ $profile->full_name }}</dd>
                    <dt class="col-sm-4 text-muted">IC Number</dt>
                    <dd class="col-sm-8">{{ $profile->ic_number }}</dd>
                    <dt class="col-sm-4 text-muted">Date of Birth</dt>
                    <dd class="col-sm-8">{{ $profile->date_of_birth?->format('d M Y') ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Gender</dt>
                    <dd class="col-sm-8">{{ $profile->gender ? ucfirst($profile->gender) : '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Phone</dt>
                    <dd class="col-sm-8">{{ $profile->phone }}</dd>
                    <dt class="col-sm-4 text-muted">Email</dt>
                    <dd class="col-sm-8">{{ $profile->contact_email }}</dd>
                    <dt class="col-sm-4 text-muted">Address</dt>
                    <dd class="col-sm-8">{{ $profile->address }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Qualification</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Qualification</dt>
                    <dd class="col-sm-8">{{ $profile->qualification }}</dd>
                    <dt class="col-sm-4 text-muted">Level</dt>
                    <dd class="col-sm-8">{{ ucfirst($profile->qualification_level) }}</dd>
                    <dt class="col-sm-4 text-muted">Specialisation</dt>
                    <dd class="col-sm-8">{{ $profile->specialisation ?? '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Area of Expertise</dt>
                    <dd class="col-sm-8">{{ $profile->area_of_expertise ?? '—' }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Bank Information</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Bank</dt>
                    <dd class="col-sm-8">{{ $profile->bank_name }}</dd>
                    <dt class="col-sm-4 text-muted">Account Number</dt>
                    <dd class="col-sm-8">{{ $profile->bank_account_number }}</dd>
                    <dt class="col-sm-4 text-muted">Account Holder</dt>
                    <dd class="col-sm-8">{{ $profile->bank_account_holder }}</dd>
                </dl>
            </div>
        </div>

        {{-- Resume / CV --}}
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Resume / CV</div>
            <div class="card-body">
                @if($profile->resume_path)
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                        <div>
                            <div class="fw-semibold">{{ $profile->resume_original_name }}</div>
                            <div class="text-muted small">{{ number_format($profile->resume_size / 1024, 1) }} KB</div>
                        </div>
                    </div>
                @else
                    <p class="text-muted small mb-0">No resume uploaded yet.</p>
                @endif
            </div>
        </div>

        {{-- Certifications --}}
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Certifications / Verification Documents</span>
                <a href="{{ route('afad.certificates.index') }}" class="btn btn-sm btn-outline-primary">Manage</a>
            </div>
            <div class="card-body">
                @forelse($profile->certificates as $cert)
                    <div class="d-flex align-items-start gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="flex-shrink-0">
                            <i class="bi bi-award fs-4 {{ $profile->status === 'verified' ? 'text-success' : 'text-muted' }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $cert->title }}</div>
                            <div class="text-muted small">{{ $cert->issuing_institution }} &mdash; {{ $cert->year_obtained }}</div>
                            @if($cert->file_original_name)
                                <div class="small mt-1">
                                    <i class="bi bi-paperclip"></i> {{ $cert->file_original_name }}
                                    <span class="text-muted">({{ number_format($cert->file_size / 1024, 1) }} KB)</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <span class="badge {{ $profile->status === 'verified' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $profile->status === 'verified' ? 'Verified with Profile' : 'Pending Profile Review' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No certificates uploaded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Profile Status</div>
            <div class="card-body text-center py-4">
                <x-status-badge :status="$profile->status" />
                @if($profile->status === 'verified')
                    <div class="text-muted small mt-2">
                        Verified by {{ $profile->verifier?->name }}<br>
                        {{ $profile->verified_at?->format('d M Y') }}
                    </div>
                @elseif($profile->status === 'rejected')
                    <div class="alert alert-danger text-start mt-3 py-2 small">
                        @php
                            $sectionLabels = [
                                'personal' => 'Personal Information',
                                'qualification' => 'Qualification',
                                'bank' => 'Bank Information',
                                'resume' => 'Resume / CV',
                                'certificates' => 'Certificates',
                                'other' => 'Other',
                            ];
                            $sections = collect($profile->rejection_sections ?? [])
                                ->map(fn ($section) => $sectionLabels[$section] ?? ucfirst($section));
                        @endphp
                        @if($sections->isNotEmpty())
                            <strong>Edit section:</strong>
                            <div class="mt-1 mb-2">
                                @foreach($sections as $section)
                                    <span class="badge bg-light text-dark border me-1 mb-1">{{ $section }}</span>
                                @endforeach
                            </div>
                        @endif
                        <strong>Reason:</strong> {{ $profile->rejection_reason }}
                        <div class="mt-2">
                            <a href="{{ route('afad.profile.edit') }}" class="btn btn-sm btn-danger">
                                Edit Profile
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
