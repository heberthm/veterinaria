<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->string('nit', 20)->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('contacto_nombre')->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proveedores');
    }
};