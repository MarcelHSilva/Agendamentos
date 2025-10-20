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
const activeTab = ref('notifications');
const showModal = ref(false);
const modalType = ref('create'); // 'create' ou 'edit'
const editingNotification = ref(null);
const searchTerm = ref('');
const filterStatus = ref('all');
const filterType = ref('all');

// Formulário
const form = ref({
    title: '',
    message: '',
    type: 'appointment_reminder',
    scheduled_for: '',
    client_id: '',
    appointment_id: '',
    send_email: true,
    send_sms: false,
    send_whatsapp: true
});

// Dados simulados
const notifications = ref([
    {
        id: 1,
        title: 'Lembrete de Agendamento',
        message: 'Olá Ana! Lembramos que você tem um agendamento amanhã às 14:00 para Corte + Escova.',
        type: 'appointment_reminder',
        status: 'sent',
        scheduled_for: '2024-01-16T13:00:00',
        sent_at: '2024-01-16T13:00:00',
        client_name: 'Ana Silva',
        appointment_service: 'Corte + Escova',
        channels: ['email', 'whatsapp'],
        created_at: '2024-01-15T10:00:00'
    },
    {
        id: 2,
        title: 'Confirmação de Agendamento',
        message: 'Seu agendamento para Manicure foi confirmado para hoje às 10:30.',
        type: 'appointment_confirmation',
        status: 'pending',
        scheduled_for: '2024-01-17T09:30:00',
        client_name: 'Maria Santos',
        appointment_service: 'Manicure',
        channels: ['email', 'sms'],
        created_at: '2024-01-16T15:30:00'
    },
    {
        id: 3,
        title: 'Promoção Especial',
        message: 'Aproveite nossa promoção de 20% de desconto em todos os serviços de cabelo!',
        type: 'promotion',
        status: 'sent',
        scheduled_for: '2024-01-15T09:00:00',
        sent_at: '2024-01-15T09:00:00',
        client_name: 'Todos os clientes',
        channels: ['email', 'whatsapp'],
        created_at: '2024-01-14T16:00:00'
    },
    {
        id: 4,
        title: 'Lembrete de Agendamento',
        message: 'João, não esqueça do seu horário hoje às 16:00 para Barba.',
        type: 'appointment_reminder',
        status: 'failed',
        scheduled_for: '2024-01-16T15:00:00',
        error_message: 'Número de telefone inválido',
        client_name: 'João Costa',
        appointment_service: 'Barba',
        channels: ['sms'],
        created_at: '2024-01-16T10:00:00'
    }
]);

const clients = ref([
    { id: 1, name: 'Ana Silva', phone: '(11) 99999-1111', email: 'ana@email.com' },
    { id: 2, name: 'Maria Santos', phone: '(11) 99999-2222', email: 'maria@email.com' },
    { id: 3, name: 'João Costa', phone: '(11) 99999-3333', email: 'joao@email.com' },
    { id: 4, name: 'Carla Lima', phone: '(11) 99999-4444', email: 'carla@email.com' }
]);

const appointments = ref([
    { id: 1, client_name: 'Ana Silva', service: 'Corte + Escova', date: '2024-01-17', time: '14:00' },
    { id: 2, client_name: 'Maria Santos', service: 'Manicure', date: '2024-01-17', time: '10:30' },
    { id: 3, client_name: 'João Costa', service: 'Barba', date: '2024-01-17', time: '16:00' }
]);

const notificationTypes = ref([
    { value: 'appointment_reminder', label: 'Lembrete de Agendamento' },
    { value: 'appointment_confirmation', label: 'Confirmação de Agendamento' },
    { value: 'appointment_cancellation', label: 'Cancelamento de Agendamento' },
    { value: 'promotion', label: 'Promoção' },
    { value: 'birthday', label: 'Aniversário' },
    { value: 'custom', label: 'Personalizada' }
]);

// Configurações de notificação
const settings = ref({
    email_enabled: true,
    sms_enabled: true,
    whatsapp_enabled: true,
    reminder_hours_before: 24,
    auto_reminders: true,
    auto_confirmations: true,
    birthday_notifications: true
});

