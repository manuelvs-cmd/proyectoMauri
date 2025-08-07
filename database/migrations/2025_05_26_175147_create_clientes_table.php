<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('clientes', function (Blueprint $table) {
        $table->id();
        $table->string('rut')->unique();
        $table->string('razon_social');
        $table->string('giro');
        $table->string('ciudad')->nullable();
        $table->string('comuna')->nullable();
        $table->string('direccion_exacta')->nullable();
        $table->enum('tipo_vivienda', ['Local', 'Casa', 'Departamento'])->nullable();
        $table->string('correo_electronico');
        $table->string('telefono');
        $table->string('orden_atencion');
        $table->string('tipo_atencion');
        $table->string('lista_precios');
        $table->string('formas_pago');
        $table->string('condicion_pago');

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
