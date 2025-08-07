<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SiiService;
use App\Models\Factura;

class SiiEnvioMasivo extends Command
{
    protected $signature = 'sii:envio-masivo 
                           {--limit=10 : Número máximo de facturas a enviar}
                           {--fecha= : Fecha específica (YYYY-MM-DD) para enviar facturas}
                           {--dry-run : Simular envío sin ejecutar}';
    
    protected $description = 'Envío masivo de facturas pendientes al SII';

    public function handle(SiiService $siiService)
    {
        $limit = $this->option('limit');
        $fecha = $this->option('fecha');
        $dryRun = $this->option('dry-run');

        $this->info('🚀 Iniciando envío masivo al SII...');
        $this->line('');

        $query = Factura::with(['pedido.cliente'])
                        ->where(function($q) {
                            $q->where('sii_estado', 'pendiente')
                              ->orWhereNull('sii_estado');
                        });

        if ($fecha) {
            $query->whereDate('fecha_emision', $fecha);
        }

        $facturas = $query->orderBy('fecha_emision')
                         ->limit($limit)
                         ->get();

        if ($facturas->isEmpty()) {
            $this->warn('⚠️  No se encontraron facturas pendientes para enviar.');
            return 0;
        }

        $this->info("📦 Se encontraron {$facturas->count()} facturas para enviar:");
        
        $this->table(
            ['ID', 'Número', 'Cliente', 'Total', 'Fecha'],
            $facturas->map(function($factura) {
                return [
                    $factura->id,
                    $factura->numero_documento,
                    $factura->pedido->cliente->razon_social,
                    '$' . number_format($factura->total, 0, ',', '.'),
                    $factura->fecha_emision->format('d/m/Y')
                ];
            })
        );

        if ($dryRun) {
            $this->warn('🔍 Modo simulación activado - no se enviarán las facturas');
            return 0;
        }

        if (!$this->confirm('¿Desea proceder con el envío?')) {
            $this->info('❌ Envío cancelado por el usuario');
            return 0;
        }

        $facturaIds = $facturas->pluck('id')->toArray();
        
        try {
            $this->info('📤 Enviando facturas al SII...');
            $this->output->progressStart($facturas->count());

            $resultado = $siiService->envioMasivo($facturaIds);

            $this->output->progressFinish();
            $this->line('');

            $this->info("✅ Envío completado:");
            $this->line("   • Total procesadas: {$resultado['total']}");
            $this->line("   • Exitosas: {$resultado['exitosos']}");
            $this->line("   • Fallidas: {$resultado['fallidos']}");

            if ($resultado['fallidos'] > 0) {
                $this->line('');
                $this->warn('❌ Facturas con errores:');
                foreach ($resultado['resultados'] as $item) {
                    if (!$item['success']) {
                        $this->line("   • Factura {$item['factura_id']}: {$item['error']}");
                    }
                }
            }

        } catch (\Exception $e) {
            $this->error("❌ Error durante el envío: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
