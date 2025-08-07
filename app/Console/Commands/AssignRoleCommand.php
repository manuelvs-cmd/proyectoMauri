<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRoleCommand extends Command
{
    protected $signature = 'user:assign-role {email} {role}';
    protected $description = 'Asignar un rol a un usuario por email';

    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado.");
            return 1;
        }

        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->error("Rol {$roleName} no existe.");
            $this->info("Roles disponibles: " . Role::pluck('name')->implode(', '));
            return 1;
        }

        $user->syncRoles([$roleName]);
        
        $this->info("Rol '{$roleName}' asignado exitosamente al usuario {$email}");
        return 0;
    }
}
