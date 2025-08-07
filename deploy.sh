#!/bin/bash

# Script de despliegue para Laravel
echo "🚀 Iniciando despliegue..."

# 1. Instalar dependencias de PHP
echo "📦 Instalando dependencias de PHP..."
composer install --no-dev --optimize-autoloader

# 2. Instalar dependencias de Node.js
echo "📦 Instalando dependencias de Node.js..."
npm ci

# 3. Compilar assets
echo "🔨 Compilando assets..."
npm run build

# 4. Limpiar cache
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Cache de configuración y rutas (solo en producción)
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache

# 6. Ejecutar migraciones (opcional, descomenta si necesitas)
# echo "🗄️ Ejecutando migraciones..."
# php artisan migrate --force

echo "✅ ¡Despliegue completado!"
echo ""
echo "📋 Tareas post-despliegue:"
echo "   - Verifica que el archivo .env esté configurado correctamente"
echo "   - Asegúrate de que los permisos de storage/ y bootstrap/cache/ sean 775"
echo "   - Verifica que APP_ENV=production en tu .env"
