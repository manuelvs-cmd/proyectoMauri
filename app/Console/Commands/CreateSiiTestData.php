<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;
use App\Models\Mercancia;
use App\Models\Pedido;
use App\Models\Factura;
use App\Models\User;
use Carbon\Carbon;

class CreateSiiTestData extends Command
{
    protected $signature = 'sii:create-test-data';
    protected $description = 'Crear datos de prueba para testing del SII';

    public function handle()
    {
        $this->info('🧪 Creando datos de prueba para SII...');
        $this->newLine();

        try {
            $user = User::first();
            if (!$user) {
                $this->error('❌ No hay usuarios en el sistema. Crea un usuario primero.');
                return 1;
            }
            $this->info("✅ Usuario encontrado: {$user->name} (ID: {$user->id})");

            $cliente = Cliente::updateOrCreate(
                ['rut' => '12345678-9'],
                [
                    'user_id' => $user->id,
                    'rut' => '12345678-9',
                    'razon_social' => 'Cliente de Prueba SII',
                    'giro' => 'Comercio menor',
                    'ciudad' => 'Santiago',
                    'comuna' => 'Santiago',
                    'direccion_exacta' => 'Av. Siempre Viva 123',
                    'tipo_vivienda' => 'Casa',
                    'correo_electronico' => 'prueba@ejemplo.cl',
                    'telefono' => '+56 9 1234 5678',
                    'orden_atencion' => '1',
                    'tipo_atencion' => 'Normal',
                    'lista_precios' => 'General',
                    'formas_pago' => json_encode(['Efectivo']),
                    'condicion_pago' => json_encode(['Contado'])
                ]
            );
            $this->info("✅ Cliente creado: {$cliente->razon_social} (ID: {$cliente->id})");

            $mercancia = Mercancia::updateOrCreate(
                ['nombre' => 'Producto de Prueba SII'],
                [
                    'nombre' => 'Producto de Prueba SII',
                    'cantidad' => 100,
                    'costo_compra' => 8000,
                    'precio_venta' => 10000,
                    'rentabilidad' => 25.0,
                    'kilos_litros' => 1.0
                ]
            );
            $this->info("✅ Mercancía creada: {$mercancia->nombre} (ID: {$mercancia->id})");

            $pedido = Pedido::create([
                'user_id' => $user->id,
                'cliente_id' => $cliente->id,
                'mercancia_id' => $mercancia->id,
                'precio_unitario' => 10000,
                'cantidad_solicitada' => 2,
                'fecha_entrega' => Carbon::today(),
                'direccion_entrega' => $cliente->obtenerDireccionCompleta(),
                'horario_entrega' => 'Mañana',
                'condicion_pago' => 'Contado',
                'formas_pago' => 'Efectivo',
                'observacion' => 'Pedido de prueba para testing SII'
            ]);
            $this->info("✅ Pedido creado (ID: {$pedido->id}) - Total: $" . number_format($pedido->calcularTotal()));

            $factura = Factura::create([
                'pedido_id' => $pedido->id,
                'numero_documento' => 'F-001',
                'tipo_documento' => 'factura',
                'fecha_emision' => Carbon::now(),
                'subtotal' => $pedido->calcularTotal() / 1.19, // Sin IVA
                'iva' => ($pedido->calcularTotal() / 1.19) * 0.19, // IVA 19%
                'total' => $pedido->calcularTotal(),
                'estado_sii' => 'pendiente',
                'observaciones' => 'Factura de prueba para testing SII'
            ]);
            $this->info("✅ Factura creada: {$factura->numero_documento} (ID: {$factura->id})");

            $this->newLine();
            $this->info('🎉 ¡Datos de prueba creados exitosamente!');
            $this->newLine();
            
            $this->warn('📋 Resumen de datos creados:');
            $this->line("   • Cliente: {$cliente->razon_social} (RUT: {$cliente->rut})");
            $this->line("   • Producto: {$mercancia->nombre} (\$" . number_format($mercancia->precio_venta) . ")");
            $this->line("   • Pedido: {$pedido->cantidad_solicitada} unidades");
            $this->line("   • Factura: {$factura->numero_documento} (\$" . number_format($factura->total) . ")");
            
            $this->newLine();
            $this->info('🌐 Próximos pasos:');
            $this->line('   1. Inicia el servidor: php artisan serve');
            $this->line('   2. Ve a la sección de facturas');
            $this->line("   3. Busca la factura: {$factura->numero_documento}");
            $this->line('   4. Haz clic en "Enviar al SII"');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error al crear datos de prueba: ' . $e->getMessage());
            $this->error('   Línea: ' . $e->getLine());
            return 1;
        }
    }
}
