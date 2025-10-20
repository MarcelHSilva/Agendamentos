<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import EditButton from '@/Components/EditButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';

// Dados simulados para demonstração
const appointments = ref([
    {
        id: 1,
        client_name: 'João Silva',
        client_phone: '(11) 99999-9999',
        service_name: 'Corte Masculino',
        service_duration: 30,
        service_price: 40.00,
        date: new Date().toISOString().split('T')[0], // Data de hoje
        time: '09:00',
        status: 'confirmado',
        notes: 'Cliente regular',
        created_at: '2024-01-15'
    },
    {
        id: 2,
        client_name: 'Maria Santos',
        client_phone: '(11) 88888-8888',
        service_name: 'Hidratação Capilar',
        service_duration: 90,
        service_price: 120.00,
        original_price: 120.00,
        discount_amount: 20.00,
        discount_type: 'loyalty_points',
        final_price: 100.00,
        date: new Date().toISOString().split('T')[0], // Data de hoje
        time: '10:30',
        status: 'confirmado',
        notes: 'Cabelo muito ressecado - Desconto de fidelidade aplicado (R$ 20,00)',
        created_at: '2024-01-15'
    },
    {
        id: 3,
        client_name: 'Pedro Costa',
        client_phone: '(11) 77777-7777',
        service_name: 'Corte + Barba',
        service_duration: 60,
        service_price: 65.00,
        date: new Date().toISOString().split('T')[0], // Data de hoje
        time: '14:00',
        status: 'confirmado',
        notes: 'Primeira vez no salão',
        created_at: '2024-01-18'
    },
    {
        id: 4,
        client_name: 'Ana Oliveira',
        client_phone: '(11) 66666-6666',
        service_name: 'Manicure',
        service_duration: 45,
        service_price: 35.00,
        date: '2024-01-21',
        time: '11:00',
        status: 'agendado',
        notes: 'Cliente preferencial',
        created_at: '2024-01-19'
    },
    {
        id: 5,
        client_name: 'Carlos Ferreira',
        client_phone: '(11) 55555-5555',
        service_name: 'Corte + Escova',
        service_duration: 60,
        service_price: 80.00,
        date: '2024-01-19',
        time: '16:00',
        status: 'concluido',
        notes: 'Pagamento em dinheiro',
        created_at: '2024-01-17'
    },
    {
        id: 6,
        client_name: 'Lucia Mendes',
        client_phone: '(11) 44444-4444',
        service_name: 'Corte Feminino',
        service_duration: 40,
        service_price: 30.00,
        date: '2024-01-18',
        time: '15:30',
        status: 'cancelado',
        notes: 'Cliente cancelou por motivos pessoais',
        created_at: '2024-01-16'
    }
]);

const clients = ref([
    { id: 1, name: 'João Silva', phone: '(11) 99999-9999' },
    { id: 2, name: 'Maria Santos', phone: '(11) 88888-8888' },
    { id: 3, name: 'Pedro Costa', phone: '(11) 77777-7777' },
    { id: 4, name: 'Ana Oliveira', phone: '(11) 66666-6666' },
    { id: 5, name: 'Carlos Ferreira', phone: '(11) 55555-5555' }
]);

const services = ref([
    { id: 1, name: 'Corte Masculino', duration: 30, price: 25.00 },
    { id: 2, name: 'Corte Feminino', duration: 40, price: 30.00 },
    { id: 3, name: 'Barba Completa', duration: 20, price: 15.00 },
    { id: 4, name: 'Corte + Barba', duration: 45, price: 35.00 },
    { id: 5, name: 'Hidratação Capilar', duration: 60, price: 45.00 },
    { id: 6, name: 'Sobrancelha', duration: 15, price: 12.00 }
]);

const statusOptions = [
    { value: 'agendado', label: 'Agendado', color: 'blue' },
    { value: 'confirmado', label: 'Confirmado', color: 'green' },
    { value: 'em_andamento', label: 'Em Andamento', color: 'yellow' },
    { value: 'concluido', label: 'Concluído', color: 'gray' },
    { value: 'cancelado', label: 'Cancelado', color: 'red' }
];

const currentDate = ref(new Date().toISOString().split('T')[0]);
const selectedDate = ref(new Date().toISOString().split('T')[0]);
const viewMode = ref('list'); // 'list' ou 'calendar'
const searchTerm = ref('');
const selectedStatus = ref('');
const showTodayOnly = ref(false);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedAppointment = ref(null);

