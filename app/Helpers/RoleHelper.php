<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class RoleHelper
{
    /**
     * Verifica si el usuario actual tiene el rol superadmin
     */
    public static function isSuperAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        return Auth::user()->hasRole('superadmin');
    }
    
    /**
     * Verifica si el usuario actual tiene un rol específico
     */
    public static function hasRole(string $role): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        return Auth::user()->hasRole($role);
    }
    
    /**
     * Verifica si el usuario actual tiene un permiso específico
     */
    public static function hasPermission(string $permission): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        return Auth::user()->can($permission);
    }
}
