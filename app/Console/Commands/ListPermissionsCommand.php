<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class ListPermissionsCommand extends Command
{
    protected $signature = 'permission:list';
    protected $description = 'Listar todos los roles y permisos del sistema';

    public function handle()
    {
        $this->info('=== ROLES Y PERMISOS ===');
        
        $this->info("\n📋 PERMISOS DISPONIBLES:");
        foreach(Permission::all() as $permission) {
            $this->line("  - {$permission->name}");
        }

        $this->info("\n👥 ROLES DISPONIBLES:");
        foreach(Role::all() as $role) {
            $userCount = User::role($role->name)->count();
            $this->line("  - {$role->name} ({$role->permissions->count()} permisos, {$userCount} usuarios)");
            
            foreach($role->permissions as $permission) {
                $this->line("    • {$permission->name}");
            }
        }

        $this->info("\n👤 USUARIOS CON ROLES:");
        foreach(User::all() as $user) {
            $roles = $user->getRoleNames()->implode(', ');
            $this->line("  - {$user->email}: " . ($roles ?: 'Sin rol'));
        }

        return 0;
    }
}
