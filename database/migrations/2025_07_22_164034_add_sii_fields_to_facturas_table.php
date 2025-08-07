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
        Schema::table('facturas', function (Blueprint $table) {
            // Campos para integración con SII
            $table->string('sii_track_id')->nullable()->after('observaciones');
            $table->enum('sii_estado', ['pendiente', 'enviado', 'aceptado', 'rechazado', 'reparo'])
                  ->default('pendiente')->after('sii_track_id');
            $table->timestamp('sii_fecha_envio')->nullable()->after('sii_estado');
            $table->json('sii_respuesta')->nullable()->after('sii_fecha_envio');
            $table->string('sii_folio_caf')->nullable()->after('sii_respuesta');
            $table->boolean('sii_enviado_automatico')->default(false)->after('sii_folio_caf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn([
                'sii_track_id',
                'sii_estado',
                'sii_fecha_envio',
                'sii_respuesta',
                'sii_folio_caf',
                'sii_enviado_automatico'
            ]);
        });
    }
};
