<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Veterinario;
use App\Models\User; // Por si quieres vincularlos a un usuario del sistema

class VeterinarioSeeder extends Seeder
{
    public function run()
    {
        // Lista de los 5 veterinarios a crear
        $veterinarios = [
            [
                'cedula'       => '1020304050',
                'nombre'       => 'Carlos Andrés',
                'apellidos'    => 'Gómez Restrepo',
                'celular'      => '3101112233',
                'email'        => 'carlos.gomez@veterinaria.com',
                'especialidad' => 'Medicina Interna',
            ],
            [
                'cedula'       => '1020304051',
                'nombre'       => 'María Fernanda',
                'apellidos'    => 'López Martínez',
                'celular'      => '3202223344',
                'email'        => 'maria.lopez@veterinaria.com',
                'especialidad' => 'Cirugía y Ortopedia',
            ],
            [
                'cedula'       => '1020304052',
                'nombre'       => 'Javier',
                'apellidos'    => 'Rodríguez Silva',
                'celular'      => '3153334455',
                'email'        => 'javier.rodriguez@veterinaria.com',
                'especialidad' => 'Dermatología',
            ],
            [
                'cedula'       => '1020304053',
                'nombre'       => 'Lorena',
                'apellidos'    => 'Pérez Castro',
                'celular'      => '3114445566',
                'email'        => 'lorena.perez@veterinaria.com',
                'especialidad' => 'Oftalmología',
            ],
            [
                'cedula'       => '1020304054',
                'nombre'       => 'Andrés Felipe',
                'apellidos'    => 'Ramírez Ospina',
                'celular'      => '3185556677',
                'email'        => 'andres.ramirez@veterinaria.com',
                'especialidad' => 'Odontología',
            ],
        ];

        foreach ($veterinarios as $vet) {
            // updateOrCreate busca por cédula y tenant_id para evitar duplicados si se corre dos veces
            Veterinario::updateOrCreate(
                [
                    'cedula' => $vet['cedula'],
                    'tenant_id' => 1, // 🔥 Ajusta este ID al tenant de tu clínica
                ],
                array_merge($vet, [
                    'tenant_id' => 1,
                    'activo'    => true,
                ])
            );
        }

        $this->command->info('✅ 5 Veterinarios creados exitosamente.');
    }
}