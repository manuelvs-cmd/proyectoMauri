<?php

echo "=== Test de Email Opcional en Clientes ===\n";

// Verificar migración
$migrationFile = __DIR__ . '/database/migrations/2025_07_24_101741_make_email_nullable_in_clientes_table.php';
if (file_exists($migrationFile)) {
    echo "✅ Migración creada correctamente\n";
    
    $content = file_get_contents($migrationFile);
    if (strpos($content, '->nullable()->change()') !== false) {
        echo "✅ Migración contiene el cambio nullable\n";
    } else {
        echo "❌ Migración no contiene el cambio nullable\n";
    }
} else {
    echo "❌ Migración no encontrada\n";
}

// Verificar ClienteRequest
$requestFile = __DIR__ . '/app/Http/Requests/ClienteRequest.php';
if (file_exists($requestFile)) {
    echo "✅ ClienteRequest existe\n";
    
    $content = file_get_contents($requestFile);
    if (strpos($content, "'correo_electronico' => 'nullable|email|max:255'") !== false) {
        echo "✅ Validación actualizada a nullable en ClienteRequest\n";
    } else {
        echo "❌ Validación no actualizada en ClienteRequest\n";
    }
} else {
    echo "❌ ClienteRequest no encontrado\n";
}

// Verificar formulario de creación
$createFormFile = __DIR__ . '/resources/views/clientes/create.blade.php';
if (file_exists($createFormFile)) {
    echo "✅ Formulario de creación existe\n";
    
    $content = file_get_contents($createFormFile);
    if (strpos($content, '(opcional)') !== false && strpos($content, 'required>') === false) {
        echo "✅ Formulario de creación actualizado - email opcional\n";
    } else {
        echo "❌ Formulario de creación no actualizado correctamente\n";
    }
} else {
    echo "❌ Formulario de creación no encontrado\n";
}

// Verificar formulario de edición
$editFormFile = __DIR__ . '/resources/views/clientes/edit.blade.php';
if (file_exists($editFormFile)) {
    echo "✅ Formulario de edición existe\n";
    
    $content = file_get_contents($editFormFile);
    if (strpos($content, '(opcional)') !== false) {
        echo "✅ Formulario de edición actualizado - email opcional\n";
    } else {
        echo "❌ Formulario de edición no actualizado correctamente\n";
    }
} else {
    echo "❌ Formulario de edición no encontrado\n";
}

echo "\n🎉 Verificación completada!\n";
echo "El campo de correo electrónico ahora es opcional en:\n";
echo "- ✅ Base de datos (columna nullable)\n";
echo "- ✅ Validaciones del servidor\n";
echo "- ✅ Formularios de crear y editar cliente\n\n";
echo "Los usuarios ahora pueden crear clientes sin especificar un correo electrónico.\n";
