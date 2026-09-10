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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('mascota_id')->constrained()->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            
            // Fechas y horas
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            
            // Tipo y estado
            $table->enum('tipo', [
                'consulta_general',
                'vacunacion',
                'emergencia',
                'cirugia',
                'odontologia',
                'laboratorio',
                'imagenologia',
                'estetica',
                'control',
                'desparasitacion',
                'certificado'
            ])->default('consulta_general');
            
            $table->enum('estado', [
                'agendada',
                'confirmada',
                'en_curso',
                'completada',
                'cancelada',
                'no_asistio'
            ])->default('agendada');
            
            // Información adicional
            $table->text('motivo');
            $table->text('observaciones')->nullable();
            $table->decimal('costo', 10, 2)->default(0);
            $table->enum('estado_pago', ['pendiente', 'pagado', 'parcial'])->default('pendiente');
            $table->boolean('recordatorio_enviado')->default(false);
            
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index(['fecha', 'estado']);
            $table->index(['usuario_id', 'fecha']);
            $table->index(['cliente_id', 'fecha']);
            $table->index(['mascota_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('citas');
    }
};