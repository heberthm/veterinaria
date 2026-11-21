<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\CategoriaProducto;

class ProductoSeeder extends Seeder
{
    public function run()
    {
        // Buscamos las categorías para asignarles los productos correctamente
        $catAlimentos = CategoriaProducto::where('codigo', 'ALI')->first();
        $catMedicamentos = CategoriaProducto::where('codigo', 'MED')->first();
        $catAccesorios = CategoriaProducto::where('codigo', 'ACC')->first();
        $catHigiene = CategoriaProducto::where('codigo', 'HIG')->first();
        $catConsultas = CategoriaProducto::where('codigo', 'CON')->first();
        $catCirugias = CategoriaProducto::where('codigo', 'CIR')->first();
        $catEstetica = CategoriaProducto::where('codigo', 'EST')->first();
        $catPaquetes = CategoriaProducto::where('codigo', 'PAQ')->first();

        // Array con los 10 productos
        $productos = [
            // --- PRODUCTOS (Alimentos) ---
            [
                'codigo' => 'PROD-001',
                'nombre' => 'Concentrado Premium Perro Adulto 10kg',
                'descripcion' => 'Alimento balanceado con pollo y arroz',
                'precio_venta' => 85000,
                'precio_compra' => 60000,
                'stock' => 15,
                'iva_porcentaje' => 19,
                'categoria_id' => $catAlimentos->id ?? null,
                'tipo' => 'producto',
            ],
            [
                'codigo' => 'PROD-002',
                'nombre' => 'Concentrado Gato Adulto 3kg',
                'descripcion' => 'Alimento para gatos con control de bolas de pelo',
                'precio_venta' => 45000,
                'precio_compra' => 30000,
                'stock' => 20,
                'iva_porcentaje' => 19,
                'categoria_id' => $catAlimentos->id ?? null,
                'tipo' => 'producto',
            ],
            
            // --- PRODUCTOS (Medicamentos) ---
            [
                'codigo' => 'PROD-003',
                'nombre' => 'Desparasitante Bravecto 20-40kg',
                'descripcion' => 'Tableta masticable contra pulgas y garrapatas',
                'precio_venta' => 65000,
                'precio_compra' => 45000,
                'stock' => 10,
                'iva_porcentaje' => 19,
                'categoria_id' => $catMedicamentos->id ?? null,
                'tipo' => 'producto',
            ],
            [
                'codigo' => 'PROD-004',
                'nombre' => 'Vacuna Antirrábica',
                'descripcion' => 'Dosis única para perros y gatos',
                'precio_venta' => 25000,
                'precio_compra' => 15000,
                'stock' => 50,
                'iva_porcentaje' => 19,
                'categoria_id' => $catMedicamentos->id ?? null,
                'tipo' => 'producto',
            ],

            // --- PRODUCTOS (Accesorios e Higiene) ---
            [
                'codigo' => 'PROD-005',
                'nombre' => 'Juguete Kong Clásico Mediano',
                'descripcion' => 'Juguete de caucho resistente para perros',
                'precio_venta' => 35000,
                'precio_compra' => 20000,
                'stock' => 12,
                'iva_porcentaje' => 19,
                'categoria_id' => $catAccesorios->id ?? null,
                'tipo' => 'producto',
            ],
            [
                'codigo' => 'PROD-006',
                'nombre' => 'Shampoo Antipulgas 500ml',
                'descripcion' => 'Shampoo medicado para baño canino',
                'precio_venta' => 28000,
                'precio_compra' => 15000,
                'stock' => 25,
                'iva_porcentaje' => 19,
                'categoria_id' => $catHigiene->id ?? null,
                'tipo' => 'producto',
            ],

            // --- SERVICIOS ---
            [
                'codigo' => 'SERV-001',
                'nombre' => 'Consulta Veterinaria General',
                'descripcion' => 'Revisión completa de la mascota',
                'precio_venta' => 50000,
                'precio_compra' => 0,
                'stock' => 999, // Stock infinito para servicios
                'iva_porcentaje' => 0, // Los servicios médicos suelen ser exentos
                'categoria_id' => $catConsultas->id ?? null,
                'tipo' => 'servicio',
            ],
            [
                'codigo' => 'SERV-002',
                'nombre' => 'Esterilización Canina (Hembra)',
                'descripcion' => 'Procedimiento quirúrgico completo',
                'precio_venta' => 350000,
                'precio_compra' => 0,
                'stock' => 999,
                'iva_porcentaje' => 0,
                'categoria_id' => $catCirugias->id ?? null,
                'tipo' => 'servicio',
            ],
            [
                'codigo' => 'SERV-003',
                'nombre' => 'Baño y Corte de Raza Pequeña',
                'descripcion' => 'Estética completa para mascotas de talla pequeña',
                'precio_venta' => 45000,
                'precio_compra' => 0,
                'stock' => 999,
                'iva_porcentaje' => 19,
                'categoria_id' => $catEstetica->id ?? null,
                'tipo' => 'servicio',
            ],

            // --- PAQUETE ---
            [
                'codigo' => 'PAQ-001',
                'nombre' => 'Plan Cachorro (Vacunas + Consulta)',
                'descripcion' => 'Incluye 2 consultas, 2 vacunas y desparasitación',
                'precio_venta' => 150000,
                'precio_compra' => 80000,
                'stock' => 999,
                'iva_porcentaje' => 0,
                'categoria_id' => $catPaquetes->id ?? null,
                'tipo' => 'paquete',
            ],
        ];

        // Insertamos o actualizamos los productos
        foreach ($productos as $prod) {
            Producto::updateOrCreate(
                ['codigo' => $prod['codigo'], 'tenant_id' => 1], // Busca por código y tenant
                array_merge($prod, [
                    'tenant_id' => 1, // Asegúrate de que este sea el ID de tu tenant
                    'activo' => true,
                ])
            );
        }

        $this->command->info('✅ 10 Productos y Servicios creados exitosamente.');
    }
}