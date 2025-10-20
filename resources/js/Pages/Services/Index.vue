<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import EditButton from '@/Components/EditButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';

// Dados simulados para demonstração
const services = ref([
    {
        id: 1,
        name: 'Corte Masculino',
        description: 'Corte de cabelo masculino tradicional',
        duration: 30,
        price: 25.00,
        category: 'Cabelo',
        active: true,
        created_at: '2024-01-15'
    },
    {
        id: 2,
        name: 'Barba Completa',
        description: 'Aparar e modelar barba com navalha',
        duration: 20,
        price: 15.00,
        category: 'Barba',
        active: true,
        created_at: '2024-01-15'
    },
    {
        id: 3,
        name: 'Corte + Barba',
        description: 'Pacote completo: corte de cabelo + barba',
        duration: 45,
        price: 35.00,
        category: 'Pacote',
        active: true,
        created_at: '2024-01-15'
    },
    {
        id: 4,
        name: 'Hidratação Capilar',
        description: 'Tratamento hidratante para cabelos ressecados',
        duration: 60,
        price: 45.00,
        category: 'Tratamento',
        active: true,
        created_at: '2024-01-12'
    },
    {
        id: 5,
        name: 'Sobrancelha',
        description: 'Design e limpeza de sobrancelhas',
        duration: 15,
        price: 12.00,
        category: 'Estética',
        active: true,
        created_at: '2024-01-10'
    }
]);

const categories = ref([
    'Cabelo',
    'Barba',
    'Pacote',
    'Tratamento',
    'Estética',
    'Outros'
]);

const searchTerm = ref('');
const selectedCategory = ref('');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedService = ref(null);

const newService = ref({
    name: '',
    description: '',
    duration: '',
    price: '',
    category: '',
    active: true
});

const editService = ref({
    id: null,
    name: '',
    description: '',
    duration: '',
    price: '',
    category: '',
    active: true
});

// Filtrar serviços baseado na busca e categoria
const filteredServices = computed(() => {
    let filtered = services.value;
    
    if (searchTerm.value) {
        filtered = filtered.filter(service => 
            service.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            service.description.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            service.category.toLowerCase().includes(searchTerm.value.toLowerCase())
        );
    }
    
    if (selectedCategory.value) {
        filtered = filtered.filter(service => service.category === selectedCategory.value);
    }
    
    return filtered;
});

// Estatísticas
const stats = computed(() => {
    const total = services.value.length;
    const active = services.value.filter(s => s.active).length;
    const avgPrice = services.value.reduce((sum, s) => sum + s.price, 0) / total;
    const avgDuration = services.value.reduce((sum, s) => sum + s.duration, 0) / total;
    
    return {
        total,
        active,
        inactive: total - active,
        avgPrice: avgPrice.toFixed(2),
        avgDuration: Math.round(avgDuration)
    };
});

// Funções CRUD
const openCreateModal = () => {
    newService.value = {
        name: '',
        description: '',
        duration: '',
        price: '',
        category: '',
        active: true
    };
    showCreateModal.value = true;
};

const createService = () => {
    const service = {
        id: services.value.length + 1,
        ...newService.value,
        duration: parseInt(newService.value.duration),
        price: parseFloat(newService.value.price),
        created_at: new Date().toISOString().split('T')[0]
    };
    
    services.value.push(service);
    showCreateModal.value = false;
    
    // Em um app real, aqui faria a requisição para o backend
    // router.post('/services', newService.value);
};

const openEditModal = (service) => {
    editService.value = { ...service };
    showEditModal.value = true;
};

const updateService = () => {
    const index = services.value.findIndex(s => s.id === editService.value.id);
    if (index !== -1) {
        editService.value.duration = parseInt(editService.value.duration);
        editService.value.price = parseFloat(editService.value.price);
        services.value[index] = { ...editService.value };
    }
    showEditModal.value = false;
    
    // Em um app real, aqui faria a requisição para o backend
    // router.put(`/services/${editService.value.id}`, editService.value);
};

const openDeleteModal = (service) => {
    selectedService.value = service;
    showDeleteModal.value = true;
};

const deleteService = () => {
    const index = services.value.findIndex(s => s.id === selectedService.value.id);
    if (index !== -1) {
        services.value.splice(index, 1);
    }
    showDeleteModal.value = false;
    selectedService.value = null;
    
    // Em um app real, aqui faria a requisição para o backend
    // router.delete(`/services/${selectedService.value.id}`);
};

const toggleServiceStatus = (service) => {
    service.active = !service.active;
    
    // Em um app real, aqui faria a requisição para o backend
    // router.patch(`/services/${service.id}/toggle-status`);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(price);
};

const formatDuration = (duration) => {
    if (duration >= 60) {
        const hours = Math.floor(duration / 60);
        const minutes = duration % 60;
        return minutes > 0 ? `${hours}h ${minutes}min` : `${hours}h`;
    }
    return `${duration}min`;
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};
</script>

