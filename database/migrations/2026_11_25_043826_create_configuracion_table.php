<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Una fila por clínica (tenant) con sus datos y parámetros generales.
         */
        if (!Schema::hasTable('configuraciones')) {
            Schema::create('configuraciones', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->unique();

                // Datos de la clínica
                $table->string('nombre_clinica', 120)->default('VetFlow');
                $table->string('razon_social', 150)->nullable();
                $table->string('nit', 30)->nullable();
                $table->string('direccion', 255)->nullable();
                $table->string('ciudad', 100)->nullable();
                $table->string('telefono', 30)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('sitio_web', 150)->nullable();
                $table->string('logo')->nullable();

                // Facturación
                $table->decimal('iva_porcentaje', 5, 2)->default(19);
                $table->string('prefijo_factura', 10)->default('POS-');
                $table->string('moneda', 5)->default('COP');
                $table->string('formato_impresion', 10)->default('ticket'); // ticket | carta
                $table->string('pie_factura', 500)->nullable();

                // Agenda
                $table->unsignedSmallInteger('duracion_cita_min')->default(30);
                $table->json('horario')->nullable();

                $table->timestamps();
            });
        }

        /**
         * Catálogo genérico por clínica: especies, razas, servicios,
         * tipos de cita, métodos de pago y categorías de producto.
         */
        if (!Schema::hasTable('catalogos')) {
            Schema::create('catalogos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id')->index();
                $table->string('tipo', 30)->index();
                $table->string('nombre', 100);
                $table->string('descripcion', 255)->nullable();
                $table->decimal('valor', 12, 2)->nullable();          // precio (servicios)
                $table->unsignedBigInteger('padre_id')->nullable();   // raza -> especie
                $table->unsignedInteger('orden')->default(0);
                $table->boolean('activo')->default(true);
                $table->timestamps();

                $table->index(['tenant_id', 'tipo']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogos');
        Schema::dropIfExists('configuraciones');
    }
};