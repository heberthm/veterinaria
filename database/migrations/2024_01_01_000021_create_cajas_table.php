<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('nombre' ,80)->default('Caja Principal');
            $table->foreignId('usurio_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('monto_apertura', 12, 2)->default(0);
            $table->decimal('monto_cierre', 12, 2)->nullable();
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cajas');
    }
};
