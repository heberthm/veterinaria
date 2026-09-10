<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('mascota_id')->constrained('mascotas')->cascadeOnDelete();
            $table->foreignId('veterinario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            $table->dateTime('fecha_consulta');
            $table->text('motivo_consulta')->nullable();
            $table->text('anamnesis')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento')->nullable();
            // Signos vitales
            $table->decimal('temperatura', 5, 2)->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('frecuencia_respiratoria')->nullable();
            $table->decimal('peso', 6, 2)->nullable();
            $table->string('estado_corporal')->nullable();
            $table->string('hidratacion')->nullable();
            $table->text('examen_fisico')->nullable();
            $table->text('observaciones')->nullable();
            $table->date('proxima_cita_sugerida')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'mascota_id']);
        });

        Schema::create('formula_medica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->cascadeOnDelete();
            $table->string('producto');
            $table->string('presentacion')->nullable();
            $table->string('dosis')->nullable();
            $table->string('cantidad')->nullable();
            $table->string('frecuencia')->nullable();
            $table->string('duracion')->nullable();
            $table->timestamps();
        });

        Schema::create('historia_clinica_archivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->cascadeOnDelete();
            $table->string('nombre_original');
            $table->string('ruta');
            $table->enum('tipo', ['imagen', 'pdf'])->default('imagen');
            $table->unsignedInteger('peso_kb')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('historia_clinica_archivos');
        Schema::dropIfExists('formula_medica');
        Schema::dropIfExists('historias_clinicas');
    }
};
