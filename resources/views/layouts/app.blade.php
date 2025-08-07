<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MiApp')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('img/Logo.jpeg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('img/Logo.jpeg') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/Logo.jpeg') }}">
    <link rel="icon" type="image/jpeg" sizes="32x32" href="{{ asset('img/Logo.jpeg') }}">
    <link rel="icon" type="image/jpeg" sizes="16x16" href="{{ asset('img/Logo.jpeg') }}">
    <meta name="msapplication-TileImage" content="{{ asset('img/Logo.jpeg') }}">
    <meta name="msapplication-TileColor" content="#007BFF">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Estilos CSS integrados (sin Vite) -->
    
    <style>
        /* Estilos adicionales para facturas */
        .card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.25rem;
        }
        .card-body {
            padding: 1.25rem;
        }
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.375rem;
            text-decoration: none;
            margin-right: 0.25rem;
        }
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .btn-success {
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-warning {
            color: #212529;
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .btn-danger {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-info {
            color: #fff;
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            border-radius: 0.2rem;
        }
        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .alert {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
        }
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
        }
        .badge-primary {
            color: #fff;
            background-color: #007bff;
        }
        .badge-secondary {
            color: #fff;
            background-color: #6c757d;
        }
        .badge-success {
            color: #fff;
            background-color: #28a745;
        }
        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }
        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .btn-group {
            position: relative;
            display: inline-flex;
            vertical-align: middle;
        }
        .btn-group > .btn:not(:last-child) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .btn-group > .btn:not(:first-child) {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        .d-flex {
            display: flex !important;
        }
        .justify-content-between {
            justify-content: space-between !important;
        }
        .align-items-center {
            align-items: center !important;
        }
        .mb-3 {
            margin-bottom: 1rem !important;
        }
        .text-center {
            text-align: center !important;
        }
        .col-md-3, .col-md-8, .col-md-12 {
            position: relative;
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
        }
        @media (min-width: 768px) {
            .col-md-3 {
                flex: 0 0 25%;
                max-width: 25%;
            }
            .col-md-8 {
                flex: 0 0 66.666667%;
                max-width: 66.666667%;
            }
            .col-md-12 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        .container {
            width: 100%;
            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
        }
        .justify-content-center {
            justify-content: center !important;
        }
        /* Estilos específicos para evitar que el navbar corte el contenido */
        .main-content {
            margin-top: 20px; /* Espacio adicional arriba */
            padding-bottom: 80px; /* Espacio adicional abajo */
        }
        .container {
            margin-top: 20px;
            margin-bottom: 40px;
        }
        /* Para formularios muy largos */
        .card {
            margin-bottom: 40px;
        }
        /* Responsive: más espacio en móviles */
        @media (max-width: 768px) {
            .main-content {
                padding-bottom: 100px;
            }
            .container {
                margin-top: 30px;
                margin-bottom: 50px;
            }
        }

        /* ===== ESTILOS PARA EL NAVBAR ===== */
        /* Global fix: prevenir scroll lateral */
        * {
            box-sizing: border-box;
        }

        /* Estilo base de la navbar */
        .navbar {
            width: 100%;
            background-color: #007BFF;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 1050 !important;
            overflow: visible;
            max-width: 100vw;
            min-height: 80px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            box-sizing: border-box;
            border: none !important;
            margin: 0 !important;
        }

        /* Logo o título */
        .navbar .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Imagen del logo */
        .navbar .logo img {
            height: 50px;
            width: auto;
            max-width: 50px;
            border-radius: 6px;
            object-fit: contain;
        }

        /* Texto del logo */
        .navbar .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
        }

        /* Contenedor de enlaces - Desktop */
        .navbar .nav-links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        /* Enlaces */
        .navbar a,
        .navbar button {
            color: white;
            background-color: transparent;
            border: none;
            font-weight: 600;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.3s;
            cursor: pointer;
        }

        /* Hover */
        .navbar a:hover,
        .navbar button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Asegurar que los botones no empujen hacia los lados */
        .navbar form {
            margin: 0;
        }

        /* ===== HAMBURGER MENU STYLES ===== */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
            background: none;
            border: none;
            z-index: 1051;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background-color: white;
            margin: 3px 0;
            transition: 0.3s;
            border-radius: 2px;
        }

        /* Animación del hamburger cuando está activo */
        .hamburger.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        /* ===== FIX PARA FORMULARIOS LARGOS ===== */
        /* Ajustar el contenido principal para que no se superponga con el navbar */
        .main-content {
            margin-top: 0 !important;
            padding-top: 100px !important;
            padding-bottom: 60px !important;
            min-height: calc(100vh - 100px) !important;
        }

        /* Contenedores adicionales de espaciado */
        .main-content .container {
            padding-top: 20px !important;
            padding-bottom: 40px !important;
        }

        /* Cards con margen adicional */
        .main-content .card {
            margin-top: 20px !important;
            margin-bottom: 40px !important;
        }

        /* Formularios largos necesitan espacio extra */
        .card-body {
            padding: 2rem !important;
        }

        .form-group {
            margin-bottom: 1.5rem !important;
        }

        /* Prevenir scroll horizontal en toda la aplicación */
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            position: relative;
        }
        
        /* Asegurar que el body tenga suficiente padding-top */
        body {
            padding-top: 80px;
        }

        /* Responsive para móviles */
        @media (max-width: 768px) {
            body {
                padding-top: 0 !important;
            }
            
            .navbar {
                padding: 15px 20px;
                height: 80px !important;
                align-items: center;
            }

            /* Mostrar hamburger en móviles */
            .hamburger {
                display: flex;
            }

            /* Ocultar nav-links por defecto en móviles */
            .navbar .nav-links {
                position: fixed;
                top: 80px;
                left: 0;
                width: 100%;
                height: calc(100vh - 80px);
                background-color: #007BFF;
                flex-direction: column;
                justify-content: flex-start;
                align-items: stretch;
                padding: 20px;
                gap: 0;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                overflow-y: auto;
                z-index: 1049;
            }

            /* Mostrar nav-links cuando están activos */
            .navbar .nav-links.active {
                transform: translateX(0);
            }

            .navbar .nav-links a,
            .navbar .nav-links button {
                width: 100%;
                text-align: left;
                padding: 15px 20px;
                font-size: 1rem;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                border-radius: 0;
                margin: 0;
            }

            .navbar .nav-links form {
                width: 100%;
            }

            .navbar .nav-links form button {
                width: 100%;
                text-align: left;
                background-color: rgba(255,255,255,0.1);
                margin-top: 10px;
            }

            /* Reducir espacio del contenido principal */
            .main-content {
                padding-top: 90px !important;
                padding-bottom: 60px !important;
            }
            
            .main-content .container {
                padding-top: 20px !important;
                padding-bottom: 30px !important;
            }
            
            .card-body {
                padding: 1.5rem !important;
            }
        }

        @media (max-width: 576px) {
            body {
                padding-top: 0 !important;
            }
            
            .navbar {
                padding: 12px 15px;
                height: 80px !important;
            }
            
            .navbar .logo img {
                height: 45px;
                max-width: 45px;
            }
            
            .hamburger {
                padding: 8px;
            }
            
            .hamburger span {
                width: 22px;
                height: 2px;
            }
            
            .navbar .nav-links a,
            .navbar .nav-links button {
                padding: 12px 15px;
                font-size: 0.95rem;
            }
            
            .main-content {
                padding-top: 100px !important;
                padding-bottom: 60px !important;
            }
            
            .form-group {
                margin-bottom: 1rem !important;
            }
        }

        /* ===== ESTILOS PARA MENSAJES DE ALERTA ===== */
        .mensaje.ok {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            margin: 20px 0;
            border-radius: 6px;
            border: 1px solid #c3e6cb;
            text-align: center;
        }

        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            margin: 20px 0;
            border-radius: 6px;
            border: 1px solid #f5c6cb;
            text-align: center;
        }

        /* ===== ESTILOS PARA TABLAS ===== */
        .tabla-contenedor {
            margin: 20px auto;
            max-width: 1200px;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .tabla-mercancias {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .tabla-mercancias th,
        .tabla-mercancias td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .tabla-mercancias th {
            background-color: #007BFF;
            color: white;
            font-weight: 600;
        }

        .tabla-mercancias tr:hover {
            background-color: #f8f9fa;
        }

        .btn-ver {
            display: inline-block;
            background-color: #007BFF;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            margin: 2px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-ver:hover {
            background-color: #0056b3;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="{{ route('dashboard') }}" class="logo">
            <img src="{{ asset('img/Logo.jpeg') }}" alt="Logo">
        </a>

        <!-- Botón hamburger para móviles -->
        <button class="hamburger" id="hamburger" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="nav-links" id="navLinks">
            <a href="{{ route('clientes.index') }}">Clientes</a>
            <a href="{{ route('pedidos.index') }}">Pedidos</a>
            <a href="{{ route('facturas.index') }}">Facturas</a>
            @superadmin
                <a href="{{ route('mercancias.index') }}">Mercancías</a>
                <a href="{{ route('users.index') }}">Gestión de Usuarios</a>
            @endsuperadmin
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="contenido @yield('content-class', '')">
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript para el menú hamburger -->
    <script>
        function toggleMenu() {
            const hamburger = document.getElementById('hamburger');
            const navLinks = document.getElementById('navLinks');
            
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        }
        
        // Cerrar el menú al hacer clic en un enlace (móviles)
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-links a, .nav-links button');
            const hamburger = document.getElementById('hamburger');
            const navLinksContainer = document.getElementById('navLinks');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        hamburger.classList.remove('active');
                        navLinksContainer.classList.remove('active');
                    }
                });
            });
            
            // Cerrar menú al redimensionar ventana
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    hamburger.classList.remove('active');
                    navLinksContainer.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>

