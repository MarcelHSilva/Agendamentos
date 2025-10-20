<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const stats = ref({
    totalClients: 156,
    todayAppointments: 3,
    monthlyRevenue: 12450.00,
    activeServices: 12
});
</script>

<template>
    <Head title="Test Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Test Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Cards de Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Card Total de Clientes -->
                    <Link :href="route('clients.index')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer group border-2 border-blue-500">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 group-hover:scale-110 transition-transform">👥</div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ stats.totalClients }}</div>
                                <div class="text-sm text-gray-600">Total de Clientes (CLICÁVEL)</div>
                            </div>
                        </div>
                    </Link>

                    <!-- Card Agendamentos Hoje -->
                    <Link :href="route('appointments.index')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer group border-2 border-green-500">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 group-hover:scale-110 transition-transform">📅</div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ stats.todayAppointments }}</div>
                                <div class="text-sm text-gray-600">Agendamentos Hoje (CLICÁVEL)</div>
                            </div>
                        </div>
                    </Link>

                    <!-- Card Receita Mensal -->
                    <Link :href="route('financial.index')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer group border-2 border-yellow-500">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 group-hover:scale-110 transition-transform">💰</div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900 group-hover:text-green-600 transition-colors">R$ {{ stats.monthlyRevenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}</div>
                                <div class="text-sm text-gray-600">Receita Mensal (CLICÁVEL)</div>
                            </div>
                        </div>
                    </Link>

                    <!-- Card Serviços Ativos -->
                    <Link :href="route('services.index')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer group border-2 border-red-500">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 group-hover:scale-110 transition-transform">✂️</div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900 group-hover:text-purple-600 transition-colors">{{ stats.activeServices }}</div>
                                <div class="text-sm text-gray-600">Serviços Ativos (CLICÁVEL)</div>
                            </div>
                        </div>
                    </Link>
                </div>

                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Teste de Links</h3>
                    <div class="space-y-2">
                        <Link :href="route('clients.index')" class="block text-blue-600 hover:text-blue-800">→ Ir para Clientes</Link>
                        <Link :href="route('appointments.index')" class="block text-blue-600 hover:text-blue-800">→ Ir para Agendamentos</Link>
                        <Link :href="route('services.index')" class="block text-blue-600 hover:text-blue-800">→ Ir para Serviços</Link>
                        <Link :href="route('financial.index')" class="block text-blue-600 hover:text-blue-800">→ Ir para Financeiro</Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>