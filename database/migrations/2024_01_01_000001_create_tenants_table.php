<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_clinica');
            $table->string('subdominio')->unique();
            $table->string('nit')->nullable();
            $table->string('email_contacto')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('logo')->nullable();
            $table->enum('plan', ['basico', 'profesional', 'premium'])->default('basico');
            $table->enum('estado', ['prueba', 'activo', 'suspendido'])->default('prueba');
            $table->date('fecha_inicio_prueba')->nullable();
            $table->date('fecha_fin_prueba')->nullable();
            $table->date('fecha_vencimiento_plan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
};
