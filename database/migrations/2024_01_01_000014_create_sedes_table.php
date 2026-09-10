<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->boolean('principal')->default(false);
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        // Modificamos aquí para primero CREAR la columna y luego hacerla FK
        Schema::table('citas', function (Blueprint $table) {
            // 1. Creamos la columna (debe ser nullable si usas nullOnDelete)
            $table->unsignedBigInteger('sede_id')->nullable()->after('id'); 
            
            // 2. Creamos la llave foránea
            $table->foreign('sede_id')->references('id')->on('sedes')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['sede_id']);
            $table->dropColumn('sede_id'); // También eliminamos la columna al revertir
        });
        Schema::dropIfExists('sedes');
    }
};