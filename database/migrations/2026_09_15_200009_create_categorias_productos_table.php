<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_productos', function (Blueprint $table) {
            $table->id();
            
            // 🔥 Relación con el tenant (multi-tenant)
            $table->foreignId('tenant_id')
                  ->nullable()
                  ->constrained('tenants')
                  ->onDelete('cascade');
            
            // 🔥 Información de la categoría
            $table->string('nombre');
            $table->string('codigo', 50)->nullable()->unique();
            $table->text('descripcion')->nullable();
            
            // 🔥 Tipo de categoría (para clasificar productos)
            $table->enum('tipo', [
                'producto',      // Alimentos, medicamentos, etc.
                'servicio',      // Consultas, cirugías, etc.
                'paquete',       // Paquetes combinados
                'otro'           // Otros
            ])->default('producto');
            
            // 🔥 Estado
            $table->boolean('activo')->default(true);
            
            // 🔥 Orden para mostrar en listas
            $table->integer('orden')->default(0);
            
            // 🔥 Imagen/icono de la categoría (opcional)
            $table->string('imagen')->nullable();
            $table->string('icono', 50)->nullable(); // Ej: "fa-pills"
            $table->string('color', 20)->nullable(); // Ej: "#3B82F6"
            
            $table->timestamps();
            $table->softDeletes(); // Para eliminación lógica
            
            // 🔥 Índices para búsquedas rápidas
            $table->index(['tenant_id', 'activo']);
            $table->index(['tenant_id', 'tipo']);
            $table->index('activo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorias_productos');
    }
};