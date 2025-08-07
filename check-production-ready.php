<?php

echo "=== VERIFICACIÓN DE PREPARACIÓN PARA PRODUCCIÓN ===\n\n";

// Verificar versión de PHP
echo "1. Verificando PHP:\n";
$phpVersion = phpversion();
echo "   Versión actual: PHP $phpVersion\n";
if (version_compare($phpVersion, '8.2.0', '>=')) {
    echo "   ✅ PHP 8.2+ requerido para Laravel 12\n";
} else {
    echo "   ❌ PHP 8.2+ requerido (actual: $phpVersion)\n";
}

// Verificar extensiones PHP requeridas
echo "\n2. Verificando extensiones PHP:\n";
$required_extensions = [
    'openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'curl'
];

foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ $ext\n";
    } else {
        echo "   ❌ $ext (REQUERIDA)\n";
    }
}

// Verificar archivos de configuración
echo "\n3. Verificando configuración:\n";
$config_files = [
    '.env' => 'Archivo de configuración principal',
    'app/Http/Kernel.php' => 'Kernel de middleware',
    'config/app.php' => 'Configuración de aplicación',
    'config/database.php' => 'Configuración de base de datos'
];

foreach ($config_files as $file => $description) {
    if (file_exists($file)) {
        echo "   ✅ $file ($description)\n";
    } else {
        echo "   ❌ $file ($description) - NO EXISTE\n";
    }
}

// Verificar permisos de directorios
echo "\n4. Verificando permisos de directorios:\n";
$writable_dirs = [
    'storage/app',
    'storage/framework',
    'storage/logs',
    'bootstrap/cache'
];

foreach ($writable_dirs as $dir) {
    if (is_dir($dir) && is_writable($dir)) {
        echo "   ✅ $dir (escribible)\n";
    } else {
        echo "   ❌ $dir (no escribible o no existe)\n";
    }
}

// Verificar dependencias
echo "\n5. Verificando dependencias:\n";
if (file_exists('vendor/autoload.php')) {
    echo "   ✅ Composer dependencies instaladas\n";
} else {
    echo "   ❌ Ejecutar 'composer install' para instalar dependencias\n";
}

// Verificar clave de aplicación
echo "\n6. Verificando configuración de seguridad:\n";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    if (strpos($env_content, 'APP_KEY=') !== false && strpos($env_content, 'APP_KEY=base64:') !== false) {
        echo "   ✅ APP_KEY configurada\n";
    } else {
        echo "   ❌ APP_KEY no configurada - ejecutar 'php artisan key:generate'\n";
    }
    
    if (strpos($env_content, 'APP_DEBUG=true') !== false) {
        echo "   ⚠️  APP_DEBUG=true (cambiar a false en producción)\n";
    } else {
        echo "   ✅ APP_DEBUG configurado para producción\n";
    }
} else {
    echo "   ❌ Archivo .env no existe\n";
}

// Verificar base de datos
echo "\n7. Verificando base de datos:\n";
if (file_exists('database/database.sqlite')) {
    echo "   ✅ SQLite database existe\n";
    echo "   ⚠️  Recomendado: migrar a MySQL en producción\n";
} else {
    echo "   ❌ Database no encontrada\n";
}

// Calcular tamaño de la aplicación
echo "\n8. Análisis de tamaño:\n";
$totalSize = 0;
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
$fileCount = 0;
foreach ($iterator as $file) {
    if ($file->isFile()) {
        $totalSize += $file->getSize();
        $fileCount++;
    }
}
$sizeInMB = round($totalSize / (1024 * 1024), 2);
echo "   📊 Tamaño total: {$sizeInMB} MB\n";
echo "   📊 Archivos: $fileCount\n";

// Recomendaciones finales
echo "\n=== RECOMENDACIONES PARA PRODUCCIÓN ===\n\n";
echo "💾 Almacenamiento mínimo: 3-5 GB SSD\n";
echo "🧠 RAM mínima: 1 GB\n";
echo "⚡ CPU mínima: 1 vCore\n";
echo "📊 Base de datos: MySQL 8.0+ recomendado\n";
echo "💰 Costo estimado: $6-12/mes\n";
echo "🏢 Proveedor recomendado: DigitalOcean, Linode, o Vultr\n\n";

echo "🔧 PASOS ANTES DE SUBIR A PRODUCCIÓN:\n";
echo "1. Configurar .env para producción\n";
echo "2. Cambiar APP_DEBUG=false\n";
echo "3. Configurar base de datos MySQL\n";
echo "4. Ejecutar 'php artisan migrate'\n";
echo "5. Ejecutar 'php artisan config:cache'\n";
echo "6. Ejecutar 'php artisan route:cache'\n";
echo "7. Configurar SSL/HTTPS\n";
echo "8. Configurar backups automáticos\n\n";

echo "=== VERIFICACIÓN COMPLETADA ===\n";
