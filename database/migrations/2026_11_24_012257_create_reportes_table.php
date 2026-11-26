<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Historial de reportes generados (pantalla, PDF o CSV).
     * Los datos de cada reporte se calculan en vivo desde ventas, compras,
     * productos, citas, mascotas y clientes; esta tabla solo guarda quién
     * generó qué reporte, con qué filtros y cuándo.
     */
    public function up(): void
    {
        if (Schema::hasTable('reportes')) {
            return;
        }

        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('tipo', 40)->index();          // ventas, compras, inventario, citas...
            $table->string('nombre', 150);                // nombre legible del reporte
            $table->date('fecha_desde')->nullable();
            $table->date('fecha_hasta')->nullable();
            $table->string('formato', 10)->default('pantalla'); // pantalla | pdf | csv
            $table->json('filtros')->nullable();          // filtros extra (reservado)
            $table->unsignedInteger('total_registros')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};