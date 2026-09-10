<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->enum('tipo_documento', ['CC', 'CE', 'NIT', 'PAS'])->default('CC');
            $table->string('numero_documento');
            $table->string('nombres');
            $table->string('apellidos')->nullable();
            $table->string('email')->nullable();           
            $table->string('celular')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->text('notas')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'numero_documento']);
            $table->index(['tenant_id', 'nombres']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};
