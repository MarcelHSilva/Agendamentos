<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Obter estatísticas do dashboard
     */
    public function getStats(): JsonResponse
    {
        try {
            // Em desenvolvimento, se não há tenant detectado, usar o primeiro tenant
            $tenantId = null;
            if (!app()->bound('tenant') && app()->environment('local')) {
                $defaultTenant = \App\Models\Tenant::first();
                if ($defaultTenant) {
                    $tenantId = $defaultTenant->id;
                }
            }

            // Total de clientes
            $clientsQuery = Client::query();
            if ($tenantId) {
                $clientsQuery = $clientsQuery->where('tenant_id', $tenantId);
            }
            $totalClients = $clientsQuery->count();

            // Agendamentos de hoje
            $appointmentsQuery = Appointment::query()->whereDate('start_time', Carbon::today());
            if ($tenantId) {
                $appointmentsQuery = $appointmentsQuery->where('tenant_id', $tenantId);
            }
            $todayAppointments = $appointmentsQuery->count();

            // Receita mensal (mês atual)
            $transactionsQuery = Transaction::query()
                ->where('type', 'revenue')
                ->whereYear('date', Carbon::now()->year)
                ->whereMonth('date', Carbon::now()->month);
            if ($tenantId) {
                $transactionsQuery = $transactionsQuery->where('tenant_id', $tenantId);
            }
            $monthlyRevenue = $transactionsQuery->sum('amount');

            // Serviços ativos
            $servicesQuery = Service::query()->where('is_active', true);
            if ($tenantId) {
                $servicesQuery = $servicesQuery->where('tenant_id', $tenantId);
            }
            $activeServices = $servicesQuery->count();

            return response()->json([
                'totalClients' => $totalClients,
                'todayAppointments' => $todayAppointments,
                'monthlyRevenue' => $monthlyRevenue,
                'activeServices' => $activeServices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar estatísticas',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter agendamentos recentes
     */
    public function getRecentAppointments(): JsonResponse
    {
        try {
            $tenantId = null;
            if (!app()->bound('tenant') && app()->environment('local')) {
                $defaultTenant = \App\Models\Tenant::first();
                if ($defaultTenant) {
                    $tenantId = $defaultTenant->id;
                }
            }

            $appointmentsQuery = Appointment::query()
                ->with(['client', 'service'])
                ->whereDate('start_time', Carbon::today())
                ->orderBy('start_time', 'asc')
                ->limit(5);
            
            if ($tenantId) {
                $appointmentsQuery = $appointmentsQuery->where('tenant_id', $tenantId);
            }

            $appointments = $appointmentsQuery->get()->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'client' => $appointment->client->name ?? 'Cliente não encontrado',
                    'service' => $appointment->service->name ?? 'Serviço não encontrado',
                    'time' => $appointment->start_time->format('H:i'),
                    'status' => $appointment->status
                ];
            });

            return response()->json($appointments);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao carregar agendamentos',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}