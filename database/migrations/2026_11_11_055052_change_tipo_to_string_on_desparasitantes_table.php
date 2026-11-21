<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('desparasitantes', function (Blueprint $table) {
            // 🔥 Cambiar a string para aceptar 'interna', 'externa', 'mixta', etc.
            $table->string('tipo', 50)->change();
        });
    }

    public function down()
    {
        Schema::table('desparasitantes', function (Blueprint $table) {
            $table->enum('tipo', ['interno', 'externo', 'mixto'])->change(); // Ajusta a tu ENUM original
        });
    }
};