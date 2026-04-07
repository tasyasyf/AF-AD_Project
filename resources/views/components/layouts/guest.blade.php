<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'AF/AD System') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a3a6e 0%, #2d6a4f 100%); min-height: 100vh; }
        .auth-card { border: none; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .brand-logo { font-size: 2rem; font-weight: 700; letter-spacing: 2px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">
    <div class="container" style="max-width: 460px;">
        {{ $slot }}
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