const newAppointment = ref({
    client_id: '',
    client_name: '',
    client_phone: '',
    service_id: '',
    date: '',
    time: '',
    notes: '',
    discount_amount: 0,
    discount_type: '',
    loyalty_discount_applied: false
});

const editAppointment = ref({
    id: null,
    client_id: '',
    client_name: '',
    client_phone: '',
    service_id: '',
    date: '',
    time: '',
    status: 'agendado',
    notes: '',
    discount_amount: 0,
    discount_type: '',
    original_price: 0,
    final_price: 0
});

// Filtrar agendamentos
const filteredAppointments = computed(() => {
    let filtered = appointments.value;
    
    if (searchTerm.value) {
        filtered = filtered.filter(appointment => 
            appointment.client_name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            appointment.service_name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            appointment.client_phone.includes(searchTerm.value)
        );
    }
    
    if (selectedStatus.value) {
        filtered = filtered.filter(appointment => appointment.status === selectedStatus.value);
    }
    
    if (showTodayOnly.value) {
        filtered = filtered.filter(appointment => appointment.date === currentDate.value);
    } else if (viewMode.value === 'calendar') {
        filtered = filtered.filter(appointment => appointment.date === selectedDate.value);
    }
    
    return filtered.sort((a, b) => {
        const dateA = new Date(`${a.date} ${a.time}`);
        const dateB = new Date(`${b.date} ${b.time}`);
        return dateA - dateB;
    });
});

// Agendamentos do dia selecionado
const todayAppointments = computed(() => {
    return appointments.value.filter(appointment => appointment.date === selectedDate.value)
        .sort((a, b) => a.time.localeCompare(b.time));
});

// Estatísticas
const stats = computed(() => {
    const today = new Date().toISOString().split('T')[0];
    const todayAppts = appointments.value.filter(a => a.date === today);
    const thisWeek = appointments.value.filter(a => {
        const appointmentDate = new Date(a.date);
        const startOfWeek = new Date();
        startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay());
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);
        return appointmentDate >= startOfWeek && appointmentDate <= endOfWeek;
    });
    
    const revenue = appointments.value
        .filter(a => a.status === 'concluido')
        .reduce((sum, a) => sum + a.service_price, 0);
    
    return {
        today: todayAppts.length,
        confirmed: appointments.value.filter(a => a.status === 'confirmado').length,
        thisWeek: thisWeek.length,
        revenue: revenue
    };
});

// Gerar horários disponíveis
const generateTimeSlots = () => {
    const slots = [];
    for (let hour = 8; hour <= 18; hour++) {
        for (let minute = 0; minute < 60; minute += 30) {
            const time = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
            slots.push(time);
        }
    }
    return slots;
};

const timeSlots = generateTimeSlots();

// Funções CRUD
const openCreateModal = () => {
    newAppointment.value = {
        client_id: '',
        client_name: '',
        client_phone: '',
        service_id: '',
        date: selectedDate.value,
        time: '',
        notes: ''
    };
    showCreateModal.value = true;
};

const onClientChange = () => {
    const client = clients.value.find(c => c.id == newAppointment.value.client_id);
    if (client) {
        newAppointment.value.client_name = client.name;
        newAppointment.value.client_phone = client.phone;
    }
};

const onEditClientChange = () => {
    const client = clients.value.find(c => c.id == editAppointment.value.client_id);
    if (client) {
        editAppointment.value.client_name = client.name;
        editAppointment.value.client_phone = client.phone;
    }
};

const createAppointment = () => {
    const service = services.value.find(s => s.id == newAppointment.value.service_id);
    
    const appointment = {
        id: appointments.value.length + 1,
        client_name: newAppointment.value.client_name,
        client_phone: newAppointment.value.client_phone,
        service_name: service.name,
        service_duration: service.duration,
        service_price: service.price,
        original_price: service.price,
        discount_amount: newAppointment.value.discount_amount || 0,
        discount_type: newAppointment.value.discount_type || '',
        final_price: service.price - (newAppointment.value.discount_amount || 0),
        date: newAppointment.value.date,
        time: newAppointment.value.time,
        status: 'agendado',
        notes: newAppointment.value.notes,
        created_at: new Date().toISOString().split('T')[0]
    };
    
    appointments.value.push(appointment);
    showCreateModal.value = false;
};

