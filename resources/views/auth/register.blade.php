<x-layouts.guest>
    <div class="card auth-card my-5">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="brand-logo text-primary mb-2">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h4 class="fw-bold mb-0">AF/AD Management System</h4>
                <p class="text-muted small mt-1">Create a new account</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name') }}"
                            placeholder="Your full name" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email') }}"
                            placeholder="you@example.com" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label fw-semibold">Register As</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">Select role...</option>
                            <option value="afad" {{ old('role') === 'afad' ? 'selected' : '' }}>Academic Facilitator / Developer (AF/AD)</option>
                            <option value="executive" {{ old('role') === 'executive' ? 'selected' : '' }}>School Executive</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Minimum 8 characters" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control"
                            id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-person-plus me-1"></i> Create Account
                </button>
            </form>

            <div class="text-center mt-3">
                <span class="text-muted small">Already have an account?</span>
                <a href="{{ route('login') }}" class="small fw-semibold">Sign In</a>
            </div>
        </div>
        <div class="card-footer bg-light text-center text-muted small py-3">
            Academic Facilitator &amp; Developer System &copy; {{ date('Y') }}
        </div>
    </div>
</x-layouts.guest>
