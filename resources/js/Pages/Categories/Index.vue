<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Categorias de Serviços
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total de Categorias</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ categories.length }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Categorias Ativas</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ activeCategories }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Serviços Vinculados</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ totalServices }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Mais Popular</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ mostPopularCategory }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="flex-1">
                <input
                  v-model="searchTerm"
                  type="text"
                  placeholder="Buscar categorias..."
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div class="flex gap-2">
                <select
                  v-model="statusFilter"
                  class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">Todos os Status</option>
                  <option value="ativa">Ativas</option>
                  <option value="inativa">Inativas</option>
                </select>
                <PrimaryButton @click="showCreateModal = true">
                  Nova Categoria
                </PrimaryButton>
              </div>
            </div>
          </div>
        </div>

        <!-- Lista de Categorias -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Categoria
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Descrição
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Serviços
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Ações
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="category in filteredCategories" :key="category.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="w-3 h-3 rounded-full mr-3" :style="{ backgroundColor: category.color }"></div>
                      <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ category.description }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-900">{{ category.services_count }} serviços</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                        category.status === 'ativa'
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ category.status === 'ativa' ? 'Ativa' : 'Inativa' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <EditButton
                        @click="editCategory(category)"
                        size="xs"
                      />
                      <button
                        @click="toggleStatus(category)"
                        :class="[
                          'px-3 py-1 text-xs font-medium rounded-md transition-colors',
                          category.status === 'ativa'
                            ? 'bg-red-100 text-red-700 hover:bg-red-200'
                            : 'bg-green-100 text-green-700 hover:bg-green-200'
                        ]"
                      >
                        {{ category.status === 'ativa' ? 'Desativar' : 'Ativar' }}
                      </button>
                      <DeleteButton
                        @click="deleteCategory(category)"
                        size="xs"
                      />
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Criar/Editar Categoria -->
    <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ showCreateModal ? 'Nova Categoria' : 'Editar Categoria' }}
          </h3>
          <form @submit.prevent="showCreateModal ? createCategory() : updateCategory()">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
              <input
                v-model="categoryForm.name"
                type="text"
                required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Ex: Manicure e Pedicure"
              />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
              <textarea
                v-model="categoryForm.description"
                rows="3"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Descrição da categoria..."
              ></textarea>
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Cor</label>
              <div class="flex items-center space-x-3">
                <input
                  v-model="categoryForm.color"
                  type="color"
                  class="w-12 h-10 border border-gray-300 rounded cursor-pointer"
                />
                <input
                  v-model="categoryForm.color"
                  type="text"
                  class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                  placeholder="#3B82F6"
                />
              </div>
            </div>
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select
                v-model="categoryForm.status"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              >
                <option value="ativa">Ativa</option>
                <option value="inativa">Inativa</option>
              </select>
            </div>
            <div class="flex justify-end space-x-3">
              <SecondaryButton type="button" @click="closeModal()">
                Cancelar
              </SecondaryButton>
              <PrimaryButton type="submit">
                {{ showCreateModal ? 'Criar' : 'Salvar' }}
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Confirmar Exclusão -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar Exclusão</h3>
          <p class="text-sm text-gray-500 mb-6">
            Tem certeza que deseja excluir a categoria "{{ categoryToDelete?.name }}"?
            Esta ação não pode ser desfeita.
          </p>
          <div class="flex justify-center space-x-3">
            <SecondaryButton @click="showDeleteModal = false">
              Cancelar
            </SecondaryButton>
            <DeleteButton @click="confirmDelete()">
              Excluir
            </DeleteButton>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import EditButton from '@/Components/EditButton.vue'
import DeleteButton from '@/Components/DeleteButton.vue'

// Estados reativos
const categories = ref([])
const searchTerm = ref('')
const statusFilter = ref('')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const categoryToDelete = ref(null)
const editingCategory = ref(null)

// Formulário
const categoryForm = ref({
  name: '',
  description: '',
  color: '#3B82F6',
  status: 'ativa'
})

