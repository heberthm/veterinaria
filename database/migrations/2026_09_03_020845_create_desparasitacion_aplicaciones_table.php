<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('desparasitacion_aplicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desparasitante_id')->constrained()->onDelete('cascade');
            $table->foreignId('mascota_id')->constrained()->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_aplicacion');
            $table->date('fecha_proxima')->nullable();
            $table->decimal('dosis_aplicada', 10, 2)->default(1);
            $table->string('lote')->nullable();
            $table->string('via_aplicacion')->default('oral');
            $table->text('observaciones')->nullable();
            $table->decimal('costo', 10, 2)->default(0);
            $table->enum('estado', ['aplicada', 'programada', 'cancelada'])->default('aplicada');
            $table->timestamps();
            
            // 🔥 ÍNDICES CON NOMBRES MÁS CORTOS
            $table->index(['mascota_id', 'fecha_aplicacion'], 'idx_desa_mascota_fecha');
            $table->index(['desparasitante_id', 'fecha_aplicacion'], 'idx_desa_producto_fecha');
            $table->index('fecha_proxima', 'idx_desa_fecha_proxima');
        });
    }

    public function down()
    {
        Schema::dropIfExists('desparasitacion_aplicaciones');
    }
};