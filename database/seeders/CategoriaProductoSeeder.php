<?php

namespace Database\Seeders;

use App\Models\CategoriaProducto;
use Illuminate\Database\Seeder;

class CategoriaProductoSeeder extends Seeder
{
    public function run()
    {
        $categorias = [
            [
                'nombre' => 'Alimentos',
                'codigo' => 'ALI',
                'descripcion' => 'Alimentos y concentrados para mascotas',
                'tipo' => 'producto',
                'icono' => 'fa-bone',
                'color' => '#F59E0B',
                'orden' => 1,
            ],
            [
                'nombre' => 'Medicamentos',
                'codigo' => 'MED',
                'descripcion' => 'Medicamentos y vacunas',
                'tipo' => 'producto',
                'icono' => 'fa-pills',
                'color' => '#3B82F6',
                'orden' => 2,
            ],
            [
                'nombre' => 'Accesorios',
                'codigo' => 'ACC',
                'descripcion' => 'Collares, juguetes y accesorios',
                'tipo' => 'producto',
                'icono' => 'fa-ring',
                'color' => '#8B5CF6',
                'orden' => 3,
            ],
            [
                'nombre' => 'Higiene',
                'codigo' => 'HIG',
                'descripcion' => 'Shampoos, arena sanitaria, etc.',
                'tipo' => 'producto',
                'icono' => 'fa-pump-soap',
                'color' => '#06B6D4',
                'orden' => 4,
            ],
            [
                'nombre' => 'Consultas',
                'codigo' => 'CON',
                'descripcion' => 'Consultas veterinarias',
                'tipo' => 'servicio',
                'icono' => 'fa-stethoscope',
                'color' => '#22C55E',
                'orden' => 5,
            ],
            [
                'nombre' => 'Cirugías',
                'codigo' => 'CIR',
                'descripcion' => 'Procedimientos quirúrgicos',
                'tipo' => 'servicio',
                'icono' => 'fa-scalpel',
                'color' => '#EF4444',
                'orden' => 6,
            ],
            [
                'nombre' => 'Estética',
                'codigo' => 'EST',
                'descripcion' => 'Baños, cortes y estética canina',
                'tipo' => 'servicio',
                'icono' => 'fa-cut',
                'color' => '#EC4899',
                'orden' => 7,
            ],
            [
                'nombre' => 'Paquetes',
                'codigo' => 'PAQ',
                'descripcion' => 'Paquetes combinados de productos y servicios',
                'tipo' => 'paquete',
                'icono' => 'fa-box-open',
                'color' => '#F97316',
                'orden' => 8,
            ],
        ];

        foreach ($categorias as $categoria) {
            CategoriaProducto::updateOrCreate(
                ['codigo' => $categoria['codigo']],
                array_merge($categoria, [
                    'tenant_id' => 1, // Ajustar según tu tenant
                    'activo' => true,
                ])
            );
        }

        $this->command->info('✅ Categorías de productos creadas exitosamente.');
    }
}