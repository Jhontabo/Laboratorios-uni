<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <!-- Importar estilos de Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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

        .login-card img {
            width: 150px;
            margin: 0 auto 20px;
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: background-color 0.3s;
        }

        .google-btn:hover {
            background-color: #f2f2f2;
        }

        .google-btn img {
            width: 25px;
            margin-right: 15px;
            vertical-align: middle;
        }

        .aviso {
            color: #555;
            font-size: 14px;
            margin-top: 20px;
        }
    </style>
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
        <img src="{{ asset('img/logo.png') }}" alt="Logo UMariana">

        <img src="https://virtual.umariana.edu.co/campus/pluginfile.php/1/theme_remui/loginpanellogo/1727444047/dise%C3%B1o-plataforma.png" class="navbar-brand-logo logo">
            <h2 class="text-blue-900 text-2xl font-bold mb-4">Bienvenido a LABORATORIOS U MARIANA</h2>

            <!-- Botón de Google -->
            <a href="{{ url('/auth/google') }}" class="google-btn">
                <img width="70" height="70" src="https://img.icons8.com/fluency/48/google-logo.png" alt="google-logo"/>
                Iniciar sesión con Google
            </a>

            <p class="aviso">Aviso de cookies</p>
        </div>
    </div>

</body>
</html>
