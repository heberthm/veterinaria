<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, BelongsToTenant;

    protected $guard_name = 'web';

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'telefono',
        'foto',
        'especialidad',
        'activo',
        'es_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
        'es_super_admin' => 'boolean',
    ];

    /**
     * ============================================
     * MÉTODOS REQUERIDOS POR ADMINLTE
     * ============================================
     */

    /**
     * 🔥 METODO 1: URL de la imagen de perfil
     * AdminLTE llama a este método para obtener la imagen del usuario
     */
    public function adminlte_image()
    {
        // Si el usuario tiene foto de perfil
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        
        // Imagen por defecto con iniciales
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF&size=128';
    }

    /**
     * 🔥 METODO 2: URL del perfil
     * AdminLTE llama a este método para el enlace del perfil
     */
    public function adminlte_profile_url()
    {
        return url('/perfil/' . $this->id);
    }

    /**
     * 🔥 METODO 3: Descripción del perfil
     * AdminLTE llama a este método para mostrar el rol/descripción
     */
    public function adminlte_desc()
    {
        if ($this->hasRole('Veterinario')) {
            return 'Veterinario';
        } elseif ($this->hasRole('Admin')) {
            return 'Administrador';
        } elseif ($this->hasRole('Recepcionista')) {
            return 'Recepcionista';
        } elseif ($this->hasRole('Cajero')) {
            return 'Cajero';
        }
        return 'Usuario';
    }

    /**
     * ============================================
     * MÉTODOS ADICIONALES
     * ============================================
     */

    /**
     * Alias para adminlte_image() (por si se llama con otro nombre)
     */
    public function adminlte_image_url()
    {
        return $this->adminlte_image();
    }

    /**
     * Relación con el tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Verificar si es super administrador
     */
    public function esSuperAdmin(): bool
    {
        return (bool) $this->es_super_admin;
    }

    /**
     * Verificar si es veterinario
     */
    public function esVeterinario(): bool
    {
        return $this->hasRole('Veterinario');
    }

    
    /**
     * Verificar si es administrador
     */
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    /**
     * Verificar si es recepcionista
     */
    public function isReceptionist()
    {
        return $this->hasRole('Recepcionista');
    }

    /**
     * Verificar si es cajero
     */
    public function isCashier()
    {
        return $this->hasRole('Cajero');
    }
}