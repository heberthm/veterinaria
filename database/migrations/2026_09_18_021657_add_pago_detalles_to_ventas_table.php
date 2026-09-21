<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            if (!Schema::hasColumn('ventas', 'referencia_pago')) {
                $table->string('referencia_pago', 100)->nullable()->after('metodo_pago');
            }
            if (!Schema::hasColumn('ventas', 'detalle_pago')) {
                $table->json('detalle_pago')->nullable()->after('referencia_pago');
            }
            if (!Schema::hasColumn('ventas', 'monto_efectivo')) {
                $table->decimal('monto_efectivo', 12, 2)->nullable()->after('detalle_pago');
            }
            if (!Schema::hasColumn('ventas', 'monto_otro')) {
                $table->decimal('monto_otro', 12, 2)->nullable()->after('monto_efectivo');
            }
            if (!Schema::hasColumn('ventas', 'cambio')) {
                $table->decimal('cambio', 12, 2)->nullable()->after('monto_otro');
            }
        });
    }

    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['referencia_pago', 'detalle_pago', 'monto_efectivo', 'monto_otro', 'cambio']);
        });
    }
};