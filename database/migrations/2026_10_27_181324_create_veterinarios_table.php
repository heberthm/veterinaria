<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('veterinarios', function (Blueprint $table) {
            $table->id();
            
            // Relación con el usuario del sistema (para login) - Opcional
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Relación con el tenant (veterinaria)
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');

            // Datos del veterinario
            $table->string('cedula', 20)->nullable()->unique();
            $table->string('nombre', 100);
            $table->string('apellidos', 100);
            $table->string('celular', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('especialidad', 100)->nullable();
            
            // Estado
            $table->boolean('activo')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('veterinarios');
    }
};