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
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->decimal('saldo_inicial', 12, 2)->default(0);
            $table->decimal('saldo_actual', 12, 2)->default(0);
            $table->enum('estado', ['abierta', 'cerrada'])->default('cerrada');
            $table->foreignId('usuario_apertura_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('usuario_cierre_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_apertura')->nullable();
            $table->timestamp('fecha_cierre')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cajas');
    }
};