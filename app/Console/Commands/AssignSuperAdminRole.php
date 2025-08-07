<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignSuperAdminRole extends Command
{

    protected $signature = 'user:assign-superadmin {email : Email del usuario}';


    protected $description = 'Asigna el rol superadmin a un usuario específico';


    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado.");
            return 1;
        }
        
        $superadminRole = Role::where('name', 'superadmin')->first();
        
        if (!$superadminRole) {
            $this->error("El rol 'superadmin' no existe. Ejecuta primero el seeder de roles.");
            return 1;
        }
        
        $user->assignRole('superadmin');
        
        $this->info("Rol 'superadmin' asignado exitosamente al usuario {$user->name} ({$user->email}).");
        
        return 0;
    }
}
