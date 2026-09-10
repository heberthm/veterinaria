<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mascotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('especie');       // Canino, Felino, Otro
            $table->string('raza')->nullable();
            $table->enum('sexo', ['Macho', 'Hembra'])->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->decimal('peso', 6, 2)->nullable();
            $table->string('color')->nullable();
            $table->string('microchip')->nullable();
            $table->text('alergias')->nullable();
            $table->string('foto')->nullable();
            $table->enum('estado', ['Activo', 'Fallecido', 'Inactivo'])->default('Activo');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'nombre']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mascotas');
    }
};
