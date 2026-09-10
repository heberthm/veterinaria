<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('vacunas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->string('laboratorio')->nullable();
            $table->string('lote')->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('dosis_total')->default(1);
            $table->integer('intervalo_dias')->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->date('fecha_caducidad')->nullable();
            $table->string('via_aplicacion')->default('subcutanea');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index(['nombre', 'activo']);
            $table->index('fecha_caducidad');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vacunas');
    }
};