const openEditModal = (appointment) => {
    const client = clients.value.find(c => c.name === appointment.client_name);
    const service = services.value.find(s => s.name === appointment.service_name);
    
    editAppointment.value = {
        id: appointment.id,
        client_id: client ? client.id : '',
        client_name: appointment.client_name,
        client_phone: appointment.client_phone,
        service_id: service ? service.id : '',
        date: appointment.date,
        time: appointment.time,
        status: appointment.status,
        notes: appointment.notes,
        discount_amount: appointment.discount_amount || 0,
        discount_type: appointment.discount_type || '',
        original_price: appointment.original_price || appointment.service_price,
        final_price: appointment.final_price || appointment.service_price
    };
    showEditModal.value = true;
};

const updateAppointment = () => {
    const index = appointments.value.findIndex(a => a.id === editAppointment.value.id);
    if (index !== -1) {
        const service = services.value.find(s => s.id == editAppointment.value.service_id);
        
        appointments.value[index] = {
            ...appointments.value[index],
            client_name: editAppointment.value.client_name,
            client_phone: editAppointment.value.client_phone,
            service_name: service.name,
            service_duration: service.duration,
            service_price: service.price,
            original_price: editAppointment.value.original_price || service.price,
            discount_amount: editAppointment.value.discount_amount || 0,
            discount_type: editAppointment.value.discount_type || '',
            final_price: (editAppointment.value.original_price || service.price) - (editAppointment.value.discount_amount || 0),
            date: editAppointment.value.date,
            time: editAppointment.value.time,
            status: editAppointment.value.status,
            notes: editAppointment.value.notes
        };
    }
    showEditModal.value = false;
};

const openDeleteModal = (appointment) => {
    selectedAppointment.value = appointment;
    showDeleteModal.value = true;
};

const deleteAppointment = () => {
    const index = appointments.value.findIndex(a => a.id === selectedAppointment.value.id);
    if (index !== -1) {
        appointments.value.splice(index, 1);
    }
    showDeleteModal.value = false;
    selectedAppointment.value = null;
};

const updateStatus = (appointment, newStatus) => {
    appointment.status = newStatus;
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

const formatTime = (time) => {
    return time;
};

const getStatusColor = (status) => {
    const statusOption = statusOptions.find(s => s.value === status);
    return statusOption ? statusOption.color : 'gray';
};

const getStatusLabel = (status) => {
    const statusOption = statusOptions.find(s => s.value === status);
    return statusOption ? statusOption.label : status;
};

const isTimeSlotAvailable = (date, time) => {
    return !appointments.value.some(a => 
        a.date === date && 
        a.time === time && 
        ['agendado', 'confirmado', 'em_andamento'].includes(a.status)
    );
};

// Função para mostrar apenas agendamentos de hoje
const showTodayAppointments = () => {
    showTodayOnly.value = true;
    viewMode.value = 'list';
};

// Função para mostrar todos os agendamentos
const showAllAppointments = () => {
    showTodayOnly.value = false;
};

// Verificar se há parâmetro de URL para mostrar apenas hoje
onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('today') === 'true') {
        showTodayAppointments();
    }
});
</script>

