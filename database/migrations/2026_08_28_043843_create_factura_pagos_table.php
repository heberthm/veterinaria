<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('factura_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained()->onDelete('cascade');
            $table->date('fecha_pago');
            $table->decimal('monto', 12, 2);
            $table->enum('metodo_pago', [
                'efectivo', 'tarjeta_credito', 'tarjeta_debito', 
                'transferencia', 'nequi', 'daviplata', 'qr'
            ])->default('efectivo');
            $table->string('referencia')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->timestamps();
            
            $table->index('factura_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('factura_pagos');
    }
};