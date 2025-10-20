<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import EditButton from '@/Components/EditButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';

// Estados reativos
const activeTab = ref('overview');
const showModal = ref(false);
const modalType = ref('create'); // 'create', 'edit', 'redeem'
const editingItem = ref(null);
const searchTerm = ref('');
const filterStatus = ref('all');

// Formulários
const rewardForm = ref({
    name: '',
    description: '',
    points_required: 0,
    discount_percentage: 0,
    discount_amount: 0,
    type: 'discount_percentage', // 'discount_percentage', 'discount_amount', 'free_service'
    service_id: '',
    is_active: true,
    expiry_days: 30
});

const redeemForm = ref({
    client_id: '',
    reward_id: '',
    notes: ''
});

// Dados simulados
const loyaltyStats = ref({
    totalMembers: 156,
    activeMembers: 142,
    totalPointsIssued: 45680,
    totalRedemptions: 90,
    averagePointsPerClient: 318
});

// Função para obter clientes da página de clientes e sincronizar com dados de fidelidade
const getClientsFromStorage = () => {
    // Em um app real, isso viria de uma API ou store global
    // Por enquanto, vamos usar os dados base e permitir que novos clientes sejam adicionados
    const baseClients = [
        {
            id: 1,
            name: 'Ana Silva',
            email: 'ana.silva@email.com',
            phone: '(11) 99999-9999',
            points_balance: 450,
            total_points_earned: 1250,
            total_points_redeemed: 800,
            member_since: '2024-01-15',
            last_visit: '2024-01-20',
            tier: 'gold',
            visits_count: 25
        },
        {
            id: 2,
            name: 'Maria Santos',
            email: 'maria.santos@email.com',
            phone: '(11) 88888-8888',
            points_balance: 80,
            total_points_earned: 680,
            total_points_redeemed: 600,
            member_since: '2024-01-10',
            last_visit: new Date().toISOString().split('T')[0],
            tier: 'silver',
            visits_count: 16
        },
        {
            id: 3,
            name: 'João Costa',
            email: 'joao.costa@email.com',
            phone: '(11) 77777-7777',
            points_balance: 120,
            total_points_earned: 320,
            total_points_redeemed: 200,
            member_since: '2024-01-05',
            last_visit: '2024-01-19',
            tier: 'bronze',
            visits_count: 8
        },
        {
            id: 4,
            name: 'Carla Oliveira',
            email: 'carla.oliveira@email.com',
            phone: '(11) 66666-6666',
            points_balance: 0,
            total_points_earned: 0,
            total_points_redeemed: 0,
            member_since: '2024-01-12',
            last_visit: null,
            tier: 'bronze',
            visits_count: 0
        }
    ];
    
    // Simular busca de clientes adicionais do localStorage ou store global
    const storedClients = JSON.parse(localStorage.getItem('additional_clients') || '[]');
    
    // Adicionar novos clientes com dados de fidelidade padrão
    const additionalClients = storedClients.map(client => ({
        ...client,
        points_balance: client.points_balance || 0,
        total_points_earned: client.total_points_earned || 0,
        total_points_redeemed: client.total_points_redeemed || 0,
        member_since: client.created_at || new Date().toISOString().split('T')[0],
        last_visit: client.last_appointment || null,
        tier: 'bronze',
        visits_count: 0
    }));
    
    return [...baseClients, ...additionalClients];
};

const clients = ref(getClientsFromStorage());

// Função para recarregar clientes (útil quando navegar entre páginas)
const refreshClients = () => {
    clients.value = getClientsFromStorage();
};

// Recarregar clientes quando a página for montada
onMounted(() => {
    refreshClients();
});

