<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="form-container">
        <h2>Iniciar Sesión</h2>
        <form action="procesar_login.php" method="POST">
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Entrar">
        </form>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>

        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
