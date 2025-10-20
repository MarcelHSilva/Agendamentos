<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'Salão Bella Vita',
                'subdomain' => 'bellavita',
                'timezone' => 'America/Sao_Paulo',
                'whatsapp' => '+5511999887766',
                'email' => 'contato@bellavita.com.br',
                'address' => 'Rua das Flores, 123 - Vila Madalena, São Paulo - SP',
                'phone' => '+5511999887766',
                'website' => 'https://bellavita.com.br',
                'status' => 'active',
                'plan' => 'profissional',
                'limits' => json_encode([
                    'max_staff' => 5,
                    'max_clients' => 500,
                    'max_appointments_per_month' => 200
                ]),
                'settings' => json_encode([
                    'business_type' => 'salon',
                    'currency' => 'BRL',
                    'date_format' => 'd/m/Y',
                    'time_format' => 'H:i',
                    'week_start' => 'monday',
                    'booking_advance_days' => 30,
                    'cancellation_hours' => 24,
                    'primary_color' => '#e91e63',
                    'logo_url' => null
                ]),
                'trial_ends_at' => now()->addDays(14),
                'subscription_ends_at' => now()->addMonths(1),
            ],
            [
                'name' => 'Clínica Dr. Silva',
                'subdomain' => 'drsilva',
                'timezone' => 'America/Sao_Paulo',
                'whatsapp' => '+5511888776655',
                'email' => 'contato@clinicadrsilva.com.br',
                'address' => 'Av. Paulista, 1000 - Bela Vista, São Paulo - SP',
                'phone' => '+5511888776655',
                'website' => 'https://clinicadrsilva.com.br',
                'status' => 'active',
                'plan' => 'premium',
                'limits' => json_encode([
                    'max_staff' => 20,
                    'max_clients' => 2000,
                    'max_appointments_per_month' => 1000
                ]),
                'settings' => json_encode([
                    'business_type' => 'clinic',
                    'currency' => 'BRL',
                    'date_format' => 'd/m/Y',
                    'time_format' => 'H:i',
                    'week_start' => 'monday',
                    'booking_advance_days' => 60,
                    'cancellation_hours' => 48,
                    'primary_color' => '#2196f3',
                    'logo_url' => null
                ]),
                'trial_ends_at' => null,
                'subscription_ends_at' => now()->addYear(),
            ],
            [
                'name' => 'Barbearia Moderna',
                'subdomain' => 'modernabarber',
                'timezone' => 'America/Sao_Paulo',
                'whatsapp' => '+5511777665544',
                'email' => 'contato@modernabarber.com.br',
                'address' => 'Rua Augusta, 500 - Consolação, São Paulo - SP',
                'phone' => '+5511777665544',
                'website' => 'https://modernabarber.com.br',
                'status' => 'active',
                'plan' => 'basico',
                'limits' => json_encode([
                    'max_staff' => 1,
                    'max_clients' => 100,
                    'max_appointments_per_month' => 50
                ]),
                'settings' => json_encode([
                    'business_type' => 'barbershop',
                    'currency' => 'BRL',
                    'date_format' => 'd/m/Y',
                    'time_format' => 'H:i',
                    'week_start' => 'monday',
                    'booking_advance_days' => 15,
                    'cancellation_hours' => 12,
                    'primary_color' => '#795548',
                    'logo_url' => null
                ]),
                'trial_ends_at' => now()->addDays(7),
                'subscription_ends_at' => null,
            ],
        ];

        foreach ($tenants as $tenant) {
            DB::table('tenants')->updateOrInsert(
                ['subdomain' => $tenant['subdomain']],
                array_merge($tenant, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
