# Script de Configuracion SII para Laravel
# ========================================

Write-Host "Configuracion del SII (Servicio de Impuestos Internos)" -ForegroundColor Cyan
Write-Host "=======================================================" -ForegroundColor Cyan
Write-Host ""

# 1. Verificar que estamos en el directorio correcto
if (!(Test-Path "artisan")) {
    Write-Host "Error: Este script debe ejecutarse desde la raiz del proyecto Laravel" -ForegroundColor Red
    exit 1
}

Write-Host "Paso 1: Configuracion inicial" -ForegroundColor Yellow
Write-Host "------------------------------" -ForegroundColor Yellow

# 2. Crear archivo .env.sii si no existe
if (!(Test-Path ".env.sii")) {
    Write-Host "Creando archivo .env.sii desde plantilla..." -ForegroundColor Green
    Copy-Item ".env.sii.example" ".env.sii"
    Write-Host "Archivo .env.sii creado" -ForegroundColor Green
} else {
    Write-Host "Archivo .env.sii ya existe" -ForegroundColor Green
}

# 3. Crear directorios necesarios
Write-Host ""
Write-Host "Creando directorios necesarios..." -ForegroundColor Green

$directories = @(
    "storage\certificates",
    "storage\folios", 
    "storage\dte_enviados"
)

foreach ($dir in $directories) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Host "Directorio creado: $dir" -ForegroundColor Green
    } else {
        Write-Host "Directorio ya existe: $dir" -ForegroundColor Green
    }
}

# 4. Verificar configuración
Write-Host ""
Write-Host "Verificando configuracion..." -ForegroundColor Yellow
Write-Host "----------------------------" -ForegroundColor Yellow

php artisan sii:check-config

Write-Host ""
Write-Host "Proximos pasos:" -ForegroundColor Cyan
Write-Host "===============" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Edita el archivo .env.sii con los datos de tu empresa:" -ForegroundColor White
Write-Host "   - RUT de empresa" -ForegroundColor Gray
Write-Host "   - Razon social" -ForegroundColor Gray
Write-Host "   - Giro comercial" -ForegroundColor Gray
Write-Host "   - Direccion fiscal" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Obten tu certificado digital del SII:" -ForegroundColor White
Write-Host "   - Visita: https://www.sii.cl/servicios_online/" -ForegroundColor Gray
Write-Host "   - Descarga el certificado .p12" -ForegroundColor Gray
Write-Host "   - Colocalo en: storage\certificates\" -ForegroundColor Gray
Write-Host ""
Write-Host "3. Solicita folios CAF del SII:" -ForegroundColor White
Write-Host "   - Facturas Electronicas (Tipo 33)" -ForegroundColor Gray
Write-Host "   - Coloca archivos .xml en: storage\folios\" -ForegroundColor Gray
Write-Host ""
Write-Host "4. Verifica la configuracion:" -ForegroundColor White
Write-Host "   php artisan sii:check-config" -ForegroundColor Gray
Write-Host ""
Write-Host "5. Prueba en ambiente de certificacion antes de produccion" -ForegroundColor White
Write-Host ""
Write-Host "Para mas detalles, consulta: docs\configuracion_sii.md" -ForegroundColor Cyan
Write-Host ""
