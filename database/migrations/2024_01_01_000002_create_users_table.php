<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Nullable: el super-admin de la SaaS no pertenece a ningún tenant
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('name', 60);
            $table->string('rol', 20);
            $table->string('email', 180)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('telefono')->nullable();
            $table->string('foto')->nullable();
            $table->string('especialidad')->nullable();
            $table->boolean('activo')->default(true);   
            $table->boolean('es_super_admin')->default(false);          
            $table->rememberToken();
            $table->timestamps();

            $table->index('tenant_id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
