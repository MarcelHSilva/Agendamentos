<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Tenant;
use Carbon\Carbon;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o primeiro tenant para desenvolvimento
        $defaultTenant = Tenant::first();
        
        if (!$defaultTenant) {
            return;
        }

        $clients = [
            [
                'tenant_id' => $defaultTenant->id,
                'name' => 'Ana Silva',
                'email' => 'ana.silva@email.com',
                'phone' => '(11) 99999-9999',
                'whatsapp' => '5511999999999',
                'birth_date' => Carbon::parse('1985-03-15'),
                'gender' => 'female',
                'address' => 'Rua das Flores, 123 - Vila Madalena, São Paulo - SP',
                'notes' => 'Cliente preferencial, gosta de cortes modernos',
                'is_active' => true,
                'last_appointment_at' => Carbon::now()->subDays(7),
                'total_appointments' => 15,
                'total_spent' => 675.00,
                'preferences' => json_encode([
                    'preferred_time' => 'morning',
                    'allergies' => [],
                    'notes' => 'Prefere produtos naturais'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $defaultTenant->id,
                'name' => 'Maria Santos',
                'email' => 'maria.santos@email.com',
                'phone' => '(11) 88888-8888',
                'whatsapp' => '5511888888888',
                'birth_date' => Carbon::parse('1990-07-22'),
                'gender' => 'female',
                'address' => 'Av. Paulista, 456 - Bela Vista, São Paulo - SP',
                'notes' => 'Cliente fiel, sempre pontual',
                'is_active' => true,
                'last_appointment_at' => Carbon::now()->subDays(3),
                'total_appointments' => 22,
                'total_spent' => 980.00,
                'preferences' => json_encode([
                    'preferred_time' => 'afternoon',
                    'allergies' => ['ammonia'],
                    'notes' => 'Sensível a produtos químicos'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $defaultTenant->id,
                'name' => 'João Costa',
                'email' => 'joao.costa@email.com',
                'phone' => '(11) 77777-7777',
                'whatsapp' => '5511777777777',
                'birth_date' => Carbon::parse('1982-11-08'),
                'gender' => 'male',
                'address' => 'Rua Augusta, 789 - Consolação, São Paulo - SP',
                'notes' => 'Cliente executivo, agenda flexível',
                'is_active' => true,
                'last_appointment_at' => Carbon::now()->subDays(14),
                'total_appointments' => 8,
                'total_spent' => 320.00,
                'preferences' => json_encode([
                    'preferred_time' => 'evening',
                    'allergies' => [],
                    'notes' => 'Prefere atendimento rápido'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $defaultTenant->id,
                'name' => 'Carla Oliveira',
                'email' => 'carla.oliveira@email.com',
                'phone' => '(11) 66666-6666',
                'whatsapp' => '5511666666666',
                'birth_date' => Carbon::parse('1992-05-18'),
                'gender' => 'female',
                'address' => 'Rua Oscar Freire, 321 - Jardins, São Paulo - SP',
                'notes' => 'Nova cliente, muito simpática',
                'is_active' => true,
                'last_appointment_at' => null,
                'total_appointments' => 0,
                'total_spent' => 0.00,
                'preferences' => json_encode([
                    'preferred_time' => 'morning',
                    'allergies' => [],
                    'notes' => 'Primeira vez no salão'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}