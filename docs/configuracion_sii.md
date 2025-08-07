# 🏛️ Guía de Configuración SII Chile

## 📋 **Paso 1: Datos de tu Empresa**

### 🔍 **Dónde encontrar tus datos:**
1. **RUT de empresa**: En tu iniciación de actividades
2. **Razón Social**: Nombre oficial de tu empresa según registro SII
3. **Giro**: Actividad económica registrada en el SII
4. **Dirección fiscal**: Dirección registrada en el SII

### 📝 **Cómo configurar:**
```bash
# 1. Copia el archivo de ejemplo
cp .env.sii.example .env.sii

# 2. Edita el archivo con tus datos reales
SII_EMISOR_RUT=76123456-7                    # Tu RUT real
SII_EMISOR_RAZON_SOCIAL="Mi Empresa Real SpA"
SII_EMISOR_GIRO="Venta al por menor de productos varios"
# ... etc
```

---

## 🔐 **Paso 2: Certificado Digital**

### 📥 **Obtener Certificado Digital:**

1. **Solicitar en SII:**
   - Ingresa a: https://www.sii.cl/servicios_online/
   - Ve a "Facturación Electrónica" → "Certificados Digitales"
   - Solicita un certificado para facturación electrónica

2. **Descargar certificado:**
   - El SII te enviará un archivo `.p12` (PKCS#12)
   - También recibirás la contraseña del certificado

### 📁 **Instalar Certificado:**

```bash
# 1. Copia tu certificado a la carpeta del proyecto
cp /ruta/del/certificado.p12 storage/certificates/

# 2. Actualiza la configuración
SII_CERTIFICADO_PATH="storage/certificates/certificado.p12"
SII_CERTIFICADO_PASSWORD="tu_password_real"
```

### 🔒 **Seguridad del Certificado:**
- ❌ **NUNCA** subas el certificado a control de versiones
- ✅ Agrega `*.p12` al `.gitignore`
- ✅ Guarda backup del certificado en lugar seguro
- ✅ La contraseña debe ser fuerte

---

## 📄 **Paso 3: Folios CAF (Código de Autorización de Folios)**

### 📥 **Solicitar Folios:**

1. **Ingresa al SII:**
   - https://www.sii.cl/servicios_online/
   - "Facturación Electrónica" → "Autorización de Folios"

2. **Solicita folios para:**
   - Facturas Electrónicas (Tipo 33)
   - Boletas Electrónicas (Tipo 39) - si las usas
   - Notas de Crédito (Tipo 61) - si las usas

3. **Descarga archivos CAF:**
   - El SII genera archivos `.xml` con autorización
   - Descarga cada archivo CAF

### 📁 **Instalar Folios:**

```bash
# Copia los archivos CAF a la carpeta
cp *.xml storage/folios/

# Estructura esperada:
storage/folios/
├── folio_33_1_100.xml      # Facturas 1-100
├── folio_39_1_50.xml       # Boletas 1-50
└── folio_61_1_20.xml       # Notas de crédito 1-20
```

---

## 🧪 **Paso 4: Ambiente de Certificación**

### 🔧 **Configurar para Pruebas:**

```bash
# En .env.sii
SII_AMBIENTE=certificacion
```

### 🧪 **URLs de Certificación:**
- **Envío DTE:** https://palena.sii.cl/cgi_dte/UPL/DTEUpload
- **Consultas:** https://maullin.sii.cl/DTEWS/

### ✅ **Probar Conexión:**
```bash
# Ejecutar comando de prueba
php artisan sii:test-connection
```

---

## 🚀 **Paso 5: Ambiente de Producción**

### ⚠️ **Solo cuando todo funcione en certificación:**

```bash
# En .env.sii
SII_AMBIENTE=produccion
```

### 🌐 **URLs de Producción:**
- **Envío DTE:** https://sii.cl/cgi_dte/UPL/DTEUpload
- **Consultas:** https://sii.cl/DTEWS/

---

## 🔍 **Verificación de Configuración**

### ✅ **Checklist Pre-Envío:**
- [ ] Datos de empresa correctos y actualizados
- [ ] Certificado digital válido y password correcto
- [ ] Folios CAF descargados y en carpeta correcta
- [ ] Ambiente configurado (certificacion/produccion)
- [ ] Conexión a internet estable

### 🛠️ **Comandos de Verificación:**
```bash
# Verificar configuración
php artisan sii:check-config

# Ver estado de folios
php artisan sii:status-folios

# Probar envío (ambiente certificación)
php artisan sii:test-send
```

---

## 🚨 **Errores Comunes y Soluciones**

### ❌ **Error: "Certificado inválido"**
- Verifica que el archivo `.p12` esté en la ruta correcta
- Confirma que la contraseña sea correcta
- Asegúrate que el certificado no haya expirado

### ❌ **Error: "Folio agotado"**
- Solicita nuevos folios en el SII
- Verifica que los archivos CAF estén en `storage/folios/`

### ❌ **Error: "Datos del emisor incorrectos"**
- Confirma que el RUT esté con formato correcto: `12345678-9`
- Verifica que la razón social coincida exactamente con el registro SII

### ❌ **Error: "Conexión rechazada"**
- Verifica conexión a internet
- Confirma que las URLs sean correctas según el ambiente
- Revisa logs en `storage/logs/laravel.log`

---

## 📞 **Soporte y Recursos**

### 🌐 **Documentación Oficial:**
- [Portal SII](https://www.sii.cl/factura_electronica/)
- [Documentación Técnica DTE](https://www.sii.cl/factura_electronica/factura_mercado/instructivo_instalacion.htm)

### 📧 **Soporte SII:**
- Email: dteconsultas@sii.cl
- Teléfono: 600 343 4000

### 🛠️ **Logs del Sistema:**
```bash
# Ver logs de SII en tiempo real
tail -f storage/logs/laravel.log | grep SII
```
