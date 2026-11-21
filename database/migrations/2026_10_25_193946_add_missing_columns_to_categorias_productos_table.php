<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categorias_productos', function (Blueprint $table) {
            // 🔥 Agregar 'codigo' si no existe
            if (!Schema::hasColumn('categorias_productos', 'codigo')) {
                $table->string('codigo', 50)->nullable()->unique()->after('nombre');
            }

            // 🔥 Agregar 'descripcion' si no existe
            if (!Schema::hasColumn('categorias_productos', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('codigo');
            }

            // 🔥 Agregar 'tipo' si no existe
            if (!Schema::hasColumn('categorias_productos', 'tipo')) {
                $table->enum('tipo', ['producto', 'servicio', 'paquete', 'otro'])->default('producto')->after('descripcion');
            }

            // 🔥 Agregar 'activo' si no existe
            if (!Schema::hasColumn('categorias_productos', 'activo')) {
                $table->boolean('activo')->default(true)->after('tipo');
            }

            // 🔥 Agregar 'orden' si no existe
            if (!Schema::hasColumn('categorias_productos', 'orden')) {
                $table->integer('orden')->default(0)->after('activo');
            }

            // 🔥 Agregar 'imagen' si no existe
            if (!Schema::hasColumn('categorias_productos', 'imagen')) {
                $table->string('imagen')->nullable()->after('orden');
            }

            // 🔥 Agregar 'color' si no existe
            if (!Schema::hasColumn('categorias_productos', 'color')) {
                $table->string('color', 20)->nullable()->after('icono');
            }

            // 🔥 Agregar 'deleted_at' (SoftDeletes) si no existe
            if (!Schema::hasColumn('categorias_productos', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down()
    {
        Schema::table('categorias_productos', function (Blueprint $table) {
            // En caso de rollback, eliminamos las columnas agregadas
            $table->dropColumn([
                'codigo', 
                'descripcion', 
                'tipo', 
                'activo', 
                'orden', 
                'imagen', 
                'color', 
                'deleted_at'
            ]);
        });
    }
};