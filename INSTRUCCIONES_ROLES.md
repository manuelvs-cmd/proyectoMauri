# Configuración del Sistema de Roles - Instrucciones

## Resumen de los cambios realizados

Se ha implementado un sistema completo de gestión de usuarios con roles donde:

1. **Solo los usuarios con rol "superadmin" pueden registrar nuevos usuarios**
2. **Durante el registro se puede seleccionar el rol del nuevo usuario**
3. **Se han creado vistas completas para gestión de usuarios (CRUD)**

## Pasos para completar la configuración:

### 1. Ejecutar las migraciones y seeders

```bash
php artisan migrate --seed
```

### 2. Crear roles y permisos ejecutando el seeder

```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 3. Asignar rol superadmin a un usuario existente

Si ya tienes usuarios en la base de datos, puedes asignar el rol superadmin a un usuario específico:

```bash
php artisan assign:superadmin 1
```

(Reemplaza "1" con el ID del usuario al que quieres asignar el rol)

### 4. Verificar que el autoload esté actualizado

```bash
composer dump-autoload
```

## Funcionalidades implementadas:

### 🔐 Middleware de Seguridad
- **SuperAdminMiddleware**: Protege las rutas de gestión de usuarios
- Solo usuarios con rol "superadmin" pueden acceder a la gestión de usuarios

### 🎯 Directivas de Blade personalizadas
- `@superadmin` - Muestra contenido solo a superadministradores
- `@role('nombre_rol')` - Muestra contenido basado en roles específicos
- `@permission('nombre_permiso')` - Muestra contenido basado en permisos

### 🖥️ Vistas nuevas creadas:
- `resources/views/users/index.blade.php` - Lista de usuarios
- `resources/views/users/create.blade.php` - Crear nuevo usuario
- `resources/views/users/edit.blade.php` - Editar usuario
- `resources/views/users/show.blade.php` - Ver detalles del usuario

### 🛣️ Rutas protegidas:
- `/users` - CRUD completo de usuarios (solo superadmin)
- `/register` - Registro legacy (solo superadmin)

### 📋 Controlador UserController:
- Gestión completa de usuarios con validaciones
- Asignación y actualización de roles
- Protección contra auto-eliminación

## Uso del sistema:

### Como Superadmin:
1. Accede al dashboard
2. Ve la opción "Gestión de Usuarios" en el menú
3. Puedes crear, editar, ver y eliminar usuarios
4. Al crear usuarios, puedes asignar roles (superadmin o vendedor)

### Como Vendedor:
- No verás las opciones de gestión de usuarios
- No podrás acceder a las rutas protegidas de usuarios

## Roles disponibles:

### Superadmin
- Acceso completo a todas las funcionalidades
- Puede gestionar usuarios
- Puede gestionar mercancías, clientes y pedidos

### Vendedor
- Acceso limitado a funcionalidades de ventas
- Puede ver y gestionar clientes y pedidos
- No puede gestionar usuarios ni mercancías (dependiendo de la configuración)

## Comandos útiles:

### Listar todos los usuarios y sus roles:
```bash
php artisan tinker
User::with('roles')->get()->map(function($user) { 
    echo "ID: {$user->id}, Nombre: {$user->name}, Rol: " . ($user->roles->first()->name ?? 'Sin rol') . "\n"; 
});
```

### Asignar rol a un usuario:
```bash
php artisan assign:superadmin {USER_ID}
```

## Personalización adicional:

Si quieres agregar más roles o permisos:
1. Modifica el archivo `database/seeders/RolesAndPermissionsSeeder.php`
2. Ejecuta el seeder nuevamente
3. Actualiza las vistas y controladores según sea necesario

## Notas importantes:

- El primer usuario debe ser asignado manualmente como superadmin usando el comando
- Los usuarios sin rol no tendrán acceso a funcionalidades protegidas
- Las directivas de Blade facilitan mostrar/ocultar contenido según roles
- El middleware protege automáticamente las rutas sensibles