const rewards = ref([
    {
        id: 1,
        name: '10% de Desconto',
        description: 'Desconto de 10% em qualquer serviço',
        points_required: 100,
        type: 'discount_percentage',
        discount_percentage: 10,
        discount_amount: 0,
        is_active: true,
        redemptions_count: 45,
        created_at: '2023-06-01'
    },
    {
        id: 2,
        name: 'R$ 20 de Desconto',
        description: 'Desconto fixo de R$ 20,00',
        points_required: 200,
        type: 'discount_amount',
        discount_percentage: 0,
        discount_amount: 20,
        is_active: true,
        redemptions_count: 28,
        created_at: '2023-06-01'
    },
    {
        id: 3,
        name: 'Manicure Grátis',
        description: 'Serviço de manicure gratuito',
        points_required: 300,
        type: 'free_service',
        service_name: 'Manicure',
        is_active: true,
        redemptions_count: 16,
        created_at: '2023-06-01'
    },
    {
        id: 4,
        name: '25% de Desconto VIP',
        description: 'Desconto especial de 25% para clientes VIP',
        points_required: 500,
        type: 'discount_percentage',
        discount_percentage: 25,
        discount_amount: 0,
        is_active: true,
        redemptions_count: 8,
        created_at: '2023-08-15'
    }
]);

const transactions = ref([
    {
        id: 1,
        client_name: 'Ana Silva',
        type: 'earned',
        points: 50,
        description: 'Pontos ganhos - Corte + Escova',
        date: '2024-01-20T14:30:00',
        appointment_id: 123
    },
    {
        id: 2,
        client_name: 'Maria Santos',
        type: 'redeemed',
        points: -100,
        description: 'Resgate - 10% de Desconto',
        date: '2024-01-18T10:15:00',
        reward_name: '10% de Desconto'
    },
    {
        id: 6,
        client_name: 'Maria Santos',
        type: 'redeemed',
        points: -200,
        description: 'Resgate - R$ 20 de Desconto aplicado na Hidratação Capilar',
        date: new Date().toISOString(),
        reward_name: 'R$ 20 de Desconto',
        appointment_id: 2
    },
    {
        id: 3,
        client_name: 'João Costa',
        type: 'earned',
        points: 30,
        description: 'Pontos ganhos - Barba',
        date: '2024-01-19T16:00:00',
        appointment_id: 124
    },
    {
        id: 4,
        client_name: 'Maria Santos',
        type: 'earned',
        points: 120,
        description: 'Pontos ganhos - Hidratação Capilar',
        date: '2024-01-18T11:30:00',
        appointment_id: 125
    },
    {
        id: 5,
        client_name: 'Ana Silva',
        type: 'bonus',
        points: 100,
        description: 'Bônus de aniversário',
        date: '2024-01-15T09:00:00'
    }
]);

const services = ref([
    { id: 1, name: 'Corte Feminino', price: 50 },
    { id: 2, name: 'Corte Masculino', price: 30 },
    { id: 3, name: 'Manicure', price: 25 },
    { id: 4, name: 'Pedicure', price: 30 },
    { id: 5, name: 'Escova', price: 35 }
]);

// Configurações do programa
const settings = ref({
    points_per_real: 1, // 1 ponto por real gasto
    birthday_bonus: 100,
    referral_bonus: 50,
    minimum_redemption: 50,
    points_expiry_months: 12,
    tiers: {
        bronze: { min_visits: 0, bonus_multiplier: 1.0 },
        silver: { min_visits: 10, bonus_multiplier: 1.2 },
        gold: { min_visits: 20, bonus_multiplier: 1.5 },
        platinum: { min_visits: 30, bonus_multiplier: 2.0 }
    }
});

// Computadas
const filteredClients = computed(() => {
    let filtered = clients.value;
    
    if (searchTerm.value) {
        filtered = filtered.filter(c => 
            c.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            c.email.toLowerCase().includes(searchTerm.value.toLowerCase())
        );
    }
    
    return filtered.sort((a, b) => b.points_balance - a.points_balance);
});

const filteredRewards = computed(() => {
    let filtered = rewards.value;
    
    if (filterStatus.value === 'active') {
        filtered = filtered.filter(r => r.is_active);
    } else if (filterStatus.value === 'inactive') {
        filtered = filtered.filter(r => !r.is_active);
    }
    
    return filtered.sort((a, b) => a.points_required - b.points_required);
});

const recentTransactions = computed(() => {
    return transactions.value
        .sort((a, b) => new Date(b.date) - new Date(a.date))
        .slice(0, 10);
});

