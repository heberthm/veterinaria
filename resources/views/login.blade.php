<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · VetCloud</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/vetcloud.css') }}">
    <style>
        body.vc-login-body {
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; background: var(--vc-page-bg);
        }
        .vc-login-card {
            width: 100%; max-width: 380px; background: #fff;
            border-radius: var(--vc-radius-lg); border: 1px solid var(--vc-border);
            box-shadow: var(--vc-shadow); padding: 32px;
        }
        .vc-login-brand { display: flex; flex-direction: column; align-items: center; gap: 10px; margin-bottom: 24px; }
        .vc-login-brand .vc-brand-icon { width: 52px; height: 52px; font-size: 22px; }
        .vc-login-brand strong { font-size: 19px; }
        .vc-field { margin-bottom: 16px; }
        .vc-field label { font-size: 12.5px; font-weight: 600; color: var(--vc-text-soft); display: block; margin-bottom: 6px; }
        .vc-field input {
            width: 100%; border: 1px solid var(--vc-border); border-radius: 10px;
            padding: 10px 14px; font-size: 13.5px; outline: none;
        }
        .vc-field input:focus { border-color: var(--vc-blue); }
        .vc-btn-primary {
            width: 100%; background: var(--vc-blue); color: #fff; border: none;
            border-radius: 10px; padding: 11px; font-weight: 700; font-size: 13.5px;
        }
        .vc-error { background: var(--vc-red-soft); color: var(--vc-red); font-size: 12.5px; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; }
    </style>
</head>
<body class="vc-login-body">
    <div class="vc-login-card">
        <div class="vc-login-brand">
            <span class="vc-brand-icon"><i class="fas fa-paw"></i></span>
            <strong>VetCloud</strong>
            <span style="font-size:12px;color:var(--vc-text-muted)">{{ currentTenant()?->nombre_clinica ?? 'Ingresa a tu clínica' }}</span>
        </div>

        @if ($errors->any())
            <div class="vc-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="vc-field">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="vc-field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="vc-btn-primary">Iniciar sesión</button>
        </form>
    </div>
</body>
</html>
