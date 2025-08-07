<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SiiService;

class SiiValidateConfig extends Command
{
    protected $signature = 'sii:validate-config';
    protected $description = 'Validar configuración del SII Chile';

    public function handle(SiiService $siiService)
    {
        $this->info('🔍 Validando configuración del SII...');
        $this->line('');

        try {
            $validacion = $siiService->validarConfiguracion();

            if ($validacion['valido']) {
                $this->info('✅ Configuración SII válida');
                $this->info("🌐 Ambiente: {$validacion['ambiente']}");
            } else {
                $this->error('❌ Configuración SII inválida');
                
                if (!empty($validacion['errores'])) {
                    $this->line('');
                    $this->error('🚨 Errores encontrados:');
                    foreach ($validacion['errores'] as $error) {
                        $this->line("   • {$error}");
                    }
                }
            }

            if (!empty($validacion['warnings'])) {
                $this->line('');
                $this->warn('⚠️  Advertencias:');
                foreach ($validacion['warnings'] as $warning) {
                    $this->line("   • {$warning}");
                }
            }

        } catch (\Exception $e) {
            $this->error("❌ Error al validar configuración: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