// Métodos
const openModal = (type, item = null) => {
    modalType.value = type;
    editingItem.value = item;
    
    if (type === 'reward') {
        if (item) {
            rewardForm.value = { ...item };
        } else {
            rewardForm.value = {
                name: '',
                description: '',
                points_required: 0,
                discount_percentage: 0,
                discount_amount: 0,
                type: 'discount_percentage',
                service_id: '',
                is_active: true,
                expiry_days: 30
            };
        }
    } else if (type === 'redeem') {
        redeemForm.value = {
            client_id: '',
            reward_id: '',
            notes: ''
        };
    }
    
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingItem.value = null;
};

const saveReward = () => {
    if (editingItem.value) {
        // Editar
        const index = rewards.value.findIndex(r => r.id === editingItem.value.id);
        rewards.value[index] = { ...rewardForm.value, id: editingItem.value.id };
    } else {
        // Criar
        const newReward = {
            ...rewardForm.value,
            id: Date.now(),
            redemptions_count: 0,
            created_at: new Date().toISOString()
        };
        rewards.value.push(newReward);
    }
    
    closeModal();
};

const processRedemption = () => {
    const client = clients.value.find(c => c.id == redeemForm.value.client_id);
    const reward = rewards.value.find(r => r.id == redeemForm.value.reward_id);
    
    if (client && reward && client.points_balance >= reward.points_required) {
        // Deduzir pontos
        client.points_balance -= reward.points_required;
        client.total_points_redeemed += reward.points_required;
        
        // Adicionar transação
        const newTransaction = {
            id: Date.now(),
            client_name: client.name,
            type: 'redeemed',
            points: -reward.points_required,
            description: `Resgate - ${reward.name}`,
            date: new Date().toISOString(),
            reward_name: reward.name
        };
        transactions.value.unshift(newTransaction);
        
        // Incrementar contador de resgates
        reward.redemptions_count++;
        
        alert('Resgate processado com sucesso!');
        closeModal();
    } else {
        alert('Pontos insuficientes ou dados inválidos!');
    }
};

const addPoints = (clientId, points, description) => {
    const client = clients.value.find(c => c.id === clientId);
    if (client) {
        client.points_balance += points;
        client.total_points_earned += points;
        
        const newTransaction = {
            id: Date.now(),
            client_name: client.name,
            type: 'earned',
            points: points,
            description: description,
            date: new Date().toISOString()
        };
        transactions.value.unshift(newTransaction);
    }
};

const getTierColor = (tier) => {
    switch (tier) {
        case 'bronze': return 'text-amber-600 bg-amber-100';
        case 'silver': return 'text-gray-600 bg-gray-100';
        case 'gold': return 'text-yellow-600 bg-yellow-100';
        case 'platinum': return 'text-purple-600 bg-purple-100';
        default: return 'text-gray-600 bg-gray-100';
    }
};

const getTierText = (tier) => {
    switch (tier) {
        case 'bronze': return 'Bronze';
        case 'silver': return 'Prata';
        case 'gold': return 'Ouro';
        case 'platinum': return 'Platina';
        default: return 'Bronze';
    }
};

const getTransactionColor = (type) => {
    switch (type) {
        case 'earned': return 'text-green-600';
        case 'redeemed': return 'text-red-600';
        case 'bonus': return 'text-blue-600';
        default: return 'text-gray-600';
    }
};

