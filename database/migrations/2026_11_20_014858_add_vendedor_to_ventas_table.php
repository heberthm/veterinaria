<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Nombre del vendedor tal como estaba al momento de la venta
            if (!Schema::hasColumn('ventas', 'vendedor_nombre')) {
                $table->string('vendedor_nombre', 150)->nullable();
            }
            // Id del vendedor, solo si la tabla no tiene ninguna de las dos variantes
            if (!Schema::hasColumn('ventas', 'usuario_id') && !Schema::hasColumn('ventas', 'usurio_id')) {
                $table->unsignedBigInteger('usuario_id')->nullable();
            }
        });

        // Ventas anteriores: rellena el nombre desde la tabla users
        foreach (['usuario_id', 'usurio_id'] as $col) {
            if (Schema::hasColumn('ventas', $col)) {
                \Illuminate\Support\Facades\DB::statement(
                    "UPDATE ventas v JOIN users u ON u.id = v.`{$col}` SET v.vendedor_nombre = u.name WHERE v.vendedor_nombre IS NULL"
                );
            }
        }
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (Schema::hasColumn('ventas', 'vendedor_nombre')) {
                $table->dropColumn('vendedor_nombre');
            }
        });
    }
};