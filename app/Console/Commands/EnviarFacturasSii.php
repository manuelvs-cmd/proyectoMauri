<?php

namespace App\Console\Commands;

use App\Models\Factura;
use App\Services\SiiService;
use Illuminate\Console\Command;

class EnviarFacturasSii extends Command
{

    protected $signature = 'sii:enviar-facturas 
                           {--limit=10 : Número máximo de facturas a procesar}
                           {--tipo= : Tipo de documento (factura|boleta)}
                           {--fecha= : Fecha específica (Y-m-d)}';


    protected $description = 'Enviar facturas pendientes al SII de Chile';


    public function handle(SiiService $siiService)
    {
        $this->info('Iniciando envío de facturas al SII...');
        

        $limit = $this->option('limit') ?? 10;
        $tipo = $this->option('tipo');
        $fecha = $this->option('fecha');
        

        $query = Factura::pendientesEnvioSii()->with(['pedido.cliente', 'pedido.mercancia']);
        
        if ($tipo) {
            $query->tipoDocumento($tipo);
        }
        
        if ($fecha) {
            $query->fechaEmision($fecha);
        }
        
        $facturas = $query->limit($limit)->get();
        
        if ($facturas->isEmpty()) {
            $this->info('No hay facturas pendientes de envío.');
            return Command::SUCCESS;
        }
        
        $this->info("Procesando {$facturas->count()} facturas...");
        
        $enviados = 0;
        $errores = 0;
        
        $bar = $this->output->createProgressBar($facturas->count());
        $bar->start();
        
        foreach ($facturas as $factura) {
            try {
                $resultado = $siiService->enviarFactura($factura);
                
                if ($resultado['success']) {
                    $enviados++;
                    $this->newLine();
                    $this->info("✓ Factura {$factura->numero_documento} enviada exitosamente");
                } else {
                    $errores++;
                    $this->newLine();
                    $this->error("✗ Error en factura {$factura->numero_documento}: {$resultado['message']}");
                }
                
            } catch (\Exception $e) {
                $errores++;
                $this->newLine();
                $this->error("✗ Excepción en factura {$factura->numero_documento}: {$e->getMessage()}");
            }
            
            $bar->advance();
            
            sleep(1);
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->table(
            ['Estado', 'Cantidad'],
            [
                ['Enviadas exitosamente', $enviados],
                ['Con errores', $errores],
                ['Total procesadas', $facturas->count()]
            ]
        );
        
        if ($errores > 0) {
            $this->warn("Se encontraron {$errores} errores. Revisa los logs para más detalles.");
            return Command::FAILURE;
        }
        
        $this->info('Proceso completado exitosamente!');
        return Command::SUCCESS;
    }
}
