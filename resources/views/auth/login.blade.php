<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Gestión</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('img/Logo.jpeg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('img/Logo.jpeg') }}">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }
        
        /* Elementos decorativos de fondo */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: movePattern 20s linear infinite;
            z-index: 1;
        }
        
        @keyframes movePattern {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        /* Contenedor principal del login */
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 15px 12px rgba(0, 0, 0, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 60px 50px;
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 2;
            transform: translateY(0);
            transition: all 0.3s ease;
        }
        
        .login-container:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.15),
                0 20px 15px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
        }
        
        /* Logo y encabezado */
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-container {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            width: 200px;
            height: 200px;
        }
        
        .logo-container img {
            width: 200px;
            height: 200px;
            border-radius: 0;
            object-fit: contain;
            object-position: center;
            background-color: transparent;
            padding: 0;
            box-shadow: none;
            border: none;
            transition: all 0.3s ease;
            /* Asegurar que el logo se centre y mantenga sus proporciones */
            max-width: 200px;
            max-height: 200px;
            margin: auto;
            display: block;
        }
        
        .logo-container:hover img {
            transform: scale(1.1);
            filter: drop-shadow(0 10px 20px rgba(0, 123, 255, 0.3));
        }
        
        .login-title {
            color: #2c3e50;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #007BFF, #0056b3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .login-subtitle {
            color: #6c757d;
            font-size: 1rem;
            font-weight: 400;
            margin-bottom: 0;
        }
        
        /* Formulario */
        .login-form {
            margin-top: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-label {
            color: #495057;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 8px;
            display: block;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1.1rem;
            z-index: 3;
            transition: color 0.3s ease;
        }
        
        .form-control {
            background: rgba(248, 249, 250, 0.8);
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px 20px 15px 50px;
            font-size: 1rem;
            font-weight: 400;
            color: #495057;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .form-control::placeholder {
            color: #adb5bd;
            font-weight: 400;
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: #007BFF;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            outline: none;
            transform: translateY(-1px);
        }
        
        .form-control:focus + .input-icon {
            color: #007BFF;
        }
        
        /* Botón de login */
        .btn-login {
            background: linear-gradient(135deg, #007BFF, #0056b3);
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* Mensajes de error y éxito */
        .alert {
            border-radius: 12px;
            padding: 15px 20px;
            margin-top: 20px;
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        /* Elementos decorativos */
        .decorative-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-shape:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }
        
        .floating-shape:nth-child(3) {
            width: 40px;
            height: 40px;
            bottom: 20%;
            left: 15%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(10px) rotate(240deg); }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            .login-container {
                padding: 40px 30px;
                border-radius: 16px;
            }
            
            .logo-container {
                width: 150px;
                height: 150px;
            }
            
            .logo-container img {
                width: 150px;
                height: 150px;
                padding: 0;
                max-width: 150px;
                max-height: 150px;
                border-radius: 0;
                background-color: transparent;
                box-shadow: none;
                border: none;
            }
            
            .login-title {
                font-size: 1.75rem;
            }
            
            .form-control {
                padding: 12px 18px 12px 45px;
                font-size: 0.95rem;
            }
            
            .input-icon {
                left: 12px;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
            }
            
            .logo-container {
                width: 120px;
                height: 120px;
            }
            
            .logo-container img {
                width: 120px;
                height: 120px;
                max-width: 120px;
                max-height: 120px;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
            
            .login-subtitle {
                font-size: 0.9rem;
            }
        }
        
        /* Animación de entrada */
        .login-container {
            animation: slideInUp 0.6s ease-out;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Elementos decorativos flotantes -->
    <div class="decorative-elements">
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>
    
    <div class="login-container">
        <!-- Encabezado con logo -->
        <div class="login-header">
            <div class="logo-container">
                <img src="{{ asset('img/Logo.jpeg') }}" alt="Logo Sistema de Gestión">
            </div>
            <h1 class="login-title">Bienvenido</h1>
            <p class="login-subtitle">Sistema de Gestión Empresarial</p>
        </div>
        
        <!-- Formulario de login -->
        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf
            
            <div class="form-group">
                <label for="username" class="form-label">
                    <i class="fas fa-user"></i> Nombre de usuario
                </label>
                <div class="input-group">
                    <input 
                        type="text" 
                        name="username" 
                        id="username"
                        class="form-control" 
                        value="{{ old('username') }}" 
                        placeholder="Ingresa tu nombre de usuario"
                        required 
                        autocomplete="username"
                        autofocus
                    >
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Contraseña
                </label>
                <div class="input-group">
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        class="form-control" 
                        placeholder="Ingresa tu contraseña"
                        required 
                        autocomplete="current-password"
                    >
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Iniciar Sesión
            </button>
            
            <!-- Mensajes de error -->
            @error('username')
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ $message }}
                </div>
            @enderror
            
            <!-- Mensajes de éxito -->
            @if (session('ok'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('ok') }}
                </div>
            @endif
        </form>
    </div>
    
    <!-- JavaScript para mejorar la experiencia -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Focus automático en el campo de usuario si está vacío
            const usernameInput = document.getElementById('username');
            if (usernameInput && !usernameInput.value) {
                setTimeout(() => {
                    usernameInput.focus();
                }, 300);
            }
            
            // Efecto de escritura en el placeholder
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
            
            // Prevenir múltiples envíos del formulario
            const form = document.querySelector('.login-form');
            const submitBtn = document.querySelector('.btn-login');
            
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';
                
                // Rehabilitar el botón después de 3 segundos en caso de error
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Iniciar Sesión';
                }, 3000);
            });
            
            // Enter en el campo de usuario pasa al campo de contraseña
            usernameInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('password').focus();
                }
            });
        });
    </script>
</body>
</html>
