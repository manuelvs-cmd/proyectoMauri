<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Services\SiiService;

class CheckSiiConfig extends Command
{

    protected $signature = 'sii:check-config';


    protected $description = 'Verifica la configuración del SII y muestra el estado de los componentes';


    public function handle()
    {
        $this->info('🔍 Verificando configuración del SII...');
        $this->newLine();

        $hasErrors = false;

        $this->info('📄 1. Archivo de configuración:');
        if (File::exists(base_path('.env.sii'))) {
            $this->info('   ✅ Archivo .env.sii encontrado');
        } else {
            $this->error('   ❌ Archivo .env.sii NO encontrado');
            $this->warn('      → Copia .env.sii.example como .env.sii');
            $hasErrors = true;
        }

        $this->newLine();
        $this->info('⚙️  2. Variables de configuración:');
        
        $requiredVars = [
            'SII_AMBIENTE' => config('sii.ambiente'),
            'SII_EMISOR_RUT' => config('sii.emisor.rut'),
            'SII_EMISOR_RAZON_SOCIAL' => config('sii.emisor.razon_social'),
            'SII_CERTIFICADO_PATH' => config('sii.certificado.ruta'),
            'SII_CERTIFICADO_PASSWORD' => config('sii.certificado.password'),
        ];

        foreach ($requiredVars as $var => $value) {
            $envValue = env(str_replace('SII_', 'SII_', $var));
            
            if (empty($value) && empty($envValue)) {
                $this->error("   ❌ {$var}: No configurado");
                $hasErrors = true;
            } elseif ($value === 'default_value' || str_contains($value ?? '', 'ejemplo') || str_contains($envValue ?? '', 'ejemplo')) {
                $this->error("   ❌ {$var}: Usando valor por defecto");
                $hasErrors = true;
            } else {
                $this->info("   ✅ {$var}: Configurado");
                if (str_contains($var, 'PASSWORD')) {
                    $this->info("      → Valor: " . str_repeat('*', min(8, strlen($envValue ?? $value))));
                } elseif (str_contains($var, 'RUT')) {
                    $this->info("      → Valor: " . ($envValue ?? $value));
                }
            }
        }

        $this->newLine();
        $this->info('🔐 3. Certificado digital:');
        
        $certPath = config('sii.certificado.ruta');
        $envCertPath = env('SII_CERTIFICADO_PATH');
        $actualCertPath = $envCertPath ?? $certPath;
        $fullCertPath = str_starts_with($actualCertPath, storage_path()) ? $actualCertPath : storage_path($actualCertPath);
        $fullCertPath = str_replace('storage\\storage', 'storage', $fullCertPath);
        if ($actualCertPath && File::exists($fullCertPath)) {
            $this->info('   ✅ Archivo de certificado encontrado');
            
            try {
                $certContent = File::get($fullCertPath);
                if (strlen($certContent) > 100) { 
                    $this->info('   ✅ Certificado parece válido (tamaño OK)');
                } else {
                    $this->error('   ❌ Certificado parece corrupto (muy pequeño)');
                    $hasErrors = true;
                }
            } catch (\Exception $e) {
                $this->error('   ❌ Error al leer certificado: ' . $e->getMessage());
                $hasErrors = true;
            }
        } else {
            $this->error('   ❌ Certificado digital NO encontrado');
            $this->warn('      → Ruta esperada: ' . $fullCertPath);
            $this->warn('      → Ruta configurada: ' . ($actualCertPath ?? 'No configurada'));
            $hasErrors = true;
        }

        $this->newLine();
        $this->info('📁 4. Folios CAF:');
        
        $foliosPath = storage_path('folios');
        if (File::exists($foliosPath)) {
            $this->info('   ✅ Directorio de folios existe');
            
            $folios = File::glob($foliosPath . '/*.xml');
            if (count($folios) > 0) {
                $this->info('   ✅ ' . count($folios) . ' archivos CAF encontrados:');
                foreach ($folios as $folio) {
                    $this->info('      → ' . basename($folio));
                }
            } else {
                $this->warn('   ⚠️  No se encontraron archivos CAF (.xml)');
                $this->warn('      → Descarga folios del SII y colócalos en storage/folios/');
            }
        } else {
            $this->error('   ❌ Directorio de folios NO existe');
            $hasErrors = true;
        }

        $this->newLine();
        $this->info('🌐 5. Conectividad:');
        
        $ambiente = config('sii.ambiente', 'certificacion');
        $seedUrl = config("sii.urls.{$ambiente}.seed");
        
        if ($seedUrl) {
            $this->info("   🔗 Ambiente: {$ambiente}");
            $this->info("   🔗 URL semilla: {$seedUrl}");
            
            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 5,
                        'user_agent' => 'Laravel SII Client',
                    ]
                ]);
                
                $response = @file_get_contents($seedUrl, false, $context);
                if ($response !== false) {
                    $this->info('   ✅ Conectividad al SII: OK');
                } else {
                    $this->warn('   ⚠️  No se pudo conectar al SII (revisa internet)');
                }
            } catch (\Exception $e) {
                $this->warn('   ⚠️  Error de conectividad: ' . $e->getMessage());
            }
        }

        $this->newLine();
        if (!$hasErrors) {
            $this->info('🎉 ¡Configuración del SII parece correcta!');
            $this->info('   → Puedes proceder a enviar facturas.');
        } else {
            $this->error('⚠️  Se encontraron problemas en la configuración.');
            $this->error('   → Revisa los errores y corrige antes de continuar.');
            $this->newLine();
            $this->warn('📖 Para más ayuda, consulta: docs/configuracion_sii.md');
        }

        return $hasErrors ? 1 : 0;
    }
}
