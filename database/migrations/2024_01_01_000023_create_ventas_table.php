<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->string('consecutivo' ,12);           // POS-000125
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('mascota_id')->nullable()->constrained('mascotas')->nullOnDelete();
            $table->foreignId('usurio_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_valor', 12, 2)->default(0);
            $table->decimal('iva_valor', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'mixto'])->default('efectivo');
            $table->enum('estado', ['pagada', 'anulada'])->default('pagada');
            $table->text('observacion')->nullable();
            $table->foreignId('factura_id')->nullable()->constrained('facturas')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tenant_id', 'consecutivo']);
            $table->index(['tenant_id', 'created_at']);
        });

        Schema::create('venta_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->string('nombre_producto' ,120);
            $table->decimal('precio_unitario', 12, 2);
            $table->integer('cantidad')->default(1);
            $table->decimal('iva_porcentaje', 5, 2)->default(19);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('venta_items');
        Schema::dropIfExists('ventas');
    }
};
