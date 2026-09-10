<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categorias_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nombre' ,90);
            $table->string('icono')->nullable();
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_productos')->nullOnDelete();
            $table->string('codigo')->nullable();
            $table->string('nombre' ,80);
            $table->text('descripcion' ,180)->nullable();
            $table->enum('tipo', ['producto', 'servicio', 'paquete'])->default('producto');
            $table->decimal('precio_compra', 12, 2)->default(0);
            $table->decimal('precio_venta', 12, 2)->default(0);
            $table->decimal('iva_porcentaje', 5, 2)->default(19);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('unidad_medida' ,16)->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'nombre']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
        Schema::dropIfExists('categorias_productos');
    }
};
