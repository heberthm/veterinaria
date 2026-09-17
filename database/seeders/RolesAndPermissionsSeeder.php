<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        Permission::create(['name' => 'ver_dashboard']);
        Permission::create(['name' => 'ver_clientes']);
        Permission::create(['name' => 'crear_clientes']);
        Permission::create(['name' => 'editar_clientes']);
        Permission::create(['name' => 'eliminar_clientes']);
        Permission::create(['name' => 'ver_mascotas']);
        Permission::create(['name' => 'crear_mascotas']);
        Permission::create(['name' => 'editar_mascotas']);
        Permission::create(['name' => 'eliminar_mascotas']);
        Permission::create(['name' => 'ver_citas']);
        Permission::create(['name' => 'crear_citas']);
        Permission::create(['name' => 'editar_citas']);
        Permission::create(['name' => 'eliminar_citas']);
        Permission::create(['name' => 'ver_historias']);
        Permission::create(['name' => 'crear_historias']);
        Permission::create(['name' => 'editar_historias']);
        Permission::create(['name' => 'eliminar_historias']);
        Permission::create(['name' => 'ver_productos']);
        Permission::create(['name' => 'crear_productos']);
        Permission::create(['name' => 'editar_productos']);
        Permission::create(['name' => 'eliminar_productos']);
        Permission::create(['name' => 'ver_ventas']);
        Permission::create(['name' => 'crear_ventas']);
        Permission::create(['name' => 'editar_ventas']);
        Permission::create(['name' => 'eliminar_ventas']);
        Permission::create(['name' => 'ver_reportes']);
        Permission::create(['name' => 'ver_usuarios']);
        Permission::create(['name' => 'crear_usuarios']);
        Permission::create(['name' => 'editar_usuarios']);
        Permission::create(['name' => 'eliminar_usuarios']);
        Permission::create(['name' => 'ver_configuracion']);
        Permission::create(['name' => 'editar_configuracion']);
        Permission::firstOrCreate(['name' => 'ver_caja', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'gestionar_caja', 'guard_name' => 'web']);


        // Crear roles
        $superAdmin = Role::create(['name' => 'SuperAdmin']);
        $admin = Role::create(['name' => 'Admin']);
        $veterinario = Role::create(['name' => 'Veterinario']);
        $recepcionista = Role::create(['name' => 'Recepcionista']);
        $cajero = Role::create(['name' => 'Cajero']);
         

        // Asignar permisos a roles
        $superAdmin->givePermissionTo(Permission::all());

        $admin->givePermissionTo([
            'ver_dashboard',
            'ver_clientes', 'crear_clientes', 'editar_clientes', 'eliminar_clientes',
            'ver_mascotas', 'crear_mascotas', 'editar_mascotas', 'eliminar_mascotas',
            'ver_citas', 'crear_citas', 'editar_citas', 'eliminar_citas',
            'ver_historias', 'crear_historias', 'editar_historias', 'eliminar_historias',
            'ver_productos', 'crear_productos', 'editar_productos', 'eliminar_productos',
            'ver_ventas', 'crear_ventas', 'editar_ventas', 'eliminar_ventas',
            'ver_reportes', 'ver_usuarios', 'ver_configuracion',
        ]);

        $veterinario->givePermissionTo([
            'ver_dashboard',
            'ver_clientes', 'crear_clientes', 'editar_clientes',
            'ver_mascotas', 'crear_mascotas', 'editar_mascotas',
            'ver_citas', 'crear_citas', 'editar_citas',
            'ver_historias', 'crear_historias', 'editar_historias',
            'ver_productos', 'ver_ventas',
        ]);

        $recepcionista->givePermissionTo([
            'ver_dashboard',
            'ver_clientes', 'crear_clientes', 'editar_clientes',
            'ver_mascotas', 'crear_mascotas', 'editar_mascotas',
            'ver_citas', 'crear_citas', 'editar_citas',
            'ver_productos',
        ]);

        $cajero->givePermissionTo([
            'ver_dashboard',
            'ver_clientes', 'ver_mascotas',
            'ver_citas',
            'ver_productos', 'ver_ventas', 'crear_ventas', 'editar_ventas',
        ]);

        // ============================================
        // 🔥 CREAR USUARIO SUPER ADMIN CON ROL
        // ============================================
        
        // Método 1: Usar firstOrCreate con todos los campos
        $user = User::firstOrCreate(
            ['email' => 'heberth.mazuera@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password123'),
                'rol' => '´Super Admin', // ← AGREGAR ROL
                'activo' => true,
                'telefono' => '3000000000',
                'es_super_admin' => true,
            ]
        );
        $user->assignRole($superAdmin);

        // ============================================
        // CREAR USUARIO VETERINARIO
        // ============================================
        $userVet = User::firstOrCreate(
            ['email' => 'veterinario@vetcloud.com'],
            [
                'name' => 'Dr. Carlos Ramírez',
                'password' => bcrypt('password123'),
                'rol' => 'veterinarian', // ← AGREGAR ROL
                'activo' => true,
                'telefono' => '3000000001',
                'especialidad' => 'Medicina General',
                'es_super_admin' => false,
            ]
        );
        $userVet->assignRole($veterinario);

        // ============================================
        // CREAR USUARIO RECEPCIONISTA
        // ============================================
        $userRecep = User::firstOrCreate(
            ['email' => 'recepcionista@vetcloud.com'],
            [
                'name' => 'Laura Gómez',
                'password' => bcrypt('password123'),
                'rol' => 'receptionist', // ← AGREGAR ROL
                'activo' => true,
                'telefono' => '3000000002',
                'es_super_admin' => false,
            ]
        );
        $userRecep->assignRole($recepcionista);

        // ============================================
        // CREAR USUARIO CAJERO
        // ============================================
        $userCajero = User::firstOrCreate(
            ['email' => 'cajero@vetcloud.com'],
            [
                'name' => 'Pedro Martínez',
                'password' => bcrypt('password123'),
                'rol' => 'cashier', // ← AGREGAR ROL
                'activo' => true,
                'telefono' => '3000000003',
                'es_super_admin' => false,
            ]
        );
        $userCajero->assignRole($cajero);

        $this->command->info('============================================');
        $this->command->info('✅ Roles y permisos creados exitosamente!');
        $this->command->info('============================================');
        $this->command->info('👑 SuperAdmin: admin@vetcloud.com / password123');
        $this->command->info('🩺 Veterinario: veterinario@vetcloud.com / password123');
        $this->command->info('📋 Recepcionista: recepcionista@vetcloud.com / password123');
        $this->command->info('💰 Cajero: cajero@vetcloud.com / password123');
        $this->command->info('============================================');
    }
}