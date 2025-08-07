# 🏛️ Configuración SII - Guía Rápida

## 🚀 **Instalación Rápida**

### **1. Ejecutar Script de Configuración:**
```powershell
# En Windows (PowerShell)
.\scripts\setup-sii.ps1

# O manualmente:
cp .env.sii.example .env.sii
php artisan sii:check-config
```

### **2. Configurar Datos de tu Empresa:**
Edita el archivo `.env.sii` con los datos reales de tu empresa:

```env
# Datos de tu empresa (OBLIGATORIOS)
SII_EMISOR_RUT=76123456-7
SII_EMISOR_RAZON_SOCIAL="Mi Empresa Real SpA"
SII_EMISOR_GIRO="Venta al por menor de productos"
SII_EMISOR_DIRECCION="Av. Libertador 1234"
SII_EMISOR_COMUNA="Santiago"
SII_EMISOR_CIUDAD="Santiago"
SII_EMISOR_TELEFONO="+56 2 2345 6789"
SII_EMISOR_EMAIL="facturacion@miempresa.cl"
```

### **3. Instalar Certificado Digital:**
```bash
# 1. Descargar certificado del SII (archivo .p12)
# 2. Copiarlo a la carpeta del proyecto:
copy certificado.p12 storage/certificates/

# 3. Configurar en .env.sii:
SII_CERTIFICADO_PATH="storage/certificates/certificado.p12"
SII_CERTIFICADO_PASSWORD="password_del_certificado"
```

### **4. Instalar Folios CAF:**
```bash
# 1. Solicitar folios en: https://www.sii.cl/servicios_online/
# 2. Descargar archivos .xml
# 3. Copiarlos a:
copy *.xml storage/folios/
```

## ✅ **Verificación**

```bash
# Verificar configuración completa
php artisan sii:check-config

# Si todo está OK, verás:
# 🎉 ¡Configuración del SII parece correcta!
```

## 🧪 **Pruebas**

### **Ambiente de Certificación (Recomendado):**
```env
SII_AMBIENTE=certificacion
```

### **Enviar Factura de Prueba:**
```bash
# Desde la interfaz web:
# 1. Crear una factura
# 2. Hacer clic en "Enviar al SII"
# 3. Verificar el resultado en los logs
```

## 🚀 **Producción**

**⚠️ Solo cambiar a producción cuando las pruebas funcionen correctamente:**

```env
SII_AMBIENTE=produccion
```

## 🆘 **Problemas Comunes**

| Error | Solución |
|-------|----------|
| `Certificado inválido` | Verificar ruta y contraseña del certificado |
| `Folio agotado` | Solicitar nuevos folios en el SII |
| `Datos del emisor incorrectos` | Verificar que coincidan con registro SII |
| `Conexión rechazada` | Verificar internet y URLs según ambiente |

## 📞 **Soporte**

- 📖 **Documentación completa:** [`docs/configuracion_sii.md`](docs/configuracion_sii.md)
- 🌐 **Portal SII:** https://www.sii.cl/factura_electronica/
- 📧 **Soporte SII:** dteconsultas@sii.cl
- 📞 **Teléfono SII:** 600 343 4000

## 📂 **Estructura de Archivos**

```
proyecto/
├── .env.sii                 # Tu configuración SII (NO subir a Git)
├── .env.sii.example         # Plantilla de configuración
├── storage/
│   ├── certificates/        # Certificados digitales (.p12)
│   ├── folios/             # Folios CAF (.xml)
│   └── dte_enviados/       # Backup de documentos enviados
├── docs/
│   └── configuracion_sii.md # Documentación detallada
└── scripts/
    └── setup-sii.ps1      # Script de configuración automática
```

---

**🔒 Nota de Seguridad:** Los archivos de certificado y `.env.sii` están excluidos del control de versiones por seguridad.
