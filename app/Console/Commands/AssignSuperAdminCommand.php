<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignSuperAdminCommand extends Command
{
    

    protected $signature = 'assign:superadmin {user}';


    protected $description = 'Assign the superadmin role to a user';


    public function handle()
    {
        $userId = $this->argument('user');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error('User not found.');
            return;
        }

        $superadminRole = Role::where('name', 'superadmin')->first();

        if (!$superadminRole) {
            $this->error('Superadmin role not found. Please run the seeder.');
            return;
        }

        $user->assignRole($superadminRole);

        $this->info("Role 'superadmin' has been assigned to user ID {$userId}.");
    }
}
