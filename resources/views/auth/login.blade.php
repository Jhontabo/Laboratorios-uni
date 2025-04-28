<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>Inicio de Sesión</title>
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #0B3D91;
        }

        .login-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .google-btn {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #fff;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px 25px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            max-width: 320px;
            margin: 20px auto;
            transition: background-color 0.3s;
        }

        .google-btn:hover {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <img src="{{ asset('img/logo.png') }}" alt="Logo UMariana">
            <h2 class="text-blue-900 text-2xl font-bold mb-4">Bienvenido a LABORATORIOS U MARIANA</h2>

            <!-- Mostrar mensajes flash -->
            @if (session('error'))
                <div class="mb-4 text-red-500 font-semibold">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="mb-4 text-green-500 font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Botón de Google -->
            <a href="{{ url('/auth/google') }}" class="google-btn">
                <img width="25" height="25" src="https://img.icons8.com/fluency/48/google-logo.png"
                    alt="google-logo" />
                Iniciar sesión con Google
            </a>

            <p class="aviso mt-4 text-gray-500 text-sm">Aviso de cookies</p>
        </div>
    </div>
</body>

</html>
