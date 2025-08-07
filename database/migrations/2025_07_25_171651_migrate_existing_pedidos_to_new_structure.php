<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Migrar datos existentes de pedidos a la nueva tabla pivote
        $pedidos = DB::table('pedidos')->get();
        
        foreach ($pedidos as $pedido) {
            if ($pedido->mercancia_id) {
                DB::table('pedido_mercancia')->insert([
                    'pedido_id' => $pedido->id,
                    'mercancia_id' => $pedido->mercancia_id,
                    'cantidad_solicitada' => $pedido->cantidad_solicitada ?? 1,
                    'precio_unitario' => $pedido->precio_unitario,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Remover columnas obsoletas de la tabla pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['mercancia_id']);
            $table->dropColumn('mercancia_id');
            $table->dropColumn('cantidad_solicitada');
            $table->dropColumn('precio_unitario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Restaurar columnas en la tabla pedidos
        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('mercancia_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('cantidad_solicitada')->nullable();
            $table->decimal('precio_unitario', 10, 2)->nullable();
        });
        
        // Migrar datos de vuelta (solo el primer ítem de cada pedido)
        $pedidoMercancias = DB::table('pedido_mercancia')
            ->select('pedido_id', 'mercancia_id', 'cantidad_solicitada', 'precio_unitario')
            ->orderBy('pedido_id')
            ->orderBy('id')
            ->get()
            ->groupBy('pedido_id');
            
        foreach ($pedidoMercancias as $pedidoId => $items) {
            $firstItem = $items->first();
            DB::table('pedidos')
                ->where('id', $pedidoId)
                ->update([
                    'mercancia_id' => $firstItem->mercancia_id,
                    'cantidad_solicitada' => $firstItem->cantidad_solicitada,
                    'precio_unitario' => $firstItem->precio_unitario,
                ]);
        }
    }
};
