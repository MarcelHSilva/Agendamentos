<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tenant IDs
        $bellavitaTenant = DB::table('tenants')->where('subdomain', 'bellavita')->first();
        $drSilvaTenant = DB::table('tenants')->where('subdomain', 'drsilva')->first();
        $modernaBarberTenant = DB::table('tenants')->where('subdomain', 'modernabarber')->first();

        $users = [
            [
                'tenant_id' => $bellavitaTenant->id,
                'name' => 'Maria Silva',
                'email' => 'maria@bellavita.com.br',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'google_id' => null,
                'role' => 'owner',
                'is_active' => true,
                'permissions' => json_encode([
                    'manage_staff',
                    'manage_clients',
                    'manage_appointments',
                    'manage_services',
                    'view_reports',
                    'manage_settings'
                ]),
                'last_login_at' => now()->subDays(1),
            ],
            [
                'tenant_id' => $drSilvaTenant->id,
                'name' => 'Dr. João Silva',
                'email' => 'joao@clinicadrsilva.com.br',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'google_id' => null,
                'role' => 'owner',
                'is_active' => true,
                'permissions' => json_encode([
                    'manage_staff',
                    'manage_clients',
                    'manage_appointments',
                    'manage_services',
                    'view_reports',
                    'manage_settings'
                ]),
                'last_login_at' => now()->subHours(2),
            ],
            [
                'tenant_id' => $drSilvaTenant->id,
                'name' => 'Dra. Ana Costa',
                'email' => 'ana@clinicadrsilva.com.br',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'google_id' => null,
                'role' => 'staff',
                'is_active' => true,
                'permissions' => json_encode([
                    'manage_appointments',
                    'view_clients',
                    'manage_own_schedule'
                ]),
                'last_login_at' => now()->subDays(3),
            ],
            [
                'tenant_id' => $modernaBarberTenant->id,
                'name' => 'Carlos Barbeiro',
                'email' => 'carlos@modernabarber.com.br',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'google_id' => null,
                'role' => 'owner',
                'is_active' => true,
                'permissions' => json_encode([
                    'manage_staff',
                    'manage_clients',
                    'manage_appointments',
                    'manage_services',
                    'view_reports',
                    'manage_settings'
                ]),
                'last_login_at' => now()->subMinutes(30),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email'], 'tenant_id' => $user['tenant_id']],
                array_merge($user, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
