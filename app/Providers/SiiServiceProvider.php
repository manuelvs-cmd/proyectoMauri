<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SiiService;
use App\Services\SiiCertificateService;
use App\Services\SiiDteGenerator;

class SiiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar servicios como singletons para optimizar rendimiento
        $this->app->singleton(SiiCertificateService::class, function ($app) {
            return new SiiCertificateService();
        });

        $this->app->singleton(SiiDteGenerator::class, function ($app) {
            return new SiiDteGenerator($app->make(SiiCertificateService::class));
        });

        $this->app->singleton(SiiService::class, function ($app) {
            return new SiiService(
                $app->make(SiiCertificateService::class),
                $app->make(SiiDteGenerator::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Registrar comandos de consola
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\SiiValidateConfig::class,
                \App\Console\Commands\SiiEnvioMasivo::class,
                \App\Console\Commands\SiiSetupWizard::class,
                \App\Console\Commands\SiiConvertCertificate::class,
            ]);
        }
    }
}
