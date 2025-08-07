# 🏪 Sistema de Facturación Electrónica Laravel

> Sistema completo de facturación electrónica para Chile con integración SII, gestión de clientes, productos y reportes avanzados.

[![Laravel](https://img.shields.io/badge/Laravel-12.0-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![SII](https://img.shields.io/badge/SII-Chile-green.svg)](https://sii.cl)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

## 🚀 Características Principales

### 💰 **Sistema de Facturación**
- ✅ **Facturas Electrónicas** (Código 33 - SII Chile)
- ✅ **Boletas Electrónicas** (Código 39 - SII Chile) 
- ✅ **Generación automática de PDF** con diseño profesional
- ✅ **Numeración automática** de documentos
- ✅ **Cálculo automático de IVA** (19% Chile)
- ✅ **Estados de documento** (emitida, pagada, anulada)

### 🏛️ **Integración SII Chile**
- 🔐 **Certificados digitales** (.p12) con validación
- 📄 **Folios CAF** (Código de Autorización de Folios)
- ✍️ **Firma digital XML** con XMLSecLibs
- 🌐 **Envío automático** al SII
- 📊 **Seguimiento de estados** SII (pendiente, aceptado, rechazado)
- 🧪 **Ambiente de certificación** y producción
- 📦 **DTE** (Documentos Tributarios Electrónicos) completos

### 👥 **Gestión de Usuarios y Roles**
- 🔒 **Autenticación segura** con Laravel Auth
- 👤 **Sistema de roles** (SuperAdmin, Admin, Vendedor)
- 🎯 **Permisos granulares** con Spatie Laravel Permission
- 📊 **Dashboard personalizado** por rol

### 📋 **Gestión Comercial**
- 👨‍💼 **Clientes** con validación RUT chileno
- 📦 **Productos/Mercancías** con stock y precios
- 📝 **Pedidos** con múltiples productos
- 💼 **Comisiones** para vendedores
- 📈 **Reportes** en PDF

### 🎨 **Interfaz de Usuario**
- 📱 **Responsive design** con Bootstrap
- 🌍 **Localización en español**
- 🎯 **UX optimizada** para facturación
- 📋 **Tablas interactivas** con filtros

## 🛠️ Stack Técnico

### **Backend**
- **Laravel 12.0** - Framework PHP moderno
- **PHP 8.2+** - Últimas características de PHP
- **MySQL/SQLite** - Base de datos flexible

### **Librerías Especializadas**
- **`barryvdh/laravel-dompdf`** - Generación de PDF
- **`spatie/laravel-permission`** - Gestión de roles
- **`robrichards/xmlseclibs`** - Firma digital XML
- **`spatie/array-to-xml`** - Conversión a XML para SII
- **`guzzlehttp/guzzle`** - Cliente HTTP para SII

### **Frontend**
- **Bootstrap 5** - Framework CSS responsive
- **JavaScript ES6+** - Interacciones dinámicas
- **Blade Templates** - Motor de plantillas Laravel

## 🚀 Instalación Rápida

### **1. Requisitos Previos**
```bash
# Verificar versiones
php --version  # PHP 8.2+
composer --version
node --version # Node.js 16+
```

### **2. Clonar y Configurar**
```bash
# Clonar repositorio
git clone https://github.com/manuelvs-cmd/proyectoMauri.git
cd proyectoMauri

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
cp .env.sii.example .env.sii
php artisan key:generate
```

### **3. Base de Datos**
```bash
# SQLite (desarrollo rápido)
touch database/database.sqlite

# O MySQL (editar .env)
# DB_CONNECTION=mysql
# DB_DATABASE=tu_base_de_datos

# Ejecutar migraciones
php artisan migrate
php artisan db:seed
```

### **4. Configuración SII (Opcional)**
```bash
# Ejecutar asistente de configuración
php artisan sii:setup-wizard

# O configuración manual
php artisan sii:check-config
```

### **5. Iniciar Servidor**
```bash
# Desarrollo
php artisan serve
npm run dev

# La aplicación estará disponible en:
# http://localhost:8000
```

## 🏛️ Configuración SII Chile

### **📋 Requisitos SII**
1. **RUT de empresa** registrado en SII
2. **Certificado digital** (.p12) vigente
3. **Resolución SII** para facturación electrónica
4. **Folios CAF** solicitados al SII

### **⚙️ Configuración Rápida**
```bash
# Ejecutar script de configuración
./scripts/setup-sii.ps1  # Windows

# Configurar datos de empresa en .env.sii
SII_EMISOR_RUT=76123456-7
SII_EMISOR_RAZON_SOCIAL="Mi Empresa SpA"
SII_EMISOR_GIRO="Venta al por menor"

# Certificado digital
SII_CERTIFICADO_PATH=storage/certificates/certificado.p12
SII_CERTIFICADO_PASSWORD=mi_password_secreto

# Ambiente (certificacion | produccion)
SII_AMBIENTE=certificacion
```

### **📂 Archivos Necesarios**
```
storage/
├── certificates/
│   └── certificado.p12          # Tu certificado digital
├── folios/
│   ├── 33_folios_123-500.xml    # Folios para facturas
│   └── 39_folios_1001-2000.xml  # Folios para boletas
└── dte_enviados/                # Backup automático
```

### **✅ Verificación**
```bash
# Verificar configuración completa
php artisan sii:check-config

# Enviar factura de prueba
php artisan sii:test-send
```

## 👥 Usuarios por Defecto

```bash
# SuperAdmin
Usuario: admin
Contraseña: admin123

# Admin
Usuario: manager
Contraseña: manager123

# Vendedor
Usuario: vendedor
Contraseña: vendedor123
```

## 📊 Funcionalidades por Rol

| Función | SuperAdmin | Admin | Vendedor |
|---------|:----------:|:-----:|:--------:|
| Gestionar usuarios | ✅ | ❌ | ❌ |
| Ver todos los clientes | ✅ | ✅ | ❌ |
| Gestionar productos | ✅ | ✅ | ❌ |
| Crear pedidos | ✅ | ✅ | ✅ |
| Generar facturas | ✅ | ✅ | ✅ |
| Enviar al SII | ✅ | ✅ | ❌ |
| Ver reportes | ✅ | ✅ | ✅* |

*Los vendedores solo ven sus propios reportes*

## 📈 Reportes Disponibles

- 📊 **Dashboard ejecutivo** con métricas clave
- 💰 **Ventas por vendedor** con comisiones
- 📦 **Inventario y productos** más vendidos
- 👥 **Análisis de clientes** y segmentación
- 🏛️ **Estado SII** y documentos pendientes

## 🔧 Comandos Artisan Disponibles

### **SII Commands**
```bash
php artisan sii:check-config      # Verificar configuración
php artisan sii:setup-wizard      # Asistente configuración
php artisan sii:test-send         # Envío de prueba
php artisan facturas:enviar-sii   # Envío masivo facturas
```

### **User Management**
```bash
php artisan users:assign-role      # Asignar roles
php artisan users:list-roles       # Listar usuarios y roles
php artisan permissions:list       # Ver permisos disponibles
```

## 🌍 Producción

### **🚀 Despliegue**
```bash
# Optimizar para producción
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compilar assets
npm run production

# Configurar permisos
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### **⚙️ Variables de Entorno**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

# SII Producción
SII_AMBIENTE=produccion
```

## 🐛 Solución de Problemas

### **❌ Errores Comunes**

| Error | Causa | Solución |
|-------|-------|----------|
| `Certificado inválido` | Certificado expirado/incorrecto | Verificar fecha y contraseña |
| `Folio agotado` | Sin folios disponibles | Solicitar nuevos folios al SII |
| `Error de permisos` | Permisos de archivos | `chmod -R 755 storage/` |
| `Conexión SII` | URLs incorrectas | Verificar ambiente (cert/prod) |

### **📋 Logs Útiles**
```bash
# Ver logs de Laravel
tail -f storage/logs/laravel.log

# Logs específicos de SII
tail -f storage/logs/sii.log

# Limpiar cache si hay problemas
php artisan cache:clear
php artisan config:clear
```

## 📚 Documentación

- 📖 **[Configuración SII Detallada](docs/configuracion_sii.md)**
- 🔧 **[Guía de Instalación](RECURSOS_SERVIDOR.md)**
- 👥 **[Gestión de Roles](INSTRUCCIONES_ROLES.md)**
- 🚀 **[Guía Rápida SII](README_SII.md)**

## 🤝 Contribuir

1. Fork el repositorio
2. Crear feature branch (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la branch (`git push origin feature/nueva-funcionalidad`)
5. Abrir Pull Request

## 📞 Soporte

- 💬 **Issues**: [GitHub Issues](https://github.com/manuelvs-cmd/proyectoMauri/issues)
- 📧 **Email**: manuelvs.dev@gmail.com
- 🌐 **SII Chile**: https://www.sii.cl/factura_electronica/
- 📞 **Soporte SII**: 600 343 4000

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE](LICENSE) para más detalles.

## 🙏 Agradecimientos

- **Laravel Community** por el excelente framework
- **SII Chile** por la documentación de integración
- **Spatie** por las librerías de permisos
- **Barry vd. Heuvel** por DomPDF Laravel

---

<div align="center">

**⭐ Si te gusta este proyecto, dale una estrella en GitHub ⭐**

**Hecho con ❤️ para la comunidad de desarrolladores chilenos**

</div>

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
