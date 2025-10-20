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

// Função para carregar clientes (base + localStorage)
const loadClients = () => {
    const baseClients = [
        {
            id: 1,
            name: 'Ana Silva',
            email: 'ana.silva@email.com',
            phone: '(11) 99999-9999',
            birth_date: '1985-03-15',
            created_at: '2024-01-15',
            last_appointment: '2024-01-20'
        },
        {
            id: 2,
            name: 'Maria Santos',
            email: 'maria.santos@email.com',
            phone: '(11) 88888-8888',
            birth_date: '1990-07-22',
            created_at: '2024-01-10',
            last_appointment: '2024-01-18'
        },
        {
            id: 3,
            name: 'João Costa',
            email: 'joao.costa@email.com',
            phone: '(11) 77777-7777',
            birth_date: '1988-12-03',
            created_at: '2024-01-05',
            last_appointment: '2024-01-19'
        },
        {
            id: 4,
            name: 'Carla Oliveira',
            email: 'carla.oliveira@email.com',
            phone: '(11) 66666-6666',
            birth_date: '1992-05-18',
            created_at: '2024-01-12',
            last_appointment: null
        }
    ];
    
    // Carregar clientes adicionais do localStorage
    const additionalClients = JSON.parse(localStorage.getItem('additional_clients') || '[]');
    
    return [...baseClients, ...additionalClients];
};

// Dados simulados para demonstração + dados persistidos
const clients = ref(loadClients());

const searchTerm = ref('');
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedClient = ref(null);

const newClient = ref({
    name: '',
    email: '',
    phone: '',
    birth_date: ''
});

const editClient = ref({
    id: null,
    name: '',
    email: '',
    phone: '',
    birth_date: ''
});

// Filtrar clientes baseado na busca
const filteredClients = computed(() => {
    if (!searchTerm.value) return clients.value;
    
    return clients.value.filter(client => 
        client.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        client.email.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
        client.phone.includes(searchTerm.value)
    );
});

// Funções CRUD
const openCreateModal = () => {
    newClient.value = {
        name: '',
        email: '',
        phone: '',
        birth_date: ''
    };
    showCreateModal.value = true;
};

const createClient = () => {
    const client = {
        id: Date.now(), // Usar timestamp para garantir ID único
        ...newClient.value,
        created_at: new Date().toISOString().split('T')[0],
        last_appointment: null
    };
    
    clients.value.push(client);
    
    // Salvar no localStorage para persistir os dados
    const additionalClients = clients.value.filter(c => c.id > 4); // Apenas novos clientes
    localStorage.setItem('additional_clients', JSON.stringify(additionalClients));
    
    showCreateModal.value = false;
    
    // Em um app real, aqui faria a requisição para o backend
    // router.post('/clients', newClient.value);
};

const openEditModal = (client) => {
    editClient.value = { ...client };
    showEditModal.value = true;
};

const updateClient = () => {
    const index = clients.value.findIndex(c => c.id === editClient.value.id);
    if (index !== -1) {
        clients.value[index] = { ...editClient.value };
        
        // Atualizar localStorage se for um cliente adicional
        if (editClient.value.id > 4) {
            const additionalClients = clients.value.filter(c => c.id > 4);
            localStorage.setItem('additional_clients', JSON.stringify(additionalClients));
        }
    }
    showEditModal.value = false;
    
    // Em um app real, aqui faria a requisição para o backend
    // router.put(`/clients/${editClient.value.id}`, editClient.value);
};

const openDeleteModal = (client) => {
    selectedClient.value = client;
    showDeleteModal.value = true;
};

const deleteClient = () => {
    const index = clients.value.findIndex(c => c.id === selectedClient.value.id);
    if (index !== -1) {
        clients.value.splice(index, 1);
        
        // Atualizar localStorage se for um cliente adicional
        if (selectedClient.value.id > 4) {
            const additionalClients = clients.value.filter(c => c.id > 4);
            localStorage.setItem('additional_clients', JSON.stringify(additionalClients));
        }
    }
    showDeleteModal.value = false;
    selectedClient.value = null;
    
    // Em um app real, aqui faria a requisição para o backend
    // router.delete(`/clients/${selectedClient.value.id}`);
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('pt-BR');
};
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Clientes
                </h2>
                <PrimaryButton @click="openCreateModal">
                    + Novo Cliente
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Barra de busca -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="flex-1 max-w-md">
                                <TextInput
                                    v-model="searchTerm"
                                    placeholder="Buscar clientes..."
                                    class="w-full"
                                />
                            </div>
                            <div class="ml-4 text-sm text-gray-600">
                                {{ filteredClients.length }} cliente(s) encontrado(s)
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de clientes -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nome
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contato
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data de Nascimento
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cadastrado em
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Último Agendamento
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="client in filteredClients" :key="client.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ client.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ client.email }}</div>
                                        <div class="text-sm text-gray-500">{{ client.phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(client.birth_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(client.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(client.last_appointment) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <EditButton
                                                @click="openEditModal(client)"
                                                size="xs"
                                            />
                                            <DeleteButton
                                                @click="openDeleteModal(client)"
                                                size="xs"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Estado vazio -->
                        <div v-if="filteredClients.length === 0" class="text-center py-12">
                            <div class="text-gray-500">
                                <div class="text-4xl mb-4">👥</div>
                                <div class="text-lg font-medium">Nenhum cliente encontrado</div>
                                <div class="text-sm">Comece cadastrando seu primeiro cliente</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Criação -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Novo Cliente</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        <TextInput v-model="newClient.name" class="w-full" placeholder="Nome completo" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <TextInput v-model="newClient.email" type="email" class="w-full" placeholder="email@exemplo.com" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <TextInput v-model="newClient.phone" class="w-full" placeholder="(11) 99999-9999" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                        <TextInput v-model="newClient.birth_date" type="date" class="w-full" />
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <SecondaryButton @click="showCreateModal = false">
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton @click="createClient">
                        Salvar
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Modal de Edição -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Editar Cliente</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        <TextInput v-model="editClient.name" class="w-full" placeholder="Nome completo" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <TextInput v-model="editClient.email" type="email" class="w-full" placeholder="email@exemplo.com" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <TextInput v-model="editClient.phone" class="w-full" placeholder="(11) 99999-9999" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
                        <TextInput v-model="editClient.birth_date" type="date" class="w-full" />
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <SecondaryButton @click="showEditModal = false">
                        Cancelar
                    </SecondaryButton>
                    <PrimaryButton @click="updateClient">
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
                    Tem certeza que deseja excluir o cliente <strong>{{ selectedClient?.name }}</strong>? 
                    Esta ação não pode ser desfeita.
                </p>
                
                <div class="flex justify-end space-x-3">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancelar
                    </SecondaryButton>
                    <DangerButton @click="deleteClient">
                        Excluir
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>