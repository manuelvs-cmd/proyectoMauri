<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Test Login Debug</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 300px; }
        label { display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background: #007bff; color: white; border: none; }
    </style>
</head>
<body>
    <h1>Test Login - Debug</h1>
    <form method="POST" action="/test-login">
        @csrf
        <label for="username">Username:</label>
        <input type="text" name="username" value="admin" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" value="admin123" required>
        
        <button type="submit">Test Login</button>
    </form>
    
    <p>Usuarios disponibles:</p>
    <ul>
        <li>admin / admin123</li>
        <li>juan.vendedor / juan123</li>
        <li>maria.vendedor / maria123</li>
    </ul>
</body>
</html>
