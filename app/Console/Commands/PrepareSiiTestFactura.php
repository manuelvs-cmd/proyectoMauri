<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Factura;

class PrepareSiiTestFactura extends Command
{
    protected $signature = 'sii:prepare-test-factura';
    protected $description = 'Preparar factura de prueba para testing SII';

    public function handle()
    {
        try {
            $factura = Factura::where('numero_documento', 'F-001')->first();
            
            if (!$factura) {
                $this->error('❌ No se encontró la factura F-001. Ejecuta primero: php artisan sii:create-test-data');
                return 1;
            }

            $factura->update([
                'sii_estado' => 'pendiente',
                'estado' => 'emitida',
                'sii_track_id' => null,
                'sii_fecha_envio' => null,
                'sii_respuesta' => null
            ]);

            $this->info('✅ Factura preparada para testing SII:');
            $this->line("   • ID: {$factura->id}");
            $this->line("   • Número: {$factura->numero_documento}");
            $this->line("   • Estado: {$factura->estado}");
            $this->line("   • Estado SII: {$factura->sii_estado}");
            $this->line("   • ¿Es enviada al SII?: " . ($factura->esEnviadaAlSii() ? 'SÍ' : 'NO'));

            $this->newLine();
            $this->info('🌐 Ahora puedes:');
            $this->line('   1. Ir a http://localhost:8000/facturas/' . $factura->id);
            $this->line('   2. Hacer clic en "Enviar al SII"');
            $this->line('   3. Observar el resultado en el modal');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
