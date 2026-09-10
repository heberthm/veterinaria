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
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nombre' ,90);
            $table->string('nit' ,18)->nullable();
            $table->string('telefono' ,18)->nullable();
            $table->string('email' ,180)->nullable();
            $table->string('direccion' ,150)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('proveedor_id')->constrained('proveedores')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('numero_factura_proveedor' ,18)->nullable();
            $table->date('fecha_compra');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva_valor', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['pendiente', 'recibida', 'anulada'])->default('pendiente');
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'fecha_compra']);
        });

        Schema::create('compra_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('compra_items');
        Schema::dropIfExists('compras');
        Schema::dropIfExists('proveedores');
    }
};
