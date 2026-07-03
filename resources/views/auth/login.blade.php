<x-layouts.guest>
    <div class="auth-card">
        <section class="auth-visual">
            <div class="auth-visual-content">
                <div>
                    <h1>Academic Portal</h1>
                    <p>Secure gateway for institutional records, research data, and academic administration.</p>
                </div>
            </div>
        </section>

        <section class="auth-form-pane">
            <div class="auth-form-inner">
                <div class="brand-logo">
                    <i class="bi bi-bank"></i>
                </div>
                <h2 class="auth-heading">Welcome Back</h2>
                <p class="auth-subtitle">Please enter your details to access your portal.</p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (request()->boolean('timeout'))
                    <div class="alert alert-warning py-2 small">
                        Your session has ended, please log in again.
                    </div>
                @endif

                <form method="POST" action="{{ route('login.attempt') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email or Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}"
                                placeholder="e.g. j.doe@institution.edu" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <a href="#" class="small">Forgot Password?</a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="mb-4 d-flex align-items-center justify-content-between">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label small" for="remember">Remember this device for 30 days</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold">
                        Login <i class="bi bi-box-arrow-in-right ms-1"></i>
                    </button>
                </form>

                <p class="auth-terms text-center">
                    By logging in, you agree to our <a href="#">Terms of Service</a> and <a href="#">Security Protocols</a>.
                </p>
            </div>
        </section>
    </div>
</x-layouts.guest>
