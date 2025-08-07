<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::create('pedidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');   // Vendedor
        $table->foreignId('cliente_id')->constrained()->onDelete('cascade'); // Cliente
        $table->foreignId('mercancia_id')->constrained()->onDelete('cascade'); // Mercancia

        $table->date('fecha_entrega');
        $table->string('direccion_entrega');
        $table->string('horario_entrega');
        $table->string('condicion_pago');
        $table->string('formas_pago');
        $table->text('observacion')->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
