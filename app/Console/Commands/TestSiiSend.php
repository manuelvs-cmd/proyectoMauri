<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Factura;
use App\Services\SiiService;

class TestSiiSend extends Command
{
    protected $signature = 'sii:test-send {factura_id?}';
    protected $description = 'Probar el envío de una factura al SII desde consola';

    public function handle()
    {
        try {
            $facturaId = $this->argument('factura_id') ?? 11;
            
            $this->info("🧪 Probando envío de factura ID: {$facturaId} al SII...");
            $this->newLine();

            $factura = Factura::with(['pedido.cliente', 'pedido.mercancia'])->find($facturaId);
            
            if (!$factura) {
                $this->error("❌ No se encontró la factura con ID: {$facturaId}");
                return 1;
            }

            $this->info("📋 Información de la factura:");
            $this->line("   • Número: {$factura->numero_documento}");
            $this->line("   • Cliente: {$factura->pedido->cliente->razon_social}");
            $this->line("   • Total: $" . number_format($factura->total));
            $this->line("   • Estado SII: {$factura->sii_estado}");
            $this->newLine();

            if ($factura->esEnviadaAlSii()) {
                $this->warn("⚠️  Esta factura ya fue enviada al SII");
                $this->line("   • Track ID: {$factura->sii_track_id}");
                return 0;
            }

            $this->info("🔧 Inicializando servicio SII...");
            $siiService = new SiiService();

            $this->info("📤 Enviando factura al SII...");
            $resultado = $siiService->enviarFactura($factura);

            $this->newLine();

            if ($resultado['success']) {
                $this->info("🎉 ¡Envío exitoso!");
                $this->line("   • Mensaje: {$resultado['message']}");
                if (isset($resultado['track_id'])) {
                    $this->line("   • Track ID: {$resultado['track_id']}");
                }
            } else {
                $this->error("❌ Error en el envío:");
                $this->line("   • Mensaje: {$resultado['message']}");
                if (isset($resultado['error'])) {
                    $this->line("   • Error: {$resultado['error']}");
                }
            }

            $this->newLine();
            $this->info("📊 Para ver logs detallados ejecuta:");
            $this->line("   Get-Content storage\\logs\\laravel.log -Tail 20");

            return $resultado['success'] ? 0 : 1;

        } catch (\Exception $e) {
            $this->error("❌ Excepción: " . $e->getMessage());
            $this->error("   Archivo: " . $e->getFile() . ":" . $e->getLine());
            $this->newLine();
            $this->line("Stack trace:");
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}
