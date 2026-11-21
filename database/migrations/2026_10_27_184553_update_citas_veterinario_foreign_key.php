<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('citas', function (Blueprint $table) {
            // Borrar la llave foránea anterior (si existe)
            $table->dropForeign(['veterinario_id']);
            
            // Volver a crearla apuntando a la nueva tabla 'veterinarios'
            $table->foreign('veterinario_id')
                  ->references('id')
                  ->on('veterinarios')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['veterinario_id']);
            $table->foreign('veterinario_id')->references('id')->on('users');
        });
    }
};