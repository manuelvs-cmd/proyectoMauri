<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;
use App\Models\Pedido;

class DemostraDireccionAutomatica extends Command
{

    protected $signature = 'demo:direccion-automatica';


    protected $description = 'Demuestra cómo funciona la dirección automática en pedidos';


    public function handle()
    {
        $this->info('=== Demostración de Dirección Automática ===');
        $this->newLine();

        $cliente = Cliente::first();
        
        if (!$cliente) {
            $this->error('No hay clientes en la base de datos. Crea un cliente primero.');
            return;
        }

        $this->info("Cliente seleccionado: {$cliente->razon_social}");
        $this->info("RUT: {$cliente->rut}");
        $this->info("Ciudad: {$cliente->ciudad}");
        $this->info("Comuna: {$cliente->comuna}");
        $this->info("Dirección exacta: {$cliente->direccion_exacta}");
        $this->newLine();

        $direccion_completa = $cliente->obtenerDireccionCompleta();
        $this->info("Dirección completa construida automáticamente:");
        $this->line("➤ {$direccion_completa}");
        $this->newLine();

        $this->info('Esta dirección se agregará automáticamente al crear un pedido para este cliente.');
        $this->info('También puedes usar la funcionalidad AJAX en el formulario web para obtener la dirección.');
        
        $this->newLine();
        $this->info('=== Funcionalidades implementadas ===');
        $this->line('✓ Dirección automática al crear pedido (si no se proporciona)');
        $this->line('✓ JavaScript para llenar automáticamente en formulario de crear');
        $this->line('✓ Botón para actualizar dirección en formulario de editar');
        $this->line('✓ Endpoint AJAX para obtener dirección del cliente');
        $this->line('✓ Validación opcional de dirección en el formulario');
    }
}
