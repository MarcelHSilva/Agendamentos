<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

// Estados reativos para dados reais
const stats = ref({
    totalClients: 0,
    todayAppointments: 0,
    monthlyRevenue: 0,
    activeServices: 0
});

const recentAppointments = ref([]);
const loading = ref(true);

// Função para carregar estatísticas
const loadStats = async () => {
    try {
        const response = await axios.get('/api/dashboard/stats');
        stats.value = response.data;
    } catch (error) {
        console.error('Erro ao carregar estatísticas:', error);
        // Manter valores padrão em caso de erro
        stats.value = {
            totalClients: 0,
            todayAppointments: 0,
            monthlyRevenue: 0,
            activeServices: 0
        };
    }
};

// Função para carregar agendamentos recentes
const loadRecentAppointments = async () => {
    try {
        const response = await axios.get('/api/dashboard/recent-appointments');
        recentAppointments.value = response.data;
    } catch (error) {
        console.error('Erro ao carregar agendamentos:', error);
        recentAppointments.value = [];
    }
};

const quickActions = ref([
    { name: 'Novo Agendamento', icon: '📅', route: 'appointments.index' },
    { name: 'Cadastrar Cliente', icon: '👤', route: 'clients.index' },
    { name: 'Gerenciar Serviços', icon: '✂️', route: 'services.index' },
    { name: 'Gerenciar Categorias', icon: '🏷️', route: 'categories.index' },
    { name: 'Ver Agendamentos', icon: '📋', route: 'appointments.index' },
    { name: 'Financeiro', icon: '💰', route: 'financial.index' },
    { name: 'Notificações', icon: '🔔', route: 'notifications.index' },
    { name: 'Fidelidade', icon: '⭐', route: 'loyalty.index' }
]);

// Carregar dados ao montar o componente
onMounted(async () => {
    loading.value = true;
    await Promise.all([
        loadStats(),
        loadRecentAppointments()
    ]);
    loading.value = false;
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Dashboard
                </h2>
                <div class="text-sm text-gray-600">
                    Bem-vindo, {{ $page.props.auth.user.name }}!
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Cards de Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Card Total de Clientes -->
                    <Link :href="route('clients.index')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer group">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 group-hover:scale-110 transition-transform">👥</div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ stats.totalClients }}</div>
                                <div class="text-sm text-gray-600">Total de Clientes</div>
                            </div>
                        </div>
                    </Link>

                    <!-- Card Agendamentos Hoje -->
                    <Link :href="route('appointments.index') + '?today=true'" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer group">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 group-hover:scale-110 transition-transform">📅</div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ stats.todayAppointments }}</div>
                                <div class="text-sm text-gray-600">Agendamentos Hoje</div>
                            </div>
                        </div>
                    </Link>

                    <!-- Card Receita Mensal -->
                    <Link :href="route('financial.index')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer group">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 group-hover:scale-110 transition-transform">💰</div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900 group-hover:text-green-600 transition-colors">R$ {{ stats.monthlyRevenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}</div>
                                <div class="text-sm text-gray-600">Receita Mensal</div>
                            </div>
                        </div>
                    </Link>

                    <!-- Card Serviços Ativos -->
                    <Link :href="route('services.index')" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer group">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 group-hover:scale-110 transition-transform">✂️</div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900 group-hover:text-purple-600 transition-colors">{{ stats.activeServices }}</div>
                                <div class="text-sm text-gray-600">Serviços Ativos</div>
                            </div>
                        </div>
                    </Link>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Agendamentos Recentes -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Agendamentos de Hoje</h3>
                        </div>
                        <div class="p-6">
                            <div v-if="loading" class="flex items-center justify-center py-8">
                                <div class="text-gray-500">Carregando agendamentos...</div>
                            </div>
                            <div v-else-if="recentAppointments.length === 0" class="text-center py-8">
                                <div class="text-gray-500">Nenhum agendamento para hoje</div>
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="appointment in recentAppointments" :key="appointment.id" 
                                     class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">{{ appointment.client.charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ appointment.client }}</p>
                                            <p class="text-sm text-gray-600">{{ appointment.service }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900">{{ appointment.time }}</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="{
                                                  'bg-green-100 text-green-800': appointment.status === 'confirmed',
                                                  'bg-yellow-100 text-yellow-800': appointment.status === 'pending',
                                                  'bg-red-100 text-red-800': appointment.status === 'cancelled'
                                              }">
                                            {{ appointment.status === 'confirmed' ? 'Confirmado' : 
                                               appointment.status === 'pending' ? 'Pendente' : 'Cancelado' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <Link :href="route('appointments.index')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Ver todos os agendamentos →
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Ações Rápidas</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4">
                                <template v-for="action in quickActions" :key="action.name">
                                    <Link v-if="action.name === 'Novo Agendamento'" :href="route('appointments.index')" 
                                          class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="text-2xl mb-2">{{ action.icon }}</div>
                                        <div class="text-sm font-medium text-gray-900 text-center">{{ action.name }}</div>
                                    </Link>
                                    <Link v-else-if="action.name === 'Cadastrar Cliente'" :href="route('clients.index')" 
                                          class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="text-2xl mb-2">{{ action.icon }}</div>
                                        <div class="text-sm font-medium text-gray-900 text-center">{{ action.name }}</div>
                                    </Link>
                                    <Link v-else-if="action.name === 'Gerenciar Serviços'" :href="route('services.index')" 
                                           class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                         <div class="text-2xl mb-2">{{ action.icon }}</div>
                                         <div class="text-sm font-medium text-gray-900 text-center">{{ action.name }}</div>
                                     </Link>
                                     <Link v-else-if="action.name === 'Gerenciar Categorias'" :href="route('categories.index')" 
                                           class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                         <div class="text-2xl mb-2">{{ action.icon }}</div>
                                         <div class="text-sm font-medium text-gray-900 text-center">{{ action.name }}</div>
                                     </Link>
                                     <Link v-else-if="action.name === 'Ver Agendamentos'" :href="route('appointments.index')" 
                                           class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                         <div class="text-2xl mb-2">{{ action.icon }}</div>
                                         <div class="text-sm font-medium text-gray-900 text-center">{{ action.name }}</div>
                                     </Link>

                                     <Link v-else-if="action.name === 'Financeiro'" :href="route('financial.index')" 
                                           class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                         <div class="text-2xl mb-2">{{ action.icon }}</div>
                                         <div class="text-sm font-medium text-gray-900 text-center">{{ action.name }}</div>
                                     </Link>
                                     <Link v-else-if="action.name === 'Notificações'" :href="route('notifications.index')" 
                                           class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                         <div class="text-2xl mb-2">{{ action.icon }}</div>
                                         <div class="text-sm font-medium text-gray-900 text-center">{{ action.name }}</div>
                                     </Link>
                                     <Link v-else-if="action.name === 'Fidelidade'" :href="route('loyalty.index')" 
                                           class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                         <div class="text-2xl mb-2">{{ action.icon }}</div>
                                         <div class="text-sm font-medium text-gray-900 text-center">{{ action.name }}</div>
                                     </Link>
                                    <a v-else :href="action.route"
                                       class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="text-2xl mb-2">{{ action.icon }}</div>
                                        <div class="text-sm font-medium text-gray-900 text-center">{{ action.name }}</div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações do Tenant -->
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informações da Empresa</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Você está logado como <strong>{{ $page.props.auth.user.role }}</strong> 
                                    no sistema multi-tenant.
                                </p>
                            </div>
                            <div class="text-sm text-gray-500">
                                Último login: {{ new Date($page.props.auth.user.last_login_at || Date.now()).toLocaleString('pt-BR') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
