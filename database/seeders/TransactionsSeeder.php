<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use Carbon\Carbon;

class TransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o primeiro tenant e clientes para desenvolvimento
        $defaultTenant = \App\Models\Tenant::first();
        $clients = \App\Models\Client::limit(4)->get();
        
        if (!$defaultTenant || $clients->count() === 0) {
            return;
        }

        $transactions = [
            [
                'tenant_id' => $defaultTenant->id,
                'description' => 'Corte de cabelo - Ana Silva',
                'amount' => 45.00,
                'category' => 'Serviços de Cabelo',
                'date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'type' => 'revenue',
                'client_id' => $clients->get(0)->id ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $defaultTenant->id,
                'description' => 'Manicure - Maria Santos',
                'amount' => 35.00,
                'category' => 'Serviços de Unha',
                'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'type' => 'revenue',
                'client_id' => $clients->get(1)->id ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tenant_id' => $defaultTenant->id,
                'description' => 'Produtos de limpeza',
                'amount' => 120.00,
                'category' => 'Materiais',
                'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'type' => 'expense',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'description' => 'Energia elétrica',
                'amount' => 280.00,
                'category' => 'Contas',
                'date' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'type' => 'expense',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'description' => 'Escova progressiva - Carla Lima',
                'amount' => 150.00,
                'category' => 'Serviços de Cabelo',
                'date' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'type' => 'revenue',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'description' => 'Pedicure - Joana Costa',
                'amount' => 40.00,
                'category' => 'Serviços de Unha',
                'date' => Carbon::now()->format('Y-m-d'),
                'type' => 'revenue',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'description' => 'Aluguel do salão',
                'amount' => 800.00,
                'category' => 'Contas',
                'date' => Carbon::now()->subDays(12)->format('Y-m-d'),
                'type' => 'expense',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'description' => 'Coloração - Patricia Oliveira',
                'amount' => 85.00,
                'category' => 'Serviços de Cabelo',
                'date' => Carbon::now()->format('Y-m-d'),
                'type' => 'revenue',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'description' => 'Hidratação - Fernanda Costa',
                'amount' => 60.00,
                'category' => 'Serviços de Cabelo',
                'date' => Carbon::now()->subDays(27)->format('Y-m-d'),
                'type' => 'revenue',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'description' => 'Água',
                'amount' => 85.00,
                'category' => 'Contas',
                'date' => Carbon::now()->subDays(32)->format('Y-m-d'),
                'type' => 'expense',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($transactions as $transaction) {
            Transaction::create($transaction);
        }
    }
}
