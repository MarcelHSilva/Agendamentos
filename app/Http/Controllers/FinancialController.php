<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FinancialController extends Controller
{
    /**
     * Exibir página principal do financeiro
     */
    public function index()
    {
        return Inertia::render('Financial/Index');
    }

    /**
     * Listar transações com filtros
     */
    public function getTransactions(Request $request)
    {
        $query = Transaction::query();

        // Em desenvolvimento, se não há tenant detectado, usar o primeiro tenant
        if (!app()->bound('tenant') && app()->environment('local')) {
            $defaultTenant = \App\Models\Tenant::first();
            if ($defaultTenant) {
                $query = $query->withoutTenantIsolation()->where('tenant_id', $defaultTenant->id);
            }
        }

        // Filtrar por período se especificado
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        // Filtrar por tipo se especificado
        if ($request->has('type') && $request->type !== 'all') {
            $query->ofType($request->type);
        }

        // Filtrar por categoria se especificado
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Buscar por descrição
        if ($request->has('search') && $request->search) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->with('client')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($transactions);
    }

    /**
     * Obter resumo financeiro
     */
    public function getSummary(Request $request)
    {
        $query = Transaction::query();

        // Em desenvolvimento, se não há tenant detectado, usar o primeiro tenant
        if (!app()->bound('tenant') && app()->environment('local')) {
            $defaultTenant = \App\Models\Tenant::first();
            if ($defaultTenant) {
                $query = $query->withoutTenantIsolation()->where('tenant_id', $defaultTenant->id);
            }
        }

        // Aplicar filtros de data se especificados
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        $revenue = (clone $query)->ofType('revenue')->sum('amount');
        $expenses = (clone $query)->ofType('expense')->sum('amount');
        $profit = $revenue - $expenses;

        return response()->json([
            'revenue' => $revenue, // Valores já estão em reais
            'expenses' => $expenses, // Valores já estão em reais
            'profit' => $profit // Valores já estão em reais
        ]);
    }

    /**
     * Criar nova transação
     */
    public function store(Request $request)
    {
        Log::info('🚀 CONTROLLER: Método store chamado');
        Log::info('📥 CONTROLLER: Dados recebidos:', $request->all());
        Log::info('📋 CONTROLLER: Headers:', $request->headers->all());

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:revenue,expense',
            'client_id' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);

        Log::info('✅ CONTROLLER: Validação passou:', $validated);

        // Em desenvolvimento, se não há tenant detectado, usar o primeiro tenant
        if (!app()->bound('tenant') && app()->environment('local')) {
            $defaultTenant = \App\Models\Tenant::first();
            if ($defaultTenant && !isset($validated['tenant_id'])) {
                $validated['tenant_id'] = $defaultTenant->id;
            }
        }

        // Garantir que o valor seja salvo em reais (não em centavos)
        // O campo amount no banco é decimal(10,2) e deve armazenar valores em reais
        $validated['amount'] = floatval($validated['amount']);

        Log::info('💾 CONTROLLER: Criando transação com dados:', $validated);

        $transaction = Transaction::create($validated);

        Log::info('✅ CONTROLLER: Transação criada com sucesso:', $transaction->toArray());

        return response()->json($transaction, 201);
    }

    /**
     * Atualizar transação existente
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:revenue,expense',
            'client_id' => 'nullable|integer',
            'notes' => 'nullable|string'
        ]);

        // Garantir que o valor seja salvo em reais (não em centavos)
        $validated['amount'] = floatval($validated['amount']);

        $transaction->update($validated);

        return response()->json($transaction);
    }

    /**
     * Excluir transação
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return response()->json(['message' => 'Transação excluída com sucesso']);
    }

    /**
     * Obter dados para gráficos
     */
    public function getChartData(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonths(11)->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $monthlyData = [];
        $current = Carbon::parse($startDate)->startOfMonth();
        $end = Carbon::parse($endDate)->endOfMonth();

        while ($current <= $end) {
            $monthKey = $current->format('Y-m');

            $revenueQuery = Transaction::ofType('revenue')
                ->whereYear('date', $current->year)
                ->whereMonth('date', $current->month);

            $expensesQuery = Transaction::ofType('expense')
                ->whereYear('date', $current->year)
                ->whereMonth('date', $current->month);

            // Em desenvolvimento, se não há tenant detectado, usar o primeiro tenant
            if (!app()->bound('tenant') && app()->environment('local')) {
                $defaultTenant = \App\Models\Tenant::first();
                if ($defaultTenant) {
                    $revenueQuery = $revenueQuery->withoutTenantIsolation()->where('tenant_id', $defaultTenant->id);
                    $expensesQuery = $expensesQuery->withoutTenantIsolation()->where('tenant_id', $defaultTenant->id);
                }
            }

            $revenue = $revenueQuery->sum('amount');
            $expenses = $expensesQuery->sum('amount');

            $monthlyData[$monthKey] = [
                'revenue' => $revenue, // Valores já estão em reais
                'expenses' => $expenses, // Valores já estão em reais
                'profit' => ($revenue - $expenses) // Valores já estão em reais
            ];

            $current->addMonth();
        }

        return response()->json($monthlyData);
    }
}
