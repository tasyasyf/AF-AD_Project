<x-layouts.guest>
    <div class="auth-card">
        <section class="auth-visual">
            <div class="auth-visual-content">
                <div>
                    <h1>Academic Portal</h1>
                    <p>Create a verified account for academic appointments, submissions, certificates, and claim administration.</p>
                </div>

                <ul class="auth-benefits">
                    <li><i class="bi bi-person-check"></i>Role-based access for every department</li>
                    <li><i class="bi bi-shield-lock"></i>Protected onboarding for academic records</li>
                </ul>
            </div>
        </section>

        <section class="auth-form-pane">
            <div class="auth-form-inner">
                <div class="brand-logo">
                    <i class="bi bi-bank"></i>
                </div>
                <h2 class="auth-heading">Create Account</h2>
                <p class="auth-subtitle">Register your portal access details and account role below.</p>

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
                        <label for="role" class="form-label fw-semibold">Account Role</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Select role...</option>
                                <option value="afad" {{ old('role') === 'afad' ? 'selected' : '' }}>Academic Facilitator / Developer (AF/AD)</option>
                                <option value="executive" {{ old('role') === 'executive' ? 'selected' : '' }}>School Executive</option>
                                <option value="pc" {{ old('role') === 'pc' ? 'selected' : '' }}>Program Coordinator</option>
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

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold">
                        Create Account <i class="bi bi-person-plus ms-1"></i>
                    </button>
                </form>

                <div class="auth-divider">Already registered?</div>

                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 py-2 fw-semibold">
                    Return to Login
                </a>
            </div>
        </section>
    </div>
</x-layouts.guest>
