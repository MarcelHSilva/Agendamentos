<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Tenant;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->createServicesForTenant($tenant);
        }
    }

    private function createServicesForTenant(Tenant $tenant): void
    {
        $businessType = $tenant->settings['business_type'] ?? 'salon';

        switch ($businessType) {
            case 'salon':
                $this->createSalonServices($tenant);
                break;
            case 'clinic':
                $this->createClinicServices($tenant);
                break;
            case 'barbershop':
                $this->createBarbershopServices($tenant);
                break;
            default:
                $this->createSalonServices($tenant);
        }
    }

    private function createSalonServices(Tenant $tenant): void
    {
        $services = [
            [
                'name' => 'Corte Feminino',
                'description' => 'Corte de cabelo feminino com lavagem e finalização',
                'duration' => 60,
                'price' => 45.00,
                'color' => '#E91E63'
            ],
            [
                'name' => 'Escova Progressiva',
                'description' => 'Tratamento alisante com escova progressiva',
                'duration' => 180,
                'price' => 120.00,
                'color' => '#9C27B0'
            ],
            [
                'name' => 'Manicure',
                'description' => 'Cuidados com as unhas das mãos',
                'duration' => 45,
                'price' => 25.00,
                'color' => '#FF5722'
            ],
            [
                'name' => 'Pedicure',
                'description' => 'Cuidados com as unhas dos pés',
                'duration' => 60,
                'price' => 30.00,
                'color' => '#FF9800'
            ],
            [
                'name' => 'Coloração',
                'description' => 'Coloração completa do cabelo',
                'duration' => 120,
                'price' => 80.00,
                'color' => '#795548'
            ]
        ];

        $this->createServices($tenant, $services);
    }

    private function createClinicServices(Tenant $tenant): void
    {
        $services = [
            [
                'name' => 'Consulta Dermatológica',
                'description' => 'Consulta médica especializada em dermatologia',
                'duration' => 30,
                'price' => 150.00,
                'color' => '#2196F3'
            ],
            [
                'name' => 'Limpeza de Pele',
                'description' => 'Procedimento de limpeza facial profunda',
                'duration' => 90,
                'price' => 80.00,
                'color' => '#4CAF50'
            ],
            [
                'name' => 'Peeling Químico',
                'description' => 'Tratamento de renovação celular',
                'duration' => 60,
                'price' => 200.00,
                'color' => '#FF9800'
            ],
            [
                'name' => 'Botox',
                'description' => 'Aplicação de toxina botulínica',
                'duration' => 45,
                'price' => 350.00,
                'color' => '#9C27B0'
            ]
        ];

        $this->createServices($tenant, $services);
    }

    private function createBarbershopServices(Tenant $tenant): void
    {
        $services = [
            [
                'name' => 'Corte Masculino',
                'description' => 'Corte de cabelo masculino tradicional',
                'duration' => 30,
                'price' => 25.00,
                'color' => '#795548'
            ],
            [
                'name' => 'Barba',
                'description' => 'Aparar e modelar a barba',
                'duration' => 20,
                'price' => 15.00,
                'color' => '#607D8B'
            ],
            [
                'name' => 'Corte + Barba',
                'description' => 'Pacote completo de corte e barba',
                'duration' => 45,
                'price' => 35.00,
                'color' => '#3F51B5'
            ],
            [
                'name' => 'Relaxamento',
                'description' => 'Tratamento capilar relaxante',
                'duration' => 60,
                'price' => 40.00,
                'color' => '#009688'
            ]
        ];

        $this->createServices($tenant, $services);
    }

    private function createServices(Tenant $tenant, array $services): void
    {
        foreach ($services as $serviceData) {
            Service::create([
                'tenant_id' => $tenant->id,
                'name' => $serviceData['name'],
                'description' => $serviceData['description'],
                'duration' => $serviceData['duration'],
                'price' => $serviceData['price'],
                'color' => $serviceData['color'],
                'is_active' => true,
                'online_booking_enabled' => true,
                'buffer_time_before' => 5,
                'buffer_time_after' => 5,
                'advance_booking_min' => 60,
                'advance_booking_max' => 10080,
            ]);
        }
    }
}
