<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mascotas', function (Blueprint $table) {
            // 🔥 Agregar 'numero_chip' si no existe
            if (!Schema::hasColumn('mascotas', 'numero_chip')) {
                $table->string('numero_chip', 50)->nullable()->after('peso');
            }

            // 🔥 Agregar 'esterilizado' si no existe
            if (!Schema::hasColumn('mascotas', 'esterilizado')) {
                $table->boolean('esterilizado')->default(false)->after('estado');
            }

            // 🔥 Agregar 'alergias' si no existe
            if (!Schema::hasColumn('mascotas', 'alergias')) {
                $table->text('alergias')->nullable()->after('esterilizado');
            }

            // 🔥 Agregar 'enfermedades_cronicas' si no existe
            if (!Schema::hasColumn('mascotas', 'enfermedades_cronicas')) {
                $table->text('enfermedades_cronicas')->nullable()->after('alergias');
            }

            // 🔥 Agregar 'notas' si no existe
            if (!Schema::hasColumn('mascotas', 'notas')) {
                $table->text('notas')->nullable()->after('enfermedades_cronicas');
            }
        });
    }

    public function down()
    {
        Schema::table('mascotas', function (Blueprint $table) {
            $table->dropColumn([
                'numero_chip', 
                'esterilizado', 
                'alergias', 
                'enfermedades_cronicas', 
                'notas'
            ]);
        });
    }
};