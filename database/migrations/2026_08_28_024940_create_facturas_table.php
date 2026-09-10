<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('venta_id')->nullable()->constrained()->onDelete('set null');
            $table->string('numero_factura', 50)->unique();
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('tipo', ['venta', 'servicio', 'consulta', 'proforma'])->default('venta');
            $table->enum('estado', ['pendiente', 'pagada', 'anulada', 'vencida'])->default('pendiente');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('descuento', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('abonado', 12, 2)->default(0);
            $table->decimal('saldo', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->string('metodo_pago')->nullable();
            $table->string('referencia_pago')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->json('configuracion')->nullable();
            $table->timestamps();
            
            $table->index(['cliente_id', 'estado']);
            $table->index(['fecha_emision', 'estado']);
            $table->index('numero_factura');
        });
    }

    public function down()
    {
        Schema::dropIfExists('facturas');
    }
};