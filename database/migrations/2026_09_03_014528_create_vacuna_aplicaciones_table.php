<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vacuna_aplicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacuna_id')->constrained()->onDelete('cascade');
            $table->foreignId('mascota_id')->constrained()->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha_aplicacion');
            $table->date('fecha_proxima')->nullable();
            $table->decimal('dosis_aplicada', 10, 2)->default(1);
            $table->string('lote')->nullable();
            $table->string('via_aplicacion')->default('subcutanea');
            $table->text('observaciones')->nullable();
            $table->decimal('costo', 10, 2)->default(0);
            $table->enum('estado', ['aplicada', 'programada', 'cancelada'])->default('aplicada');
            $table->timestamps();
            
            $table->index(['mascota_id', 'fecha_aplicacion']);
            $table->index(['vacuna_id', 'fecha_aplicacion']);
            $table->index('fecha_proxima');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vacuna_aplicaciones');
    }
};