<template>
    <Head title="Agendamentos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Agendamentos
                </h2>
                <div class="flex space-x-3">
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button
                            @click="showTodayAppointments"
                            :class="[
                                'px-3 py-1 rounded text-sm font-medium transition-colors',
                                showTodayOnly 
                                    ? 'bg-blue-500 text-white shadow-sm' 
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            📅 Hoje
                        </button>
                        <button
                            @click="showAllAppointments; viewMode = 'list'"
                            :class="[
                                'px-3 py-1 rounded text-sm font-medium transition-colors',
                                viewMode === 'list' && !showTodayOnly
                                    ? 'bg-white text-gray-900 shadow-sm' 
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            📋 Lista
                        </button>
                        <button
                            @click="showAllAppointments; viewMode = 'calendar'"
                            :class="[
                                'px-3 py-1 rounded text-sm font-medium transition-colors',
                                viewMode === 'calendar' && !showTodayOnly
                                    ? 'bg-white text-gray-900 shadow-sm' 
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            📅 Calendário
                        </button>
                    </div>
                    <PrimaryButton @click="openCreateModal">
                        + Novo Agendamento
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="text-2xl text-blue-600">📅</div>
                                <div class="ml-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.today }}</div>
                                    <div class="text-sm text-gray-600">Hoje</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="text-2xl text-green-600">✅</div>
                                <div class="ml-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.confirmed }}</div>
                                    <div class="text-sm text-gray-600">Confirmados</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="text-2xl text-purple-600">📊</div>
                                <div class="ml-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.thisWeek }}</div>
                                    <div class="text-sm text-gray-600">Esta Semana</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="text-2xl text-yellow-600">💰</div>
                                <div class="ml-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ formatPrice(stats.revenue) }}</div>
                                    <div class="text-sm text-gray-600">Faturamento</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Filtros -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <TextInput
                                    v-model="searchTerm"
                                    placeholder="Buscar por cliente, serviço ou telefone..."
                                    class="w-full"
                                />
                            </div>
                            <div class="w-full md:w-48">
                                <select 
                                    v-model="selectedStatus"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">Todos os status</option>
                                    <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                        {{ status.label }}
                                    </option>
                                </select>
                            </div>
                            <div v-if="viewMode === 'calendar'" class="w-full md:w-48">
                                <input 
                                    v-model="selectedDate"
                                    type="date"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                />
                            </div>
                            <div class="text-sm text-gray-600 flex items-center">
                                {{ filteredAppointments.length }} agendamento(s)
                            </div>
                        </div>
                    </div>

                    <!-- Vista de Lista -->
                    <div v-if="viewMode === 'list'" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Serviço
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data/Hora
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Valor
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Desconto
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="appointment in filteredAppointments" :key="appointment.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ appointment.client_name }}</div>
                                        <div class="text-sm text-gray-500">{{ appointment.client_phone }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ appointment.service_name }}</div>
                                        <div class="text-sm text-gray-500">{{ appointment.service_duration }}min</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ formatDate(appointment.date) }}</div>
                                        <div class="text-sm text-gray-500">{{ formatTime(appointment.time) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select
                                            :value="appointment.status"
                                            @change="updateStatus(appointment, $event.target.value)"
                                            :class="[
                                                'text-xs font-medium rounded-full px-2.5 py-0.5 border-0 focus:ring-2',
                                                getStatusColor(appointment.status) === 'blue' ? 'bg-blue-100 text-blue-800 focus:ring-blue-500' :
                                                getStatusColor(appointment.status) === 'green' ? 'bg-green-100 text-green-800 focus:ring-green-500' :
                                                getStatusColor(appointment.status) === 'yellow' ? 'bg-yellow-100 text-yellow-800 focus:ring-yellow-500' :
                                                getStatusColor(appointment.status) === 'red' ? 'bg-red-100 text-red-800 focus:ring-red-500' :
                                                'bg-gray-100 text-gray-800 focus:ring-gray-500'
                                            ]"
                                        >
                                            <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                                {{ status.label }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div v-if="appointment.discount_amount > 0">
                                            <div class="text-gray-500 line-through text-xs">{{ formatPrice(appointment.original_price || appointment.service_price) }}</div>
                                            <div class="font-medium text-green-600">{{ formatPrice(appointment.final_price || appointment.service_price) }}</div>
                                        </div>
                                        <div v-else class="font-medium">
                                            {{ formatPrice(appointment.service_price) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div v-if="appointment.discount_amount > 0" class="text-green-600">
                                            <div class="font-medium">-{{ formatPrice(appointment.discount_amount) }}</div>
                                            <div class="text-xs text-gray-500">{{ appointment.discount_type === 'loyalty_points' ? 'Fidelidade' : 'Desconto' }}</div>
                                        </div>
                                        <div v-else class="text-gray-400">-</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <EditButton
                                                @click="openEditModal(appointment)"
                                                size="xs"
                                            />
                                            <DeleteButton
                                                @click="openDeleteModal(appointment)"
                                                size="xs"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Estado vazio -->
                        <div v-if="filteredAppointments.length === 0" class="text-center py-12">
                            <div class="text-gray-500">
                                <div class="text-4xl mb-4">📅</div>
                                <div class="text-lg font-medium">Nenhum agendamento encontrado</div>
                                <div class="text-sm">Comece criando seu primeiro agendamento</div>
                            </div>
                        </div>
                    </div>

                    <!-- Vista de Calendário -->
                    <div v-if="viewMode === 'calendar'" class="p-6">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Agendamentos para {{ formatDate(selectedDate) }}
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div v-for="appointment in todayAppointments" :key="appointment.id" 
                                     class="bg-gray-50 rounded-lg p-4 border-l-4"
                                     :class="{
                                         'border-blue-500': appointment.status === 'agendado',
                                         'border-green-500': appointment.status === 'confirmado',
                                         'border-yellow-500': appointment.status === 'em_andamento',
                                         'border-gray-500': appointment.status === 'concluido',
                                         'border-red-500': appointment.status === 'cancelado'
                                     }"
                                >
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="text-lg font-semibold text-gray-900">{{ appointment.time }}</div>
                                        <span :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getStatusColor(appointment.status) === 'blue' ? 'bg-blue-100 text-blue-800' :
                                            getStatusColor(appointment.status) === 'green' ? 'bg-green-100 text-green-800' :
                                            getStatusColor(appointment.status) === 'yellow' ? 'bg-yellow-100 text-yellow-800' :
                                            getStatusColor(appointment.status) === 'red' ? 'bg-red-100 text-red-800' :
                                            'bg-gray-100 text-gray-800'
                                        ]">
                                            {{ getStatusLabel(appointment.status) }}
                                        </span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">{{ appointment.client_name }}</div>
                                    <div class="text-sm text-gray-600">{{ appointment.service_name }}</div>
                                    <div class="text-sm text-gray-500">{{ appointment.service_duration }}min - {{ formatPrice(appointment.service_price) }}</div>
                                    <div v-if="appointment.notes" class="text-xs text-gray-500 mt-2">{{ appointment.notes }}</div>
                                    
                                    <div class="flex justify-end space-x-2 mt-3">
                                        <div class="flex space-x-1">
                                            <EditButton
                                                @click="openEditModal(appointment)"
                                                size="xs"
                                            />
                                            <DeleteButton
                                                @click="openDeleteModal(appointment)"
                                                size="xs"
                                            />
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Estado vazio para o dia -->
                                <div v-if="todayAppointments.length === 0" class="col-span-full text-center py-8">
                                    <div class="text-gray-500">
                                        <div class="text-3xl mb-2">📅</div>
                                        <div class="text-sm">Nenhum agendamento para este dia</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Criação -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Novo Agendamento</h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                            <select 
                                v-model="newAppointment.client_id"
                                @change="onClientChange"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Selecione um cliente</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.name }}
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Serviço</label>
                            <select 
                                v-model="newAppointment.service_id"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Selecione um serviço</option>
                                <option v-for="service in services" :key="service.id" :value="service.id">
                                    {{ service.name }} - {{ formatPrice(service.price) }} ({{ service.duration }}min)
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                            <input 
                                v-model="newAppointment.date"
                                type="date"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horário</label>
                            <select 
                                v-model="newAppointment.time"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Selecione um horário</option>
                                <option v-for="time in timeSlots" :key="time" :value="time" 
                                        :disabled="!isTimeSlotAvailable(newAppointment.date, time)">
                                    {{ time }} {{ !isTimeSlotAvailable(newAppointment.date, time) ? '(Ocupado)' : '' }}
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea 
                            v-model="newAppointment.notes"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="3"
                            placeholder="Observações sobre o agendamento..."
                        ></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <SecondaryButton @click="showCreateModal = false">
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton @click="createAppointment">
                        Agendar
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal de Edição -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Editar Agendamento</h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                            <select 
                                v-model="editAppointment.client_id"
                                @change="onEditClientChange"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Selecione um cliente</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.name }}
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Serviço</label>
                            <select 
                                v-model="editAppointment.service_id"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Selecione um serviço</option>
                                <option v-for="service in services" :key="service.id" :value="service.id">
                                    {{ service.name }} - {{ formatPrice(service.price) }} ({{ service.duration }}min)
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                            <input 
                                v-model="editAppointment.date"
                                type="date"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Horário</label>
                            <select 
                                v-model="editAppointment.time"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Selecione um horário</option>
                                <option v-for="time in timeSlots" :key="time" :value="time">
                                    {{ time }}
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select 
                                v-model="editAppointment.status"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option v-for="status in statusOptions" :key="status.value" :value="status.value">
                                    {{ status.label }}
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea 
                            v-model="editAppointment.notes"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="3"
                            placeholder="Observações sobre o agendamento..."
                        ></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <SecondaryButton @click="showEditModal = false">
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton @click="updateAppointment">
                        Atualizar
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal de Confirmação de Exclusão -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar Exclusão</h3>
                
                <p class="text-sm text-gray-600 mb-6">
                    Tem certeza que deseja excluir o agendamento de <strong>{{ selectedAppointment?.client_name }}</strong> 
                    para <strong>{{ selectedAppointment?.service_name }}</strong> em 
                    <strong>{{ selectedAppointment ? formatDate(selectedAppointment.date) : '' }}</strong> às 
                    <strong>{{ selectedAppointment?.time }}</strong>?
                </p>
                
                <div class="flex justify-end space-x-3">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancelar
                    </SecondaryButton>
                    <DangerButton @click="deleteAppointment">
                        Excluir
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>