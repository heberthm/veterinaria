<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
       Schema::create('examenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->foreignId('mascota_id')->constrained('mascotas')->cascadeOnDelete();
            $table->foreignId('historia_clinica_id')->nullable()->constrained('historias_clinicas')->nullOnDelete();
            $table->foreignId('veterinario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('categoria', ['examen', 'laboratorio']);
            $table->string('nombre', 150);              // Radiografía de tórax, Hemograma...
            $table->date('fecha');
            $table->text('resultado')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('archivo_ruta')->nullable();
            $table->string('archivo_nombre')->nullable();
            $table->timestamps();
});
    }    
    public function down()
    {
        Schema::dropIfExists('examenes');
    }
};
