<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AF/AD System') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --portal-red: #c3121f;
            --portal-red-dark: #9f0f19;
            --portal-ink: #2c1d1d;
            --portal-muted: #7a6262;
            --portal-border: #efc4bf;
            --portal-soft: #fff7f6;
            --portal-panel: #fffdfc;
        }

        body {
            min-height: 100vh;
            color: var(--portal-ink);
            background: #fff;
        }

        a { color: var(--portal-red); text-decoration: none; }
        a:hover { color: var(--portal-red-dark); text-decoration: underline; }

        .auth-shell {
            width: 100%;
            min-height: 100vh;
        }

        .auth-frame {
            min-height: 100vh;
        }

        .auth-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(390px, 1fr);
            overflow: hidden;
            min-height: 100vh;
            border: 1px solid var(--portal-border);
            border-radius: 0;
            background: var(--portal-panel);
            box-shadow: none;
        }

        .auth-visual {
            position: relative;
            display: flex;
            min-height: 100%;
            padding: clamp(2rem, 6vw, 5.5rem);
            background:
                linear-gradient(180deg, rgba(255, 242, 241, 0.91) 0%, rgba(255, 235, 232, 0.86) 46%, rgba(253, 229, 225, 0.94) 100%),
                url('/images/academic-portal-building-aeu-centered.png') center bottom / cover no-repeat;
            border-right: 1px solid #f0d2cd;
        }

        .auth-visual-content {
            position: relative;
            z-index: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 3rem;
        }

        .auth-visual h1 {
            color: var(--portal-red);
            font-size: clamp(2.1rem, 4vw, 2.65rem);
            line-height: 1.1;
            margin-bottom: 1rem;
            font-weight: 800;
        }

        .auth-visual p {
            max-width: 31rem;
            color: #5d4242;
            font-size: 1.1rem;
            line-height: 1.7;
            margin: 0;
        }

        .auth-form-pane {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(2rem, 5vw, 5.25rem);
            background: rgba(255, 255, 255, 0.94);
        }

        .auth-form-inner {
            width: min(100%, 392px);
        }

        .brand-logo {
            color: var(--portal-red);
            font-size: 2.55rem;
            line-height: 1;
            margin-bottom: 1.1rem;
        }

        .auth-heading {
            font-size: 2rem;
            line-height: 1.1;
            font-weight: 800;
            margin-bottom: 0.6rem;
            color: var(--portal-ink);
        }

        .auth-subtitle {
            color: #644747;
            margin-bottom: 1.75rem;
            font-size: 1rem;
        }

        .form-label {
            color: #4c3333;
            font-size: 0.93rem;
            margin-bottom: 0.45rem;
        }

        .input-group-text,
        .form-control,
        .form-select {
            border-color: var(--portal-border);
            border-radius: 0;
            color: var(--portal-ink);
            min-height: 51px;
        }

        .input-group-text {
            background: #fff;
            color: #987979;
            min-width: 52px;
            justify-content: center;
        }

        .form-control:focus,
        .form-select:focus,
        .form-check-input:focus {
            border-color: var(--portal-red);
            box-shadow: 0 0 0 0.2rem rgba(195, 18, 31, 0.12);
        }

        .form-check-input {
            border-color: var(--portal-border);
            border-radius: 2px;
        }

        .form-check-input:checked {
            background-color: var(--portal-red);
            border-color: var(--portal-red);
        }

        .btn-primary {
            --bs-btn-bg: var(--portal-red);
            --bs-btn-border-color: var(--portal-red);
            --bs-btn-hover-bg: var(--portal-red-dark);
            --bs-btn-hover-border-color: var(--portal-red-dark);
            --bs-btn-active-bg: #870c14;
            --bs-btn-active-border-color: #870c14;
        }

        .btn-outline-primary {
            --bs-btn-color: var(--portal-red);
            --bs-btn-border-color: var(--portal-border);
            --bs-btn-hover-bg: var(--portal-red);
            --bs-btn-hover-border-color: var(--portal-red);
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #9b7d7d;
            font-size: 0.82rem;
            margin: 2.2rem 0 1.9rem;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: "";
            height: 1px;
            flex: 1;
            background: var(--portal-border);
        }

        .auth-terms {
            color: #684f4f;
            font-size: 0.78rem;
            line-height: 1.25;
            margin-top: 0.85rem;
        }

        @media (max-width: 991.98px) {
            .auth-card { grid-template-columns: 1fr; min-height: auto; }
            .auth-visual { min-height: 420px; border-right: 0; border-bottom: 1px solid #f0d2cd; }
        }

        @media (max-width: 575.98px) {
            .auth-visual, .auth-form-pane { padding: 1.5rem; }
            .auth-visual { min-height: 350px; }
        }
    </style>
</head>
<body>
    <main class="auth-shell">
        <div class="auth-frame">
        {{ $slot }}
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
