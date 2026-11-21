<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sede;

class SedeSeeder extends Seeder
{
    public function run()
    {
        $sedes = [
            [
                'nombre'    => 'Sede Principal',
                'direccion' => 'Calle 10 # 25 - 40, Centro',
                'principal' => true,
                'activa'    => true,
            ],
            [
                'nombre'    => 'Sede Norte',
                'direccion' => 'Avenida 5 # 12 - 80, Barrio Norte',
                'principal' => false,
                'activa'    => true,
            ],
            [
                'nombre'    => 'Sede Sur',
                'direccion' => 'Carrera 15 # 45 - 10, Barrio Sur',
                'principal' => false,
                'activa'    => true,
            ],
        ];

        foreach ($sedes as $sede) {
            // updateOrCreate busca por nombre y tenant_id para evitar duplicados
            Sede::updateOrCreate(
                [
                    'nombre'    => $sede['nombre'],
                    'tenant_id' => 1, // 🔥 Ajusta este ID al tenant de tu clínica
                ],
                array_merge($sede, [
                    'tenant_id' => 1,
                ])
            );
        }

        $this->command->info('✅ 3 Sedes creadas exitosamente.');
    }
}