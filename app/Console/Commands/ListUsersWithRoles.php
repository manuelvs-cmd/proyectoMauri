<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsersWithRoles extends Command
{

    protected $signature = 'user:list-roles';


    protected $description = 'Lista todos los usuarios con sus roles asignados';

    public function handle()
    {
        $users = User::with('roles')->get();
        
        if ($users->isEmpty()) {
            $this->info('No hay usuarios registrados.');
            return 0;
        }
        
        $headers = ['ID', 'Nombre', 'Email', 'Roles'];
        $rows = [];
        
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->implode(', ');
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                $roles ?: 'Sin roles'
            ];
        }
        
        $this->table($headers, $rows);
        
        return 0;
    }
}
