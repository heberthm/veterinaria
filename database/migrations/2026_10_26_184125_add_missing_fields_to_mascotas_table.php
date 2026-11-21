<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mascotas', function (Blueprint $table) {
            
            // 🔥 Agregar los campos que faltan validando que no existan
            
            if (!Schema::hasColumn('mascotas', 'genero')) {
                $table->enum('genero', ['macho', 'hembra'])->nullable()->after('fecha_nacimiento');
            }

            if (!Schema::hasColumn('mascotas', 'color')) {
                $table->string('color', 50)->nullable()->after('raza');
            }

            if (!Schema::hasColumn('mascotas', 'peso')) {
                $table->decimal('peso', 5, 2)->nullable()->after('genero');
            }

            if (!Schema::hasColumn('mascotas', 'numero_chip')) {
                $table->string('numero_chip', 50)->nullable()->after('peso');
            }

            if (!Schema::hasColumn('mascotas', 'estado')) {
                $table->string('estado', 20)->default('activo')->after('numero_chip');
            }

            if (!Schema::hasColumn('mascotas', 'esterilizado')) {
                $table->boolean('esterilizado')->default(false)->after('estado');
            }

            if (!Schema::hasColumn('mascotas', 'alergias')) {
                $table->text('alergias')->nullable()->after('esterilizado');
            }

            if (!Schema::hasColumn('mascotas', 'enfermedades_cronicas')) {
                $table->text('enfermedades_cronicas')->nullable()->after('alergias');
            }

            if (!Schema::hasColumn('mascotas', 'notas')) {
                $table->text('notas')->nullable()->after('enfermedades_cronicas');
            }
        });
    }

    public function down()
    {
        Schema::table('mascotas', function (Blueprint $table) {
            $table->dropColumn([
                'genero', 'color', 'peso', 'numero_chip', 'estado', 
                'esterilizado', 'alergias', 'enfermedades_cronicas', 'notas'
            ]);
        });
    }
};