# Cómo Exportar Certificado Digital desde Windows

## Método 1: Desde el Navegador (Chrome/Edge)

### Paso 1: Acceder a la configuración del certificado
1. Abre Chrome o Edge
2. Ve a `Configuración` → `Privacidad y seguridad` → `Seguridad` → `Administrar certificados`
3. En la pestaña "Personal", busca tu certificado del SII

### Paso 2: Exportar el certificado
1. Selecciona tu certificado del SII
2. Haz clic en "Exportar..."
3. Selecciona el formato "Intercambio de información personal - PKCS #12 (.P12)"
4. Marca la opción "Incluir todas las claves en la ruta de certificación"
5. Establece una contraseña fuerte para el archivo
6. Guarda el archivo como `certificado.p12`

## Método 2: Desde Internet Explorer (si usaste IE para descargar)

### Paso 1: Abrir Internet Explorer
1. Abre Internet Explorer
2. Ve a `Herramientas` → `Opciones de Internet`
3. Pestaña "Contenido" → "Certificados"

### Paso 2: Exportar
1. En la pestaña "Personal", selecciona tu certificado
2. Clic en "Exportar..."
3. Asistente de exportación:
   - Selecciona "Sí, exportar la clave privada"
   - Formato: "Intercambio de información personal - PKCS #12 (.PFX)"
   - Marca "Incluir todos los certificados en la ruta de certificación"
   - Establece contraseña
   - Guarda como `certificado.p12`

## Método 3: Usando MMC (Microsoft Management Console)

### Paso 1: Abrir MMC
1. Presiona `Win + R`, escribe `mmc` y presiona Enter
2. Ve a `Archivo` → `Agregar o quitar complemento`
3. Selecciona "Certificados" → "Agregar"
4. Selecciona "Mi cuenta de usuario" → "Finalizar"

### Paso 2: Exportar certificado
1. Navega a `Certificados - Usuario actual` → `Personal` → `Certificados`
2. Encuentra tu certificado del SII
3. Clic derecho → "Todas las tareas" → "Exportar..."
4. Sigue el asistente como en el Método 2

## Verificar el Certificado

Una vez exportado, puedes verificar que el certificado es válido usando PowerShell:

```powershell
# Verificar información del certificado
$cert = New-Object System.Security.Cryptography.X509Certificates.X509Certificate2("ruta\al\certificado.p12", "contraseña")
Write-Host "Emisor: " $cert.Issuer
Write-Host "Sujeto: " $cert.Subject
Write-Host "Válido desde: " $cert.NotBefore
Write-Host "Válido hasta: " $cert.NotAfter
Write-Host "Tiene clave privada: " $cert.HasPrivateKey
```

## Notas Importantes

- **Contraseña**: Recuerda la contraseña que estableciste, la necesitarás en Laravel
- **Seguridad**: El archivo .p12 contiene tu clave privada, manténlo seguro
- **Vigencia**: Verifica que el certificado esté vigente
- **Backup**: Haz una copia de seguridad del archivo en un lugar seguro
