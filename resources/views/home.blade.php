<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
        }

        /* Barra superior */
        .top-bar {
            width: 100%;
            background-color: #007BFF;
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .top-bar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            background-color: #0056b3;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .top-bar a:hover {
            background-color: #004094;
        }

        /* Contenido principal */
        .contenido {
            padding-top: 100px; /* espacio por la barra fija */
            text-align: center;
        }

        .contenido h1 {
            font-size: 2em;
            margin-bottom: 15px;
        }

        .contenido p {
            color: #555;
            font-size: 1.1em;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <a href="{{ route('login') }}">Iniciar sesión</a>
    </div>

    <div class="contenido">
        <h1>Bienvenido a la página de inicio</h1>
        <p>Usa el botón de arriba para acceder al sistema.</p>
    </div>

</body>
</html>
