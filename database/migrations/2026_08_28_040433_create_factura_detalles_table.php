<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('factura_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained()->onDelete('cascade');
            $table->foreignId('producto_id')->nullable()->constrained()->onDelete('set null');
            $table->string('descripcion');
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('tipo')->default('producto'); // producto, servicio, consulta
            $table->timestamps();
            
            $table->index('factura_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('factura_detalles');
    }
};