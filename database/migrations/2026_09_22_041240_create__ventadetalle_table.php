<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventadetalle', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venta_id')
                  ->constrained('ventas')
                  ->cascadeOnDelete();

            $table->foreignId('producto_id')
                  ->nullable()
                  ->constrained('productos')
                  ->nullOnDelete();

            // Snapshot del nombre por si el producto cambia luego
            $table->string('nombre_producto');

            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('cantidad', 12, 2)->default(1);
            $table->decimal('iva_porcentaje', 5, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);

            $table->timestamps();

            $table->index('venta_id');
            $table->index('producto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventadetalle');
    }
};