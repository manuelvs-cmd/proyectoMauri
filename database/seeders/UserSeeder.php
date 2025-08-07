<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario superadmin
        $superadmin = User::create([
            'name' => 'Super Administrador',
            'username' => 'admin',
            'email' => 'admin@empresa.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol superadmin
        $superadmin->assignRole('superadmin');

        // Crear usuario vendedor
        $vendedor = User::create([
            'name' => 'Juan Pérez',
            'username' => 'juan.vendedor',
            'email' => 'juan@empresa.com',
            'password' => Hash::make('juan123'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol vendedor
        $vendedor->assignRole('vendedor');

        // Crear otro usuario vendedor
        $vendedor2 = User::create([
            'name' => 'María González',
            'username' => 'maria.vendedor',
            'email' => 'maria@empresa.com',
            'password' => Hash::make('maria123'),
            'email_verified_at' => now(),
        ]);

        // Asignar rol vendedor
        $vendedor2->assignRole('vendedor');

        $this->command->info('Usuarios creados exitosamente:');
        $this->command->info('- Superadmin: usuario=admin, contraseña=admin123');
        $this->command->info('- Vendedor: usuario=juan.vendedor, contraseña=juan123');
        $this->command->info('- Vendedor: usuario=maria.vendedor, contraseña=maria123');
    }
}