const formatDateTime = (dateTime) => {
    return new Date(dateTime).toLocaleString('pt-BR');
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const deleteReward = (id) => {
    if (confirm('Tem certeza que deseja excluir esta recompensa?')) {
        rewards.value = rewards.value.filter(r => r.id !== id);
    }
};

const toggleRewardStatus = (id) => {
    const reward = rewards.value.find(r => r.id === id);
    if (reward) {
        reward.is_active = !reward.is_active;
    }
};
</script>

<template>
    <Head title="Programa de Fidelidade" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Programa de Fidelidade
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Cards de Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4">👥</div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900">{{ loyaltyStats.totalMembers }}</div>
                                <div class="text-sm text-gray-600">Total de Membros</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 text-green-500">✅</div>
                            <div>
                                <div class="text-2xl font-bold text-green-600">{{ loyaltyStats.activeMembers }}</div>
                                <div class="text-sm text-gray-600">Membros Ativos</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 text-blue-500">🎯</div>
                            <div>
                                <div class="text-2xl font-bold text-blue-600">{{ loyaltyStats.totalPointsIssued.toLocaleString() }}</div>
                                <div class="text-sm text-gray-600">Pontos Emitidos</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 text-purple-500">🎁</div>
                            <div>
                                <div class="text-2xl font-bold text-purple-600">{{ loyaltyStats.totalRedemptions }}</div>
                                <div class="text-sm text-gray-600">Resgates</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 text-yellow-500">⭐</div>
                            <div>
                                <div class="text-2xl font-bold text-yellow-600">{{ loyaltyStats.averagePointsPerClient }}</div>
                                <div class="text-sm text-gray-600">Média por Cliente</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navegação por Abas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 px-6">
                            <SecondaryButton @click="activeTab = 'overview'" 
                                    :class="activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Visão Geral
                            </SecondaryButton>
                            <SecondaryButton @click="activeTab = 'clients'" 
                                    :class="activeTab === 'clients' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Clientes
                            </SecondaryButton>
                            <SecondaryButton @click="activeTab = 'rewards'" 
                                    :class="activeTab === 'rewards' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Recompensas
                            </SecondaryButton>
                            <SecondaryButton @click="activeTab = 'transactions'" 
                                    :class="activeTab === 'transactions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Transações
                            </SecondaryButton>
                        </nav>
                    </div>
                </div>

                <!-- Aba Visão Geral -->
                <div v-if="activeTab === 'overview'">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Top Clientes -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Top Clientes por Pontos</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div v-for="(client, index) in clients.slice(0, 5)" :key="client.id" 
                                         class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="text-2xl mr-3">
                                                {{ index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : '🏅' }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ client.name }}</div>
                                                <div class="text-sm text-gray-600">
                                                    <span :class="getTierColor(client.tier)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                                        {{ getTierText(client.tier) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-bold text-blue-600">{{ client.points_balance }} pts</div>
                                            <div class="text-sm text-gray-500">{{ client.visits_count }} visitas</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transações Recentes -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Transações Recentes</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    <div v-for="transaction in recentTransactions.slice(0, 6)" :key="transaction.id" 
                                         class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ transaction.client_name }}</div>
                                            <div class="text-sm text-gray-600">{{ transaction.description }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div :class="getTransactionColor(transaction.type)" class="font-bold">
                                                {{ transaction.points > 0 ? '+' : '' }}{{ transaction.points }} pts
                                            </div>
                                            <div class="text-xs text-gray-500">{{ formatDate(transaction.date) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba Clientes -->
                <div v-if="activeTab === 'clients'">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <!-- Cabeçalho com Filtros -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                                <div class="flex space-x-4">
                                    <input v-model="searchTerm" type="text" placeholder="Buscar clientes..."
                                           class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                </div>
                                <div class="flex space-x-2">
                                    <PrimaryButton @click="openModal('redeem')">
                                        Processar Resgate
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Clientes -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nível</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pontos</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visitas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membro desde</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última visita</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="client in filteredClients" :key="client.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="font-medium">{{ client.name }}</div>
                                            <div class="text-gray-500">{{ client.email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="getTierColor(client.tier)"
                                                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ getTierText(client.tier) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-bold text-blue-600">{{ client.points_balance }}</div>
                                            <div class="text-xs text-gray-500">Total: {{ client.total_points_earned }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ client.visits_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(client.member_since) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(client.last_visit) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <SecondaryButton @click="addPoints(client.id, 50, 'Pontos manuais')" 
                                                    class="text-green-600 hover:text-green-900 mr-3">+ Pontos</SecondaryButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Aba Recompensas -->
                <div v-if="activeTab === 'rewards'">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <!-- Cabeçalho -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                                <div class="flex space-x-4">
                                    <select v-model="filterStatus" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                        <option value="all">Todas as recompensas</option>
                                        <option value="active">Ativas</option>
                                        <option value="inactive">Inativas</option>
                                    </select>
                                </div>
                                <div class="flex space-x-2">
                                    <PrimaryButton @click="openModal('reward')">
                                        + Nova Recompensa
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Recompensas -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                            <div v-for="reward in filteredRewards" :key="reward.id" 
                                 class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ reward.name }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ reward.description }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <SecondaryButton @click="toggleRewardStatus(reward.id)" 
                                                :class="reward.is_active ? 'text-green-600' : 'text-gray-400'"
                                                class="hover:text-green-800">
                                            {{ reward.is_active ? '✅' : '⏸️' }}
                                        </SecondaryButton>
                                        <EditButton @click="openModal('reward', reward)">✏️</EditButton>
                                        <DeleteButton @click="deleteReward(reward.id)">🗑️</DeleteButton>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Pontos necessários:</span>
                                        <span class="font-bold text-blue-600">{{ reward.points_required }}</span>
                                    </div>
                                    
                                    <div v-if="reward.type === 'discount_percentage'" class="flex justify-between text-sm">
                                        <span class="text-gray-600">Desconto:</span>
                                        <span class="font-bold text-green-600">{{ reward.discount_percentage }}%</span>
                                    </div>
                                    
                                    <div v-if="reward.type === 'discount_amount'" class="flex justify-between text-sm">
                                        <span class="text-gray-600">Desconto:</span>
                                        <span class="font-bold text-green-600">R$ {{ reward.discount_amount }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Resgates:</span>
                                        <span class="font-medium">{{ reward.redemptions_count }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <span :class="reward.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                          class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ reward.is_active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aba Transações -->
                <div v-if="activeTab === 'transactions'">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Histórico de Transações</h3>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pontos</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="transaction in transactions" :key="transaction.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ transaction.client_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span v-if="transaction.type === 'earned'" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                Ganho
                                            </span>
                                            <span v-else-if="transaction.type === 'redeemed'" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                Resgate
                                            </span>
                                            <span v-else class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Bônus
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold" :class="getTransactionColor(transaction.type)">
                                            {{ transaction.points > 0 ? '+' : '' }}{{ transaction.points }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ transaction.description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDateTime(transaction.date) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Recompensa -->
        <div v-if="showModal && modalType === 'reward'" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ editingItem ? 'Editar Recompensa' : 'Nova Recompensa' }}
                    </h3>
                    
                    <form @submit.prevent="saveReward" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                                <input v-model="rewardForm.name" type="text" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pontos Necessários</label>
                                <input v-model="rewardForm.points_required" type="number" min="1" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                            <textarea v-model="rewardForm.description" rows="2" required
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                <select v-model="rewardForm.type" required
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <option value="discount_percentage">Desconto Percentual</option>
                                    <option value="discount_amount">Desconto Fixo</option>
                                    <option value="free_service">Serviço Gratuito</option>
                                </select>
                            </div>
                            
                            <div v-if="rewardForm.type === 'discount_percentage'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Desconto (%)</label>
                                <input v-model="rewardForm.discount_percentage" type="number" min="1" max="100"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            </div>
                            
                            <div v-if="rewardForm.type === 'discount_amount'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Desconto (R$)</label>
                                <input v-model="rewardForm.discount_amount" type="number" min="0" step="0.01"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            </div>
                            
                            <div v-if="rewardForm.type === 'free_service'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Serviço</label>
                                <select v-model="rewardForm.service_id"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <option value="">Selecione um serviço</option>
                                    <option v-for="service in services" :key="service.id" :value="service.id">
                                        {{ service.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <input v-model="rewardForm.is_active" type="checkbox" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Recompensa ativa</span>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-4">
                            <SecondaryButton type="button" @click="closeModal">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton type="submit">
                                {{ editingItem ? 'Atualizar' : 'Criar' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Resgate -->
        <div v-if="showModal && modalType === 'redeem'" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Processar Resgate</h3>
                    
                    <form @submit.prevent="processRedemption" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                            <select v-model="redeemForm.client_id" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">Selecione um cliente</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.name }} ({{ client.points_balance }} pontos)
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recompensa</label>
                            <select v-model="redeemForm.reward_id" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">Selecione uma recompensa</option>
                                <option v-for="reward in rewards.filter(r => r.is_active)" :key="reward.id" :value="reward.id">
                                    {{ reward.name }} ({{ reward.points_required }} pontos)
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                            <textarea v-model="redeemForm.notes" rows="2"
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-4">
                            <SecondaryButton type="button" @click="closeModal">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton type="submit">
                                Processar Resgate
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>