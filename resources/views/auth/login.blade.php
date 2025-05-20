<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Inicio de Sesión</title>

    <!-- Fuente moderna -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #0B3D91;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-card {
            background: white;
            border-radius: 12px;
            padding: 40px 30px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .login-card img {
            width: 120px;
            margin-bottom: 20px;
        }

        .login-card h2 {
            color: #0B3D91;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .message {
            margin-bottom: 20px;
            font-weight: 500;
        }

        .error {
            color: #e74c3c;
        }

        .success {
            color: #27ae60;
        }

        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .google-btn img {
            margin-right: 10px;
            width: 22px;
            height: 22px;
        }

        .google-btn:hover {
            background-color: #f0f0f0;
        }

        .aviso {
            margin-top: 20px;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <img src="{{ asset('img/logo.png') }}" alt="Logo UMariana">
        <h2>Bienvenido a LABORATORIOS U MARIANA</h2>

        <!-- Mensajes flash -->
        @if (session('error'))
            <div class="message error">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="message success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Botón de Google -->
        <a href="{{ url('/auth/google') }}" class="google-btn">
            <img src="https://img.icons8.com/fluency/48/google-logo.png" alt="Google logo">
            Iniciar sesión con Google
        </a>

        <p class="aviso">Este sitio utiliza cookies. Al continuar aceptas su uso.</p>
    </div>
</body>

</html>

