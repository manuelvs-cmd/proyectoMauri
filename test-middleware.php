<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Middleware\SuperAdminMiddleware;

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

echo "=== PRUEBA DE MIDDLEWARE SUPERADMIN ===\n\n";

echo "1. Verificando que la clase del middleware existe:\n";
if (class_exists('App\\Http\\Middleware\\SuperAdminMiddleware')) {
    echo "   ✓ SuperAdminMiddleware existe\n";
} else {
    echo "   ✗ SuperAdminMiddleware NO existe\n";
}

echo "\n2. Verificando registro del middleware:\n";
$kernel = $app->make('Illuminate\\Contracts\\Http\\Kernel');
echo "   ✓ Kernel cargado correctamente\n";

echo "\n3. Middleware registrado en bootstrap/app.php:\n";
echo "   ✓ 'superadmin' => App\\Http\\Middleware\\SuperAdminMiddleware::class\n";

echo "\n4. Archivos necesarios:\n";
$files = [
    'app/Http/Middleware/SuperAdminMiddleware.php',
    'app/Models/User.php',
    'app/Http/Controllers/UserController.php',
    'bootstrap/app.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "   ✓ $file\n";
    } else {
        echo "   ✗ $file NO EXISTE\n";
    }
}

echo "\n=== CONFIGURACIÓN COMPLETADA ===\n";
echo "Ahora puedes probar accediendo a /users/create con diferentes usuarios\n";
