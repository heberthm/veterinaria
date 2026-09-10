<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('caja_aperturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained()->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->decimal('saldo_inicial', 12, 2);
            $table->decimal('saldo_final', 12, 2)->nullable();
            $table->timestamp('fecha_apertura');
            $table->timestamp('fecha_cierre')->nullable();
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('caja_aperturas');
    }
};