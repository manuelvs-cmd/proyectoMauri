<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página de Ejemplo</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        .top-bar {
            width: 100%;
            background-color: #007BFF;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        .top-bar a,
        .top-bar button {
            color: white;
            text-decoration: none;
            font-weight: bold;
            background-color: #0056b3;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            transition: background 0.3s ease;
            cursor: pointer;
        }

        .top-bar a:hover,
        .top-bar button:hover {
            background-color: #004094;
        }

        .contenido {
            padding-top: 100px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <a href="{{ route('dashboard') }}">← Volver al Dashboard</a>

        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </div>

    <div class="contenido">
        <h1>Página de Ejemplo</h1>
        <p>¡Estás autenticado! Esta es una página de muestra.</p>
    </div>

</body>
</html>