// Computadas
const filteredNotifications = computed(() => {
    let filtered = notifications.value;
    
    if (searchTerm.value) {
        filtered = filtered.filter(n => 
            n.title.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            n.message.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            n.client_name.toLowerCase().includes(searchTerm.value.toLowerCase())
        );
    }
    
    if (filterStatus.value !== 'all') {
        filtered = filtered.filter(n => n.status === filterStatus.value);
    }
    
    if (filterType.value !== 'all') {
        filtered = filtered.filter(n => n.type === filterType.value);
    }
    
    return filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const notificationStats = computed(() => {
    const total = notifications.value.length;
    const sent = notifications.value.filter(n => n.status === 'sent').length;
    const pending = notifications.value.filter(n => n.status === 'pending').length;
    const failed = notifications.value.filter(n => n.status === 'failed').length;
    
    return { total, sent, pending, failed };
});

// Métodos
const openModal = (type, notification = null) => {
    modalType.value = type;
    editingNotification.value = notification;
    
    if (notification) {
        form.value = { ...notification };
    } else {
        form.value = {
            title: '',
            message: '',
            type: 'appointment_reminder',
            scheduled_for: '',
            client_id: '',
            appointment_id: '',
            send_email: true,
            send_sms: false,
            send_whatsapp: true
        };
    }
    
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingNotification.value = null;
    form.value = {
        title: '',
        message: '',
        type: 'appointment_reminder',
        scheduled_for: '',
        client_id: '',
        appointment_id: '',
        send_email: true,
        send_sms: false,
        send_whatsapp: true
    };
};

const saveNotification = () => {
    if (editingNotification.value) {
        // Editar
        const index = notifications.value.findIndex(n => n.id === editingNotification.value.id);
        notifications.value[index] = { ...form.value, id: editingNotification.value.id };
    } else {
        // Criar
        const newNotification = {
            ...form.value,
            id: Date.now(),
            status: 'pending',
            created_at: new Date().toISOString(),
            client_name: clients.value.find(c => c.id == form.value.client_id)?.name || 'Cliente não encontrado'
        };
        notifications.value.push(newNotification);
    }
    
    closeModal();
};

const deleteNotification = (id) => {
    if (confirm('Tem certeza que deseja excluir esta notificação?')) {
        notifications.value = notifications.value.filter(n => n.id !== id);
    }
};

const resendNotification = (id) => {
    const notification = notifications.value.find(n => n.id === id);
    if (notification) {
        notification.status = 'pending';
        notification.error_message = null;
        // Aqui seria feita a chamada para reenviar a notificação
        setTimeout(() => {
            notification.status = 'sent';
            notification.sent_at = new Date().toISOString();
        }, 2000);
    }
};

const getStatusColor = (status) => {
    switch (status) {
        case 'sent': return 'text-green-600 bg-green-100';
        case 'pending': return 'text-yellow-600 bg-yellow-100';
        case 'failed': return 'text-red-600 bg-red-100';
        default: return 'text-gray-600 bg-gray-100';
    }
};

const getStatusText = (status) => {
    switch (status) {
        case 'sent': return 'Enviada';
        case 'pending': return 'Pendente';
        case 'failed': return 'Falhou';
        default: return 'Desconhecido';
    }
};

const getTypeText = (type) => {
    const typeObj = notificationTypes.value.find(t => t.value === type);
    return typeObj ? typeObj.label : type;
};

const formatDateTime = (dateTime) => {
    return new Date(dateTime).toLocaleString('pt-BR');
};

const updateSettings = () => {
    // Aqui seria feita a chamada para salvar as configurações
    alert('Configurações salvas com sucesso!');
};

const sendTestNotification = () => {
    // Aqui seria feita a chamada para enviar uma notificação de teste
    alert('Notificação de teste enviada!');
};
</script>

<template>
    <Head title="Sistema de Notificações" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Sistema de Notificações
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Cards de Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4">📧</div>
                            <div>
                                <div class="text-2xl font-bold text-gray-900">{{ notificationStats.total }}</div>
                                <div class="text-sm text-gray-600">Total de Notificações</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 text-green-500">✅</div>
                            <div>
                                <div class="text-2xl font-bold text-green-600">{{ notificationStats.sent }}</div>
                                <div class="text-sm text-gray-600">Enviadas</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 text-yellow-500">⏳</div>
                            <div>
                                <div class="text-2xl font-bold text-yellow-600">{{ notificationStats.pending }}</div>
                                <div class="text-sm text-gray-600">Pendentes</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 text-red-500">❌</div>
                            <div>
                                <div class="text-2xl font-bold text-red-600">{{ notificationStats.failed }}</div>
                                <div class="text-sm text-gray-600">Falharam</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navegação por Abas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 px-6">
                            <button @click="activeTab = 'notifications'" 
                                    :class="activeTab === 'notifications' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Notificações
                            </button>
                            <button @click="activeTab = 'settings'" 
                                    :class="activeTab === 'settings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Configurações
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Aba de Notificações -->
                <div v-if="activeTab === 'notifications'">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <!-- Cabeçalho com Filtros e Ações -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                                    <input v-model="searchTerm" type="text" placeholder="Buscar notificações..."
                                           class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <select v-model="filterStatus" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                        <option value="all">Todos os status</option>
                                        <option value="sent">Enviadas</option>
                                        <option value="pending">Pendentes</option>
                                        <option value="failed">Falharam</option>
                                    </select>
                                    <select v-model="filterType" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                        <option value="all">Todos os tipos</option>
                                        <option v-for="type in notificationTypes" :key="type.value" :value="type.value">
                                            {{ type.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="flex space-x-2">
                                    <PrimaryButton @click="openModal('create')">
                                        + Nova Notificação
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Notificações -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agendado para</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Canais</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="notification in filteredNotifications" :key="notification.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="font-medium">{{ notification.title }}</div>
                                            <div class="text-gray-500 text-xs mt-1 truncate max-w-xs">{{ notification.message }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ notification.client_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ getTypeText(notification.type) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="getStatusColor(notification.status)"
                                                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ getStatusText(notification.status) }}
                                            </span>
                                            <div v-if="notification.error_message" class="text-red-500 text-xs mt-1">
                                                {{ notification.error_message }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDateTime(notification.scheduled_for) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="flex space-x-1">
                                                <span v-for="channel in notification.channels" :key="channel"
                                                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ channel }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <EditButton @click="openModal('edit', notification)" size="xs">
                                                    Editar
                                                </EditButton>
                                                <PrimaryButton v-if="notification.status === 'failed'" @click="resendNotification(notification.id)" 
                                                               class="!bg-green-600 hover:!bg-green-700 !px-2 !py-1 !text-xs">
                                                    Reenviar
                                                </PrimaryButton>
                                                <DeleteButton @click="deleteNotification(notification.id)" size="xs">
                                                    Excluir
                                                </DeleteButton>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Aba de Configurações -->
                <div v-if="activeTab === 'settings'">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Configurações Gerais -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Configurações Gerais</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700">Notificações por Email</label>
                                    <input v-model="settings.email_enabled" type="checkbox" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700">Notificações por SMS</label>
                                    <input v-model="settings.sms_enabled" type="checkbox" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700">Notificações por WhatsApp</label>
                                    <input v-model="settings.whatsapp_enabled" type="checkbox" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lembrete (horas antes)</label>
                                    <input v-model="settings.reminder_hours_before" type="number" min="1" max="168"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Configurações Automáticas -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Notificações Automáticas</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700">Lembretes Automáticos</label>
                                    <input v-model="settings.auto_reminders" type="checkbox" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700">Confirmações Automáticas</label>
                                    <input v-model="settings.auto_confirmations" type="checkbox" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700">Notificações de Aniversário</label>
                                    <input v-model="settings.birthday_notifications" type="checkbox" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                            </div>
                            
                            <div class="mt-6 space-y-2">
                                <PrimaryButton @click="updateSettings" class="w-full">
                                    Salvar Configurações
                                </PrimaryButton>
                                <SecondaryButton @click="sendTestNotification" class="w-full">
                                    Enviar Notificação de Teste
                                </SecondaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Notificação -->
        <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ modalType === 'create' ? 'Nova Notificação' : 'Editar Notificação' }}
                    </h3>
                    
                    <form @submit.prevent="saveNotification" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                <input v-model="form.title" type="text" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                <select v-model="form.type" required
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <option v-for="type in notificationTypes" :key="type.value" :value="type.value">
                                        {{ type.label }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mensagem</label>
                            <textarea v-model="form.message" rows="3" required
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                                <select v-model="form.client_id"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <option value="">Selecione um cliente</option>
                                    <option v-for="client in clients" :key="client.id" :value="client.id">
                                        {{ client.name }}
                                    </option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Agendamento</label>
                                <select v-model="form.appointment_id"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <option value="">Selecione um agendamento</option>
                                    <option v-for="appointment in appointments" :key="appointment.id" :value="appointment.id">
                                        {{ appointment.client_name }} - {{ appointment.service }} ({{ appointment.date }} {{ appointment.time }})
                                    </option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Agendar para</label>
                            <input v-model="form.scheduled_for" type="datetime-local" required
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Canais de Envio</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input v-model="form.send_email" type="checkbox" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Email</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="form.send_sms" type="checkbox" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">SMS</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="form.send_whatsapp" type="checkbox" 
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">WhatsApp</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-4">
                            <SecondaryButton type="button" @click="closeModal">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton type="submit">
                                {{ modalType === 'create' ? 'Criar' : 'Atualizar' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>