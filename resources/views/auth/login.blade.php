<x-layouts.guest>
    <div class="card auth-card my-5">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="brand-logo text-primary mb-2">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h4 class="fw-bold mb-0">AF/AD Management System</h4>
                <p class="text-muted small mt-1">Sign in to your account</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email') }}"
                            placeholder="you@example.com" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="••••••••" required>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label small" for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                </button>
            </form>

            <div class="text-center mt-3">
                <span class="text-muted small">Don't have an account?</span>
                <a href="{{ route('register') }}" class="small fw-semibold">Create Account</a>
            </div>
        </div>
        <div class="card-footer bg-light text-center text-muted small py-3">
            Academic Facilitator &amp; Developer System &copy; {{ date('Y') }}
        </div>
    </div>
</x-layouts.guest>
