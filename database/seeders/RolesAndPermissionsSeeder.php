<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // Permisos para usuarios
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            
            // Permisos para clientes
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',
            
            // Permisos para mercancías
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            
            // Permisos para pedidos
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            
            // Permisos para reportes
            'view_reports',
            'generate_reports',
            
            // Permisos administrativos
            'manage_system',
            'manage_permissions',
            'access_admin_panel',
        ];

        // Crear todos los permisos
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $superadmin = Role::create(['name' => 'superadmin']);
        $vendedor = Role::create(['name' => 'vendedor']);

        // Asignar todos los permisos al superadmin
        $superadmin->givePermissionTo(Permission::all());

        // Asignar permisos específicos al vendedor
        $vendedorPermissions = [
            'view_clients',
            'create_clients',
            'edit_clients',
            'view_products',
            'view_orders',
            'create_orders',
            'edit_orders',
            'view_reports',
        ];

        $vendedor->givePermissionTo($vendedorPermissions);

        $this->command->info('Roles y permisos creados exitosamente:');
        $this->command->info('- Superadmin: ' . $superadmin->permissions->count() . ' permisos');
        $this->command->info('- Vendedor: ' . $vendedor->permissions->count() . ' permisos');
    }
}
