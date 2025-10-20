<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Básico',
                'slug' => 'basico',
                'description' => 'Plano ideal para profissionais autônomos que estão começando',
                'price_monthly' => 29.90,
                'price_yearly' => 299.00, // 2 meses grátis
                'max_staff' => 1,
                'max_clients' => 100,
                'max_appointments_per_month' => 50,
                'whatsapp_integration' => true,
                'email_notifications' => false,
                'online_booking' => false,
                'custom_branding' => false,
                'analytics_reports' => false,
                'api_access' => false,
                'features' => json_encode([
                    'Agenda digital',
                    'Cadastro de clientes',
                    'Notificações WhatsApp',
                    'Suporte por email'
                ]),
                'trial_days' => 14,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Profissional',
                'slug' => 'profissional',
                'description' => 'Para salões e clínicas com equipe pequena',
                'price_monthly' => 59.90,
                'price_yearly' => 599.00, // 2 meses grátis
                'max_staff' => 5,
                'max_clients' => 500,
                'max_appointments_per_month' => 200,
                'whatsapp_integration' => true,
                'email_notifications' => true,
                'online_booking' => true,
                'custom_branding' => false,
                'analytics_reports' => true,
                'api_access' => false,
                'features' => json_encode([
                    'Tudo do plano Básico',
                    'Até 5 profissionais',
                    'Agendamento online',
                    'Notificações por email',
                    'Relatórios básicos',
                    'Suporte prioritário'
                ]),
                'trial_days' => 14,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Para empresas que precisam de recursos avançados',
                'price_monthly' => 99.90,
                'price_yearly' => 999.00, // 2 meses grátis
                'max_staff' => 20,
                'max_clients' => 2000,
                'max_appointments_per_month' => 1000,
                'whatsapp_integration' => true,
                'email_notifications' => true,
                'online_booking' => true,
                'custom_branding' => true,
                'analytics_reports' => true,
                'api_access' => true,
                'features' => json_encode([
                    'Tudo do plano Profissional',
                    'Até 20 profissionais',
                    'Marca personalizada',
                    'API para integrações',
                    'Relatórios avançados',
                    'Suporte telefônico',
                    'Gerente de conta dedicado'
                ]),
                'trial_days' => 14,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('plans')->updateOrInsert(
                ['slug' => $plan['slug']],
                array_merge($plan, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
