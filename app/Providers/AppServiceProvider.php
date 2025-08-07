<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\RoleHelper;
use App\Helpers\SiiEnvLoader;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cargar variables de entorno SII
        SiiEnvLoader::load();
        
        // Configurar Carbon en español
        Carbon::setLocale('es');
        
        // Registrar directivas de Blade personalizadas
        Blade::if('superadmin', function () {
            return RoleHelper::isSuperAdmin();
        });
        
        Blade::if('role', function ($role) {
            return RoleHelper::hasRole($role);
        });
        
        Blade::if('permission', function ($permission) {
            return RoleHelper::hasPermission($permission);
        });
    }
}
