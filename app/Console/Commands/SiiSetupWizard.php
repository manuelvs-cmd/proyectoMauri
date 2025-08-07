<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SiiSetupWizard extends Command
{
    protected $signature = 'sii:setup';
    protected $description = 'Asistente de configuración para SII Chile';

    public function handle()
    {
        $this->info('🏛️ ASISTENTE DE CONFIGURACIÓN SII CHILE');
        $this->line('========================================');
        $this->line('');

        $ambiente = $this->choice('¿En qué ambiente trabajarás?', [
            'certificacion' => '🧪 Certificación (Pruebas)',
            'produccion' => '🚀 Producción (Real)'
        ], 'certificacion');

        $this->info('📋 Configuremos los datos de tu empresa:');
        $this->line('(Estos datos deben coincidir EXACTAMENTE con los registrados en el SII)');
        $this->line('');

        $rut = $this->ask('🆔 RUT de tu empresa (con guión)', '76123456-7');
        $razonSocial = $this->ask('🏢 Razón social completa', 'MI EMPRESA DE PRUEBA SPA');
        $giro = $this->ask('💼 Giro comercial', 'Venta al por menor de productos varios');
        $direccion = $this->ask('📍 Dirección fiscal', 'Av. Libertador Bernardo O\'Higgins 1449');
        $comuna = $this->ask('🏘️ Comuna', 'Santiago');
        $ciudad = $this->ask('🏙️ Ciudad', 'Santiago');
        $telefono = $this->ask('📞 Teléfono', '+56912345678');
        $email = $this->ask('📧 Email de facturación', 'facturacion@miempresa.cl');

        $this->info('🔐 Configuración del certificado digital:');
        $certPath = $this->ask('📁 Ruta del certificado (.p12)', 'storage/certificates/certificado.p12');
        
        if (!file_exists($certPath)) {
            $this->warn("⚠️ No se encontró el certificado en: {$certPath}");
            $this->line('Por favor, asegúrate de colocar tu certificado .p12 en esa ubicación.');
        }

        $certPassword = $this->secret('🔑 Password del certificado');

        $this->line('');
        $this->info('📄 RESUMEN DE CONFIGURACIÓN:');
        $this->table(
            ['Campo', 'Valor'],
            [
                ['Ambiente', $ambiente === 'certificacion' ? '🧪 Certificación' : '🚀 Producción'],
                ['RUT', $rut],
                ['Razón Social', $razonSocial],
                ['Giro', $giro],
                ['Dirección', $direccion],
                ['Comuna', $comuna],
                ['Ciudad', $ciudad],
                ['Teléfono', $telefono],
                ['Email', $email],
                ['Certificado', $certPath],
                ['Password', str_repeat('*', strlen($certPassword))],
            ]
        );

        if (!$this->confirm('¿Los datos son correctos?')) {
            $this->warn('❌ Configuración cancelada. Ejecuta nuevamente el comando.');
            return 1;
        }

        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        $siiConfig = "\n# ===================================================================\n";
        $siiConfig .= "# 🏛️ CONFIGURACIÓN SII CHILE - FACTURACIÓN ELECTRÓNICA\n";
        $siiConfig .= "# ===================================================================\n\n";
        $siiConfig .= "SII_AMBIENTE={$ambiente}\n";
        $siiConfig .= "SII_EMISOR_RUT={$rut}\n";
        $siiConfig .= "SII_EMISOR_RAZON_SOCIAL=\"{$razonSocial}\"\n";
        $siiConfig .= "SII_EMISOR_GIRO=\"{$giro}\"\n";
        $siiConfig .= "SII_EMISOR_DIRECCION=\"{$direccion}\"\n";
        $siiConfig .= "SII_EMISOR_COMUNA=\"{$comuna}\"\n";
        $siiConfig .= "SII_EMISOR_CIUDAD=\"{$ciudad}\"\n";
        $siiConfig .= "SII_EMISOR_TELEFONO=\"{$telefono}\"\n";
        $siiConfig .= "SII_EMISOR_EMAIL=\"{$email}\"\n";
        $siiConfig .= "SII_CERTIFICADO_PATH={$certPath}\n";
        $siiConfig .= "SII_CERTIFICADO_PASSWORD=\"{$certPassword}\"\n";
        $siiConfig .= "SII_TIMEOUT=30\n";
        $siiConfig .= "SII_VALIDAR_CERTIFICADO=" . ($ambiente === 'produccion' ? 'true' : 'false') . "\n";
        $siiConfig .= "SII_LOGS_ENABLED=true\n";

        $envContent = preg_replace('/# ===================================================================\s*\n# 🏛️.*?\n# ===================================================================.*?SII_LOGS_ENABLED=.*?\n/s', '', $envContent);

        $envContent .= $siiConfig;

        file_put_contents($envPath, $envContent);

        $this->info('✅ Configuración guardada exitosamente en .env');
        $this->line('');

        $this->info('🔍 Validando configuración...');
        $this->call('config:clear');
        $this->call('sii:validate-config');

        $this->line('');
        $this->info('🎉 ¡Configuración completada!');
        $this->line('');
        $this->info('📋 PRÓXIMOS PASOS:');
        $this->line('1. ✅ Verifica que el certificado esté en la ruta correcta');
        $this->line('2. ✅ Asegúrate de tener folios CAF en storage/folios/');
        $this->line('3. 🚀 Envía facturas con: php artisan sii:envio-masivo');
        $this->line('4. 🌐 O usa la interfaz web en: /facturas/dashboard-sii');

        return 0;
    }
}
