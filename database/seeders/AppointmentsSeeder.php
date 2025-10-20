<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->createAppointmentsForTenant($tenant);
        }
    }

    private function createAppointmentsForTenant(Tenant $tenant): void
    {
        $clients = Client::where('tenant_id', $tenant->id)->get();
        $services = Service::where('tenant_id', $tenant->id)->get();
        $staff = DB::table('staff')->where('tenant_id', $tenant->id)->get();

        if ($clients->isEmpty() || $services->isEmpty() || $staff->isEmpty()) {
            return;
        }

        // Criar agendamentos para os últimos 30 dias
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Pular fins de semana
            if ($date->isWeekend()) {
                continue;
            }

            $appointmentsCount = rand(2, 8);
            
            for ($j = 0; $j < $appointmentsCount; $j++) {
                $client = $clients->random();
                $service = $services->random();
                $staffMember = $staff->random();
                
                $startHour = rand(8, 17);
                $startMinute = [0, 15, 30, 45][rand(0, 3)];
                
                $startTime = $date->copy()->setTime($startHour, $startMinute);
                $endTime = $startTime->copy()->addMinutes($service->duration);
                
                // Status baseado na data
                $status = 'confirmed';
                if ($date->isFuture()) {
                    $status = ['confirmed', 'pending'][rand(0, 1)];
                } elseif ($date->isPast()) {
                    $status = ['completed', 'cancelled'][rand(0, 1)];
                    // 80% de chance de ser completed
                    if (rand(1, 10) <= 8) {
                        $status = 'completed';
                    }
                }
                
                Appointment::create([
                    'tenant_id' => $tenant->id,
                    'client_id' => $client->id,
                    'staff_id' => $staffMember->id,
                    'service_id' => $service->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => $status,
                    'notes' => $this->getRandomNotes(),
                    'price' => $service->price,
                    'created_at' => $startTime->copy()->subDays(rand(1, 7)),
                    'updated_at' => $startTime->copy()->subDays(rand(0, 3)),
                ]);
            }
        }
        
        // Criar alguns agendamentos para hoje
        $today = Carbon::today();
        $todayAppointments = rand(1, 4);
        
        for ($i = 0; $i < $todayAppointments; $i++) {
            $client = $clients->random();
            $service = $services->random();
            $staffMember = $staff->random();
            
            $startHour = rand(9, 16);
            $startMinute = [0, 15, 30, 45][rand(0, 3)];
            
            $startTime = $today->copy()->setTime($startHour, $startMinute);
            $endTime = $startTime->copy()->addMinutes($service->duration);
            
            Appointment::create([
                'tenant_id' => $tenant->id,
                'client_id' => $client->id,
                'staff_id' => $staffMember->id,
                'service_id' => $service->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'confirmed',
                'notes' => $this->getRandomNotes(),
                'price' => $service->price,
                'created_at' => $startTime->copy()->subDays(rand(1, 3)),
                'updated_at' => $startTime->copy()->subDays(rand(0, 1)),
            ]);
        }
    }

    private function getRandomNotes(): ?string
    {
        $notes = [
            null,
            'Cliente preferencial',
            'Primeira vez no salão',
            'Alergia a produtos com amônia',
            'Cliente pontual',
            'Gosta de conversar',
            'Prefere profissional específico',
            'Cabelo sensível',
            'Cliente VIP',
            'Retoque de cor'
        ];
        
        return $notes[rand(0, count($notes) - 1)];
    }
}
