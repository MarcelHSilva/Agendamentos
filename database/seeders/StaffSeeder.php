<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\User;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();
        $users = User::all();

        foreach ($tenants as $tenant) {
            $this->createStaffForTenant($tenant, $users);
        }
    }

    private function createStaffForTenant(Tenant $tenant, $users): void
    {
        // Criar staff baseado no tipo de negócio
        $businessType = $tenant->settings['business_type'] ?? 'salon';
        
        switch ($businessType) {
            case 'salon':
                $this->createSalonStaff($tenant, $users);
                break;
            case 'clinic':
                $this->createClinicStaff($tenant, $users);
                break;
            case 'barbershop':
                $this->createBarbershopStaff($tenant, $users);
                break;
            default:
                $this->createSalonStaff($tenant, $users);
        }
    }

    private function createSalonStaff(Tenant $tenant, $users): void
    {
        $staffData = [
            [
                'name' => 'Maria Silva',
                'email' => 'maria@' . $tenant->subdomain . '.com',
                'phone' => '(11) 99999-1111',
                'bio' => 'Especialista em cortes femininos e coloração com 8 anos de experiência.',
                'user_id' => $users->first()->id ?? null,
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana@' . $tenant->subdomain . '.com',
                'phone' => '(11) 99999-2222',
                'bio' => 'Expert em manicure e pedicure, atendimento personalizado.',
                'user_id' => null,
            ]
        ];

        $this->createStaff($tenant, $staffData);
    }

    private function createClinicStaff(Tenant $tenant, $users): void
    {
        $staffData = [
            [
                'name' => 'Dr. João Silva',
                'email' => 'joao@' . $tenant->subdomain . '.com',
                'phone' => '(11) 99999-3333',
                'bio' => 'Dermatologista com especialização em procedimentos estéticos.',
                'user_id' => $users->skip(1)->first()->id ?? null,
            ],
            [
                'name' => 'Dra. Ana Costa',
                'email' => 'ana@' . $tenant->subdomain . '.com',
                'phone' => '(11) 99999-4444',
                'bio' => 'Especialista em tratamentos faciais e rejuvenescimento.',
                'user_id' => $users->skip(2)->first()->id ?? null,
            ]
        ];

        $this->createStaff($tenant, $staffData);
    }

    private function createBarbershopStaff(Tenant $tenant, $users): void
    {
        $staffData = [
            [
                'name' => 'Carlos Barbeiro',
                'email' => 'carlos@' . $tenant->subdomain . '.com',
                'phone' => '(11) 99999-5555',
                'bio' => 'Barbeiro tradicional com técnicas modernas, especialista em barba.',
                'user_id' => $users->last()->id ?? null,
            ],
            [
                'name' => 'Roberto Silva',
                'email' => 'roberto@' . $tenant->subdomain . '.com',
                'phone' => '(11) 99999-6666',
                'bio' => 'Especialista em cortes masculinos e relaxamento capilar.',
                'user_id' => null,
            ]
        ];

        $this->createStaff($tenant, $staffData);
    }

    private function createStaff(Tenant $tenant, array $staffData): void
    {
        foreach ($staffData as $data) {
            DB::table('staff')->insert([
                'tenant_id' => $tenant->id,
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'bio' => $data['bio'],
                'is_active' => true,
                'accepts_online_bookings' => true,
                'working_hours' => json_encode([
                    'monday' => ['start' => '09:00', 'end' => '18:00'],
                    'tuesday' => ['start' => '09:00', 'end' => '18:00'],
                    'wednesday' => ['start' => '09:00', 'end' => '18:00'],
                    'thursday' => ['start' => '09:00', 'end' => '18:00'],
                    'friday' => ['start' => '09:00', 'end' => '18:00'],
                    'saturday' => ['start' => '09:00', 'end' => '16:00'],
                    'sunday' => null
                ]),
                'break_times' => json_encode([
                    ['start' => '12:00', 'end' => '13:00']
                ]),
                'commission_rate' => rand(10, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