<template>
    <Head title="Serviços" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Serviços
                </h2>
                <PrimaryButton @click="openCreateModal">
                    + Novo Serviço
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Estatísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="text-2xl text-blue-600">📋</div>
                                <div class="ml-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.total }}</div>
                                    <div class="text-sm text-gray-600">Total de Serviços</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="text-2xl text-green-600">✅</div>
                                <div class="ml-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.active }}</div>
                                    <div class="text-sm text-gray-600">Serviços Ativos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="text-2xl text-yellow-600">💰</div>
                                <div class="ml-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ formatPrice(stats.avgPrice) }}</div>
                                    <div class="text-sm text-gray-600">Preço Médio</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="text-2xl text-purple-600">⏱️</div>
                                <div class="ml-4">
                                    <div class="text-2xl font-bold text-gray-900">{{ stats.avgDuration }}min</div>
                                    <div class="text-sm text-gray-600">Duração Média</div>
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
                                    placeholder="Buscar serviços..."
                                    class="w-full"
                                />
                            </div>
                            <div class="w-full md:w-48">
                                <select 
                                    v-model="selectedCategory"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                >
                                    <option value="">Todas as categorias</option>
                                    <option v-for="category in categories" :key="category" :value="category">
                                        {{ category }}
                                    </option>
                                </select>
                            </div>
                            <div class="text-sm text-gray-600 flex items-center">
                                {{ filteredServices.length }} serviço(s) encontrado(s)
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de serviços -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Serviço
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Categoria
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Duração
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Preço
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="service in filteredServices" :key="service.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ service.name }}</div>
                                        <div class="text-sm text-gray-500">{{ service.description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ service.category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDuration(service.duration) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ formatPrice(service.price) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button
                                            @click="toggleServiceStatus(service)"
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                service.active 
                                                    ? 'bg-green-100 text-green-800 hover:bg-green-200' 
                                                    : 'bg-red-100 text-red-800 hover:bg-red-200'
                                            ]"
                                        >
                                            {{ service.active ? 'Ativo' : 'Inativo' }}
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <EditButton
                                                @click="openEditModal(service)"
                                                size="xs"
                                            />
                                            <DeleteButton
                                                @click="openDeleteModal(service)"
                                                size="xs"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Estado vazio -->
                        <div v-if="filteredServices.length === 0" class="text-center py-12">
                            <div class="text-gray-500">
                                <div class="text-4xl mb-4">✂️</div>
                                <div class="text-lg font-medium">Nenhum serviço encontrado</div>
                                <div class="text-sm">Comece cadastrando seu primeiro serviço</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Criação -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Novo Serviço</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Serviço</label>
                        <TextInput v-model="newService.name" class="w-full" placeholder="Ex: Corte Masculino" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea 
                            v-model="newService.description"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="3"
                            placeholder="Descreva o serviço..."
                        ></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duração (minutos)</label>
                            <TextInput v-model="newService.duration" type="number" class="w-full" placeholder="30" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                            <TextInput v-model="newService.price" type="number" step="0.01" class="w-full" placeholder="25.00" />
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <select 
                            v-model="newService.category"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">Selecione uma categoria</option>
                            <option v-for="category in categories" :key="category" :value="category">
                                {{ category }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="flex items-center">
                        <input 
                            v-model="newService.active"
                            type="checkbox" 
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        >
                        <label class="ml-2 text-sm text-gray-700">Serviço ativo</label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <SecondaryButton @click="showCreateModal = false">
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton @click="createService">
                        Salvar
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal de Edição -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Editar Serviço</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Serviço</label>
                        <TextInput v-model="editService.name" class="w-full" placeholder="Ex: Corte Masculino" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea 
                            v-model="editService.description"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            rows="3"
                            placeholder="Descreva o serviço..."
                        ></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duração (minutos)</label>
                            <TextInput v-model="editService.duration" type="number" class="w-full" placeholder="30" />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Preço (R$)</label>
                            <TextInput v-model="editService.price" type="number" step="0.01" class="w-full" placeholder="25.00" />
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                        <select 
                            v-model="editService.category"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="">Selecione uma categoria</option>
                            <option v-for="category in categories" :key="category" :value="category">
                                {{ category }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="flex items-center">
                        <input 
                            v-model="editService.active"
                            type="checkbox" 
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        >
                        <label class="ml-2 text-sm text-gray-700">Serviço ativo</label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <SecondaryButton @click="showEditModal = false">
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton @click="updateService">
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
                    Tem certeza que deseja excluir o serviço <strong>{{ selectedService?.name }}</strong>? 
                    Esta ação não pode ser desfeita e pode afetar agendamentos existentes.
                </p>
                
                <div class="flex justify-end space-x-3">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancelar
                    </SecondaryButton>
                    <DangerButton @click="deleteService">
                        Excluir
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>