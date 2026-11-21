<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    public function run()
    {
        $proveedores = [
            [
                'nombre'            => 'Distribuidora Veterinaria El Sol',
                'nit'               => '900123456-1',
                'email'             => 'ventas@elsolvet.com',
                'telefono'          => '6012345678',
                'direccion'         => 'Calle 15 # 45 - 20',
                'ciudad'            => 'Bogotá',
                'contacto_nombre'   => 'Carlos Pérez',
                'contacto_telefono' => '3101234567',
            ],
            [
                'nombre'            => 'Bayer Animal Health Colombia',
                'nit'               => '860014561-2',
                'email'             => 'contacto@bayer.com',
                'telefono'          => '6019876543',
                'direccion'         => 'Avenida 68 # 22 - 10',
                'ciudad'            => 'Bogotá',
                'contacto_nombre'   => 'María Gómez',
                'contacto_telefono' => '3159876543',
            ],
            [
                'nombre'            => 'VetPharma Distribuciones',
                'nit'               => '901234567-3',
                'email'             => 'compras@vetpharma.com',
                'telefono'          => '6023334444',
                'direccion'         => 'Carrera 4 # 10 - 50',
                'ciudad'            => 'Medellín',
                'contacto_nombre'   => 'Juan Rodríguez',
                'contacto_telefono' => '3113334444',
            ],
            [
                'nombre'            => 'Agroveterinaria Central',
                'nit'               => '890987654-4',
                'email'             => 'ventas@agrocentral.com',
                'telefono'          => '6025556666',
                'direccion'         => 'Calle 80 # 50 - 30',
                'ciudad'            => 'Cali',
                'contacto_nombre'   => 'Ana Martínez',
                'contacto_telefono' => '3205556666',
            ],
            [
                'nombre'            => 'Zoetis Colombia S.A.S.',
                'nit'               => '830123456-5',
                'email'             => 'pedidos@zoetis.com',
                'telefono'          => '6017778888',
                'direccion'         => 'Autopista Norte # 120 - 15',
                'ciudad'            => 'Bogotá',
                'contacto_nombre'   => 'Pedro Silva',
                'contacto_telefono' => '3167778888',
            ],
        ];

        foreach ($proveedores as $proveedor) {
            // updateOrCreate busca por NIT y tenant_id para evitar duplicados si se corre dos veces
            Proveedor::updateOrCreate(
                [
                    'nit'       => $proveedor['nit'],
                    'tenant_id' => 1, // 🔥 Ajusta este ID al tenant de tu clínica
                ],
                array_merge($proveedor, [
                    'tenant_id' => 1,
                    'activo'    => true,
                ])
            );
        }

        $this->command->info('✅ 5 Proveedores creados exitosamente.');
    }
}