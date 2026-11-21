<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desparasitante;
use Carbon\Carbon;

class DesparasitanteSeeder extends Seeder
{
    public function run()
    {
        $desparasitantes = [
            [
                'nombre'           => 'Bravecto',
                'laboratorio'      => 'MSD Animal Health',
                'lote'             => 'LOT-BRV-001',
                'descripcion'      => 'Tableta masticable para el tratamiento y prevención de pulgas y garrapatas.',
                'tipo' => 'externo', 
               
                'principio_activo' => 'Fluralaner',
                'precio'           => 65000,
                'stock'            => 20,
                'stock_minimo'     => 5,
                'fecha_caducidad'  => Carbon::now()->addYear()->format('Y-m-d'),
                'via_aplicacion'   => 'oral',
                'activo'           => true,
            ],
            [
                'nombre'           => 'NexGard',
                'laboratorio'      => 'Boehringer Ingelheim',
                'lote'             => 'LOT-NXG-002',
                'descripcion'      => 'Tableta masticable para perros, elimina pulgas y garrapatas.',
                'tipo'             => 'externa',
                'principio_activo' => 'Afoxolaner',
                'precio'           => 58000,
                'stock'            => 25,
                'stock_minimo'     => 5,
                'fecha_caducidad'  => Carbon::now()->addMonths(10)->format('Y-m-d'),
                'via_aplicacion'   => 'oral',
                'activo'           => true,
            ],
            [
                'nombre'           => 'Drontal Plus',
                'laboratorio'      => 'Bayer',
                'lote'             => 'LOT-DRT-003',
                'descripcion'      => 'Desparasitante interno de amplio espectro para perros y gatos.',
                'tipo'             => 'interna',
                'principio_activo' => 'Praziquantel + Pirantel + Febantel',
                'precio'           => 22000,
                'stock'            => 40,
                'stock_minimo'     => 10,
                'fecha_caducidad'  => Carbon::now()->addMonths(8)->format('Y-m-d'),
                'via_aplicacion'   => 'oral',
                'activo'           => true,
            ],
            [
                'nombre'           => 'Milbemax',
                'laboratorio'      => 'Elanco',
                'lote'             => 'LOT-MLB-004',
                'descripcion'      => 'Desparasitante interno para gatos y perros de razas pequeñas.',
                'tipo'             => 'interna',
                'principio_activo' => 'Milbemicina oxima + Praziquantel',
                'precio'           => 28000,
                'stock'            => 15,
                'stock_minimo'     => 5,
                'fecha_caducidad'  => Carbon::now()->addYear()->format('Y-m-d'),
                'via_aplicacion'   => 'oral',
                'activo'           => true,
            ],
            [
                'nombre'           => 'Frontline Plus',
                'laboratorio'      => 'Boehringer Ingelheim',
                'lote'             => 'LOT-FRT-005',
                'descripcion'      => 'Pipeta para el control de pulgas y garrapatas en perros y gatos.',
                'tipo'             => 'externa',
                'principio_activo' => 'Fipronil + (S)-metopreno',
                'precio'           => 35000,
                'stock'            => 30,
                'stock_minimo'     => 10,
                'fecha_caducidad'  => Carbon::now()->addMonths(12)->format('Y-m-d'),
                'via_aplicacion'   => 'topica',
                'activo'           => true,
            ],
            [
                'nombre'           => 'Endogard',
                'laboratorio'      => 'Virbac',
                'lote'             => 'LOT-END-006',
                'descripcion'      => 'Desparasitante mixto para perros y gatos.',
                'tipo'             => 'mixta',
                'principio_activo' => 'Praziquantel + Pirantel + Febantel',
                'precio'           => 26000,
                'stock'            => 18,
                'stock_minimo'     => 5,
                'fecha_caducidad'  => Carbon::now()->addMonths(6)->format('Y-m-d'),
                'via_aplicacion'   => 'oral',
                'activo'           => true,
            ],
        ];

        foreach ($desparasitantes as $desp) {
            // updateOrCreate busca por nombre y tenant_id para evitar duplicados
            Desparasitante::updateOrCreate(
                [
                    'nombre'    => $desp['nombre'],
                    'tenant_id' => 1, // 🔥 Ajusta este ID al tenant de tu clínica
                ],
                array_merge($desp, [
                    'tenant_id' => 1,
                ])
            );
        }

        $this->command->info('✅ 6 Desparasitantes creados exitosamente en el catálogo.');
    }
}