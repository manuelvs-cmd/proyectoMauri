<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Resetear cache de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos para mercancías
        $permissions = [
            'ver mercancías',
            'crear mercancías',
            'editar mercancías',
            'eliminar mercancías',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear permisos para clientes
        $clientePermissions = [
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'eliminar clientes',
        ];

        foreach ($clientePermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear permisos para pedidos
        $pedidoPermissions = [
            'ver pedidos',
            'crear pedidos',
            'editar pedidos',
            'eliminar pedidos',
        ];

        foreach ($pedidoPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles
        $superadmin = Role::firstOrCreate(['name' => 'superadmin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $empleado = Role::firstOrCreate(['name' => 'empleado']);

        // Asignar todos los permisos al superadmin
        $superadmin->givePermissionTo(Permission::all());

        // Asignar permisos limitados al admin (todo excepto eliminar)
        $adminPermissions = [
            'ver mercancías',
            'crear mercancías',
            'editar mercancías',
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'ver pedidos',
            'crear pedidos',
            'editar pedidos',
        ];
        $admin->givePermissionTo($adminPermissions);

        // Asignar permisos básicos al empleado (solo ver y crear)
        $empleadoPermissions = [
            'ver mercancías',
            'ver clientes',
            'crear clientes',
            'ver pedidos',
            'crear pedidos',
        ];
        $empleado->givePermissionTo($empleadoPermissions);

        // Buscar el primer usuario y asignarle el rol de superadmin
        $user = User::first();
        if ($user) {
            $user->assignRole('superadmin');
            $this->command->info('Usuario ' . $user->email . ' asignado como superadmin');
        }

        $this->command->info('Permisos y roles creados exitosamente!');
    }
}