// Dados simulados
const mockCategories = [
  {
    id: 1,
    name: 'Corte de Cabelo',
    description: 'Serviços de corte e modelagem capilar',
    color: '#3B82F6',
    status: 'ativa',
    services_count: 8,
    created_at: '2024-01-15'
  },
  {
    id: 2,
    name: 'Manicure',
    description: 'Cuidados com unhas das mãos',
    color: '#EC4899',
    status: 'ativa',
    services_count: 6,
    created_at: '2024-01-16'
  },
  {
    id: 3,
    name: 'Pedicure',
    description: 'Cuidados com unhas dos pés',
    color: '#F472B6',
    status: 'ativa',
    services_count: 5,
    created_at: '2024-01-17'
  },
  {
    id: 4,
    name: 'Tratamentos Faciais',
    description: 'Limpeza de pele e tratamentos estéticos faciais',
    color: '#10B981',
    status: 'ativa',
    services_count: 6,
    created_at: '2024-01-18'
  },
  {
    id: 5,
    name: 'Coloração',
    description: 'Tintura, mechas e coloração capilar',
    color: '#F59E0B',
    status: 'ativa',
    services_count: 10,
    created_at: '2024-01-19'
  },
  {
    id: 6,
    name: 'Massagem',
    description: 'Massagens relaxantes e terapêuticas',
    color: '#8B5CF6',
    status: 'ativa',
    services_count: 4,
    created_at: '2024-01-20'
  },
  {
    id: 7,
    name: 'Depilação',
    description: 'Remoção de pelos corporais',
    color: '#EF4444',
    status: 'inativa',
    services_count: 7,
    created_at: '2024-01-21'
  }
]

// Computadas
const filteredCategories = computed(() => {
  let filtered = categories.value

  if (searchTerm.value) {
    filtered = filtered.filter(category =>
      category.name.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
      category.description.toLowerCase().includes(searchTerm.value.toLowerCase())
    )
  }

  if (statusFilter.value) {
    filtered = filtered.filter(category => category.status === statusFilter.value)
  }

  return filtered
})

const activeCategories = computed(() => {
  return categories.value.filter(category => category.status === 'ativa').length
})

const totalServices = computed(() => {
  return categories.value.reduce((total, category) => total + category.services_count, 0)
})

const mostPopularCategory = computed(() => {
  if (categories.value.length === 0) return 'N/A'
  const popular = categories.value.reduce((prev, current) => 
    prev.services_count > current.services_count ? prev : current
  )
  return popular.name
})

// Métodos
const loadCategories = () => {
  categories.value = mockCategories
}

const createCategory = () => {
  const newCategory = {
    id: Date.now(),
    ...categoryForm.value,
    services_count: 0,
    created_at: new Date().toISOString().split('T')[0]
  }
  
  categories.value.push(newCategory)
  closeModal()
  alert('Categoria criada com sucesso!')
}

const editCategory = (category) => {
  editingCategory.value = category
  categoryForm.value = { ...category }
  showEditModal.value = true
}

const updateCategory = () => {
  const index = categories.value.findIndex(c => c.id === editingCategory.value.id)
  if (index !== -1) {
    categories.value[index] = { ...categories.value[index], ...categoryForm.value }
  }
  closeModal()
  alert('Categoria atualizada com sucesso!')
}

const deleteCategory = (category) => {
  categoryToDelete.value = category
  showDeleteModal.value = true
}

const confirmDelete = () => {
  const index = categories.value.findIndex(c => c.id === categoryToDelete.value.id)
  if (index !== -1) {
    categories.value.splice(index, 1)
  }
  showDeleteModal.value = false
  categoryToDelete.value = null
  alert('Categoria excluída com sucesso!')
}

const toggleStatus = (category) => {
  category.status = category.status === 'ativa' ? 'inativa' : 'ativa'
  alert(`Categoria ${category.status === 'ativa' ? 'ativada' : 'desativada'} com sucesso!`)
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingCategory.value = null
  categoryForm.value = {
    name: '',
    description: '',
    color: '#3B82F6',
    status: 'ativa'
  }
}

// Lifecycle
onMounted(() => {
  loadCategories()
})
</script>