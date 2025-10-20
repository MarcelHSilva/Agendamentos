<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FinancialController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test-dashboard', function () {
    return Inertia::render('TestDashboard');
})->name('test.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Clientes
    Route::get('/clients', function () {
        return Inertia::render('Clients/Index');
    })->name('clients.index');
    
    // Serviços
    Route::get('/services', function () {
        return Inertia::render('Services/Index');
    })->name('services.index');
    
    // Agendamentos
    Route::get('/appointments', function () {
        return Inertia::render('Appointments/Index');
    })->name('appointments.index');
    
    // Categorias
    Route::get('/categories', function () {
        return Inertia::render('Categories/Index');
    })->name('categories.index');
    

    
    // Financeiro
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    
    // Notificações
    Route::get('/notifications', function () {
        return Inertia::render('Notifications/Index');
    })->name('notifications.index');
    
    // Programa de Fidelidade
    Route::get('/loyalty', function () {
        return Inertia::render('Loyalty/Index');
    })->name('loyalty.index');
});

// Rotas da API Financeira (fora do middleware auth para permitir acesso)
Route::middleware(['web'])->group(function () {
    Route::get('/api/transactions', [FinancialController::class, 'getTransactions'])->name('api.transactions');
    Route::get('/api/financial/summary', [FinancialController::class, 'getSummary'])->name('api.financial.summary');
    Route::get('/api/financial/chart-data', [FinancialController::class, 'getChartData'])->name('api.financial.chart-data');
    Route::post('/api/transactions', [FinancialController::class, 'store'])->name('api.transactions.store');
    Route::put('/api/transactions/{transaction}', [FinancialController::class, 'update'])->name('api.transactions.update');
    Route::delete('/api/transactions/{transaction}', [FinancialController::class, 'destroy'])->name('api.transactions.destroy');
    
    // Rotas da API Dashboard
    Route::get('/api/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'getStats'])->name('api.dashboard.stats');
    Route::get('/api/dashboard/recent-appointments', [\App\Http\Controllers\DashboardController::class, 'getRecentAppointments'])->name('api.dashboard.recent-appointments');
});

require __DIR__.'/auth.php';
