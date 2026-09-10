<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('caja_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained()->onDelete('cascade');
            $table->foreignId('caja_apertura_id')->constrained()->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('venta_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('factura_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->enum('categoria', [
                'venta', 'factura', 'abono', 'gasto', 
                'retiro', 'ajuste', 'apertura', 'cierre'
            ]);
            $table->decimal('monto', 12, 2);
            $table->decimal('saldo_anterior', 12, 2);
            $table->decimal('saldo_nuevo', 12, 2);
            $table->string('metodo_pago')->nullable();
            $table->string('referencia')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
            
            $table->index(['caja_id', 'created_at']);
            $table->index(['tipo', 'categoria']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('caja_movimientos');
    }
};