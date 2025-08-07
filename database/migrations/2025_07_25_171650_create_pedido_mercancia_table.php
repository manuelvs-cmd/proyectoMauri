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
        Schema::create('pedido_mercancia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained()->onDelete('cascade');
            $table->foreignId('mercancia_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad_solicitada');
            $table->decimal('precio_unitario', 10, 2)->nullable(); // Precio personalizado por ítem
            $table->timestamps();
            
            // Evitar duplicados de la misma mercancía en el mismo pedido
            $table->unique(['pedido_id', 'mercancia_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pedido_mercancia');
    }
};
