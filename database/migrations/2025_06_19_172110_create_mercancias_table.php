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
    Schema::create('mercancias', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->integer('cantidad');
        $table->decimal('costo_compra', 10, 2);
        $table->decimal('precio_venta', 10, 2);
        $table->decimal('rentabilidad', 10, 2);
        $table->decimal('kilos_litros', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mercancias');
    }
};
