<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('compras', function (Blueprint $table) {
            // 🔥 Agregamos la columna JSON
            $table->json('items_json')->nullable()->after('total');
        });
    }

    public function down()
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropColumn('items_json');
        });
    }
};