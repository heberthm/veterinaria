<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vacuna;
use Carbon\Carbon;

class VacunaSeeder extends Seeder
{
    public function run()
    {
        $vacunas = [
            [
                'nombre'          => 'Vacuna Antirrábica',
                'laboratorio'     => 'Pfizer',
                'lote'            => 'LOT-ANT-001',
                'descripcion'     => 'Vacuna para la prevención de la rabia en perros y gatos.',
                'dosis_total'     => 1,
                'intervalo_dias'  => 365, // Anual
                'precio'          => 25000,
                'stock'           => 50,
                'stock_minimo'    => 10,
                'fecha_caducidad' => Carbon::now()->addYear()->format('Y-m-d'),
                'via_aplicacion'  => 'subcutanea',
                'activo'          => true,
            ],
            [
                'nombre'          => 'Vacuna Parvovirus',
                'laboratorio'     => 'Boehringer Ingelheim',
                'lote'            => 'LOT-PAR-002',
                'descripcion'     => 'Protección contra parvovirus canino.',
                'dosis_total'     => 2,
                'intervalo_dias'  => 21,
                'precio'          => 32000,
                'stock'           => 30,
                'stock_minimo'    => 5,
                'fecha_caducidad' => Carbon::now()->addMonths(10)->format('Y-m-d'),
                'via_aplicacion'  => 'subcutanea',
                'activo'          => true,
            ],
            [
                'nombre'          => 'Vacuna Moquillo Canino',
                'laboratorio'     => 'Zoetis',
                'lote'            => 'LOT-MOQ-003',
                'descripcion'     => 'Prevención del moquillo (Distemper).',
                'dosis_total'     => 2,
                'intervalo_dias'  => 21,
                'precio'          => 30000,
                'stock'           => 25,
                'stock_minimo'    => 5,
                'fecha_caducidad' => Carbon::now()->addMonths(8)->format('Y-m-d'),
                'via_aplicacion'  => 'subcutanea',
                'activo'          => true,
            ],
            [
                'nombre'          => 'Triple Felina',
                'laboratorio'     => 'Virbac',
                'lote'            => 'LOT-TRI-004',
                'descripcion'     => 'Protección contra rinotraqueítis, calicivirus y panleucopenia felina.',
                'dosis_total'     => 2,
                'intervalo_dias'  => 21,
                'precio'          => 45000,
                'stock'           => 15,
                'stock_minimo'    => 3,
                'fecha_caducidad' => Carbon::now()->addYear()->format('Y-m-d'),
                'via_aplicacion'  => 'intramuscular',
                'activo'          => true,
            ],
            [
                'nombre'          => 'Vacuna Leucemia Felina',
                'laboratorio'     => 'Merial',
                'lote'            => 'LOT-LEU-005',
                'descripcion'     => 'Prevención del virus de la leucemia felina (FeLV).',
                'dosis_total'     => 2,
                'intervalo_dias'  => 21,
                'precio'          => 38000,
                'stock'           => 20,
                'stock_minimo'    => 5,
                'fecha_caducidad' => Carbon::now()->addMonths(6)->format('Y-m-d'),
                'via_aplicacion'  => 'subcutanea',
                'activo'          => true,
            ],
            [
                'nombre'          => 'Vacuna Bordetella',
                'laboratorio'     => 'Merck',
                'lote'            => 'LOT-BOR-006',
                'descripcion'     => 'Prevención de la tos de las perreras.',
                'dosis_total'     => 1,
                'intervalo_dias'  => 365,
                'precio'          => 28000,
                'stock'           => 10,
                'stock_minimo'    => 2,
                'fecha_caducidad' => Carbon::now()->addMonths(12)->format('Y-m-d'),
                'via_aplicacion'  => 'intranasal',
                'activo'          => true,
            ],
        ];

        foreach ($vacunas as $vacuna) {
            // updateOrCreate busca por nombre y tenant_id para no duplicar si se corre dos veces
            Vacuna::updateOrCreate(
                [
                    'nombre'    => $vacuna['nombre'],
                    'tenant_id' => 1, // 🔥 Ajusta este ID al tenant de tu clínica
                ],
                array_merge($vacuna, [
                    'tenant_id' => 1,
                ])
            );
        }

        $this->command->info('✅ 6 Vacunas creadas exitosamente en el catálogo.');
    }
}