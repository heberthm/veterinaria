<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
    {
       Schema::create('recetas', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('tenant_id')->index();
        $table->foreignId('mascota_id')->constrained('mascotas')->cascadeOnDelete();
        $table->foreignId('historia_clinica_id')->nullable()->constrained('historias_clinicas')->nullOnDelete();
        $table->foreignId('veterinario_id')->nullable()->constrained('users')->nullOnDelete();
        $table->date('fecha_emision');
        $table->text('indicaciones_generales')->nullable();
        $table->text('observaciones')->nullable();
        $table->timestamps();
    });

    Schema::create('receta_medicamentos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('receta_id')->constrained('recetas')->cascadeOnDelete();
        $table->string('medicamento', 150);
        $table->string('dosis', 100);
        $table->string('frecuencia', 50)->nullable();
        $table->unsignedSmallInteger('duracion_dias')->nullable();
        $table->string('instrucciones', 255)->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recetas_tables');
    }
};
