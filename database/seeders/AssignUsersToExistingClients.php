<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\User;

class AssignUsersToExistingClients extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Obtener todos los clientes sin user_id asignado
        $clientesSinUsuario = Cliente::whereNull('user_id')->get();
        
        // Obtener un usuario admin para asignar a estos clientes
        $adminUser = User::whereHas('roles', function($query) {
            $query->where('name', 'superadmin');
        })->first();
        
        if ($adminUser && $clientesSinUsuario->count() > 0) {
            foreach ($clientesSinUsuario as $cliente) {
                $cliente->update(['user_id' => $adminUser->id]);
            }
            
            $this->command->info('Asignados ' . $clientesSinUsuario->count() . ' clientes al usuario admin.');
        } else {
            $this->command->info('No hay clientes sin asignar o no se encontró un usuario admin.');
        }
    }
}
