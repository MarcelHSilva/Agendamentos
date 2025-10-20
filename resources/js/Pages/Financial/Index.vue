<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { Chart, registerables } from 'chart.js/auto';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import EditButton from '@/Components/EditButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';

Chart.register(...registerables);

// Estados reativos
const activeTab = ref('overview');
const showModal = ref(false);
const modalType = ref('revenue'); // 'revenue' ou 'expense'
const editingItem = ref(null);
const searchTerm = ref('');
const filterPeriod = ref('month');
const filterType = ref('all');
const serviceSearchTerm = ref('');
const clientSearchTerm = ref('');
const showServiceDropdown = ref(false);
const showClientDropdown = ref(false);

// Estados para relatórios
const reportStartDate = ref('');
const reportEndDate = ref('');
const reportPeriod = ref('30');
const reportType = ref('all');
const reportCategory = ref('all');

// Formulário
const form = ref({
    description: '',
    amount: '',
    category: '',
    date: new Date().toISOString().split('T')[0],
    type: 'revenue', // 'revenue' ou 'expense'
    service_id: '',
    service_name: '',
    client_id: '',
    client_name: ''
});

// Dados de serviços (simulados)
const services = ref([
    {
        id: 1,
        name: 'Corte Masculino',
        price: 25.00,
        category: 'Cabelo'
    },
    {
        id: 2,
        name: 'Barba Completa',
        price: 15.00,
        category: 'Barba'
    },
    {
        id: 3,
        name: 'Corte + Barba',
        price: 35.00,
        category: 'Pacote'
    },
    {
        id: 4,
        name: 'Hidratação Capilar',
        price: 45.00,
        category: 'Tratamento'
    },
    {
        id: 5,
        name: 'Sobrancelha',
        price: 12.00,
        category: 'Estética'
    }
]);

// Dados de clientes (simulados)
const clients = ref([
    {
        id: 1,
        name: 'Ana Silva',
        email: 'ana.silva@email.com',
        phone: '(11) 99999-9999'
    },
    {
        id: 2,
        name: 'Maria Santos',
        email: 'maria.santos@email.com',
        phone: '(11) 88888-8888'
    },
    {
        id: 3,
        name: 'João Costa',
        email: 'joao.costa@email.com',
        phone: '(11) 77777-7777'
    },
    {
        id: 4,
        name: 'Carla Oliveira',
        email: 'carla.oliveira@email.com',
        phone: '(11) 66666-6666'
    }
]);

// Computadas para filtros
const filteredServices = computed(() => {
    if (!serviceSearchTerm.value) return services.value;
    return services.value.filter(service => 
        service.name.toLowerCase().includes(serviceSearchTerm.value.toLowerCase())
    );
});

const filteredClients = computed(() => {
    if (!clientSearchTerm.value) return clients.value;
    return clients.value.filter(client => 
        client.name.toLowerCase().includes(clientSearchTerm.value.toLowerCase())
    );
});

// Funções para seleção de serviços e clientes
const selectService = (service) => {
    form.value.service_id = service.id;
    form.value.service_name = service.name;
    form.value.amount = service.price.toString();
    form.value.description = service.name;
    serviceSearchTerm.value = service.name;
    showServiceDropdown.value = false;
};

const selectClient = (client) => {
    form.value.client_id = client.id;
    form.value.client_name = client.name;
    clientSearchTerm.value = client.name;
    showClientDropdown.value = false;
};

const clearServiceSelection = () => {
    form.value.service_id = '';
    form.value.service_name = '';
    serviceSearchTerm.value = '';
};

const clearClientSelection = () => {
    form.value.client_id = '';
    form.value.client_name = '';
    clientSearchTerm.value = '';
};

// Funções para relatórios
const updateReportDates = () => {
    const days = parseInt(reportPeriod.value);
    
    if (reportPeriod.value === 'custom') {
        // Não alterar as datas se for personalizado
        return;
    }
    
    if (reportPeriod.value === 'all') {
        reportStartDate.value = '';
        reportEndDate.value = '';
        return;
    }
    
    // Usar a mesma função de data local já existente
    const today = new Date();
    const startDate = new Date(today);
    startDate.setDate(today.getDate() - days);
    
    // Formatar usando a mesma lógica da função getLocalDateString
    const formatLocalDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };
    
    reportStartDate.value = formatLocalDate(startDate);
    reportEndDate.value = formatLocalDate(today);
};

const updateReports = () => {
    console.log('🔄 Atualizando relatórios...');
    
    // Atualiza as datas apenas se não for período personalizado
    if (reportPeriod.value !== 'custom') {
        updateReportDates();
    }
    
    // Força a reatividade das computadas
    nextTick(() => {
        // Trigger manual para forçar recálculo
        const currentTransactions = [...transactions.value];
        const filteredCount = filteredReportTransactions.value.length;
        const summary = reportSummary.value;
        
        console.log('📊 Relatório atualizado:', {
            periodo: reportPeriod.value,
            dataInicial: reportStartDate.value,
            dataFinal: reportEndDate.value,
            tipo: reportType.value,
            categoria: reportCategory.value,
            transacoesEncontradas: filteredCount,
            totalTransacoes: currentTransactions.length,
            resumo: {
                receitas: summary.revenue,
                despesas: summary.expenses,
                lucro: summary.profit
            }
        });
    });
};

const printReport = () => {
    // Criar uma nova janela para impressão com apenas os dados do relatório
    const printWindow = window.open('', '_blank');
    const reportData = filteredReportTransactions.value;
    const summary = reportSummary.value;
    
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Relatório Financeiro</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    color: #333;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #333;
                    padding-bottom: 10px;
                }
                .summary {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 20px;
                    margin-bottom: 30px;
                }
                .summary-card {
                    border: 1px solid #ddd;
                    padding: 15px;
                    text-align: center;
                    border-radius: 5px;
                }
                .summary-card h3 {
                    margin: 0 0 10px 0;
                    font-size: 14px;
                    color: #666;
                }
                .summary-card .value {
                    font-size: 18px;
                    font-weight: bold;
                }
                .revenue { color: #22c55e; }
                .expense { color: #ef4444; }
                .profit { color: #3b82f6; }
                .count { color: #6b7280; }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f5f5f5;
                    font-weight: bold;
                }
                .text-center { text-align: center; }
                .revenue-row { background-color: #f0fdf4; }
                .expense-row { background-color: #fef2f2; }
                @media print {
                    body { margin: 0; }
                    .summary { grid-template-columns: repeat(2, 1fr); }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Relatório Financeiro</h1>
                <p>Período: ${reportPeriod.value === 'custom' ? 
                    `${reportStartDate.value} até ${reportEndDate.value}` : 
                    `Últimos ${reportPeriod.value} dias`}</p>
                <p>Gerado em: ${new Date().toLocaleDateString('pt-BR')} às ${new Date().toLocaleTimeString('pt-BR')}</p>
            </div>
            
            <div class="summary">
                <div class="summary-card">
                    <h3>Total de Receitas</h3>
                    <div class="value revenue">R$ ${summary.revenue.toFixed(2).replace('.', ',')}</div>
                </div>
                <div class="summary-card">
                    <h3>Total de Despesas</h3>
                    <div class="value expense">R$ ${summary.expenses.toFixed(2).replace('.', ',')}</div>
                </div>
                <div class="summary-card">
                    <h3>Lucro/Prejuízo</h3>
                    <div class="value profit">R$ ${summary.profit.toFixed(2).replace('.', ',')}</div>
                </div>
                <div class="summary-card">
                    <h3>Total de Transações</h3>
                    <div class="value count">${summary.transactionCount}</div>
                </div>
            </div>
            
            ${reportData.length > 0 ? `
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Categoria</th>
                            <th>Tipo</th>
                            <th class="text-center">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${reportData.map(transaction => `
                            <tr class="${transaction.type}-row">
                                <td>${formatDate(transaction.date)}</td>
                                <td>${transaction.description}</td>
                                <td>${transaction.category}</td>
                                <td class="text-center">${transaction.type === 'revenue' ? 'Receita' : 'Despesa'}</td>
                                <td class="text-center">R$ ${transaction.amount.toFixed(2).replace('.', ',')}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            ` : '<p style="text-align: center; margin-top: 40px; color: #666;">Nenhuma transação encontrada para os filtros selecionados.</p>'}
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    
    // Aguardar o carregamento e imprimir
    printWindow.onload = () => {
        printWindow.print();
        printWindow.close();
    };
};

const exportReportCSV = () => {
    const data = filteredReportTransactions.value;
    const headers = ['Data', 'Descrição', 'Categoria', 'Tipo', 'Valor'];
    
    let csv = headers.join(',') + '\n';
    
    data.forEach(transaction => {
        const row = [
            formatDate(transaction.date),
            `"${transaction.description}"`,
            `"${transaction.category}"`,
            transaction.type === 'revenue' ? 'Receita' : 'Despesa',
            transaction.amount.toFixed(2).replace('.', ',')
        ];
        csv += row.join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `relatorio_financeiro_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Inicializar datas do relatório
const initializeReportDates = () => {
    updateReportDates();
};

// Dados reais do banco de dados
const transactions = ref([]);
const loading = ref(false);
const summary = ref({ revenue: 0, expenses: 0, profit: 0 });

// Função para carregar transações da API
const loadTransactions = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams();
        
        // Adicionar apenas filtros da aba de transações (não os filtros de relatório)
        if (filterType.value !== 'all') {
            params.append('type', filterType.value);
        }
        
        if (searchTerm.value) {
            params.append('search', searchTerm.value);
        }
        
        const response = await fetch(`/api/transactions?${params.toString()}`);
        const data = await response.json();
        
        transactions.value = data.map(transaction => ({
            ...transaction,
            amount: parseFloat(transaction.amount)
        }));
        
        console.log('✅ Transações carregadas:', transactions.value.length);
    } catch (error) {
        console.error('❌ Erro ao carregar transações:', error);
    } finally {
        loading.value = false;
    }
};

// Função para carregar transações com filtros de relatório
const loadReportTransactions = async () => {
    try {
        const params = new URLSearchParams();
        
        // Adicionar filtros de relatório
        if (reportStartDate.value && reportEndDate.value) {
            params.append('start_date', reportStartDate.value);
            params.append('end_date', reportEndDate.value);
        }
        
        if (reportType.value !== 'all') {
            params.append('type', reportType.value);
        }
        
        const response = await fetch(`/api/transactions?${params.toString()}`);
        const data = await response.json();
        
        // Atualizar apenas para relatórios, não a lista principal
        console.log('✅ Transações de relatório carregadas:', data.length);
        return data;
    } catch (error) {
        console.error('❌ Erro ao carregar transações de relatório:', error);
        return [];
    }
};

// Função para carregar resumo financeiro geral (sem filtros de data)
const loadSummary = async () => {
    try {
        const response = await fetch('/api/financial/summary');
        const data = await response.json();
        
        summary.value = {
            revenue: parseFloat(data.revenue || 0),
            expenses: parseFloat(data.expenses || 0),
            profit: parseFloat(data.profit || 0)
        };
        
        console.log('✅ Resumo geral carregado:', summary.value);
    } catch (error) {
        console.error('❌ Erro ao carregar resumo:', error);
    }
};

// Função para carregar resumo de relatórios (com filtros de data)
const loadReportSummary = async () => {
    try {
        const params = new URLSearchParams();
        
        if (reportStartDate.value && reportEndDate.value) {
            params.append('start_date', reportStartDate.value);
            params.append('end_date', reportEndDate.value);
        }
        
        const response = await fetch(`/api/financial/summary?${params.toString()}`);
        const data = await response.json();
        
        console.log('✅ Resumo de relatório carregado:', data);
        return data;
    } catch (error) {
        console.error('❌ Erro ao carregar resumo de relatório:', error);
        return { revenue: 0, expenses: 0, profit: 0 };
    }
};

const categories = ref({
    revenue: ['Serviços de Cabelo', 'Serviços de Unha', 'Serviços de Pele', 'Produtos', 'Outros'],
    expense: ['Materiais', 'Contas', 'Salários', 'Marketing', 'Manutenção', 'Outros']
});

// Computadas
const filteredTransactions = computed(() => {
    let filtered = transactions.value;
    
    if (searchTerm.value) {
        filtered = filtered.filter(t => 
            t.description.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
            t.category.toLowerCase().includes(searchTerm.value.toLowerCase())
        );
    }
    
    if (filterType.value !== 'all') {
        filtered = filtered.filter(t => t.type === filterType.value);
    }
    
    return filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
});

// Computadas para relatórios
const filteredReportTransactions = computed(() => {
    let filtered = [...transactions.value]; // Cria uma cópia para evitar mutação
    
    // Filtrar por período
    if (reportPeriod.value === 'custom') {
        // Modo personalizado - usar datas específicas
        if (reportStartDate.value && reportEndDate.value) {
            filtered = filtered.filter(t => {
                const transactionDate = new Date(t.date + 'T00:00:00');
                const startDate = new Date(reportStartDate.value + 'T00:00:00');
                const endDate = new Date(reportEndDate.value + 'T23:59:59');
                return transactionDate >= startDate && transactionDate <= endDate;
            });
        }
    } else if (reportPeriod.value !== 'all' && reportPeriod.value !== 'custom') {
        // Modo predefinido - usar número de dias
        const days = parseInt(reportPeriod.value);
        if (!isNaN(days)) {
            const today = new Date();
            const startDate = new Date(today);
            startDate.setDate(today.getDate() - days);
            
            // Formatar datas para comparação usando a mesma lógica local
            const formatLocalDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            const startDateStr = formatLocalDate(startDate);
            
            filtered = filtered.filter(t => {
                const transactionDateStr = t.date.split('T')[0]; // Pegar apenas a parte da data
                return transactionDateStr >= startDateStr;
            });
        }
    }
    
    // Filtrar por tipo
    if (reportType.value && reportType.value !== 'all') {
        filtered = filtered.filter(t => t.type === reportType.value);
    }
    
    // Filtrar por categoria
    if (reportCategory.value && reportCategory.value !== 'all') {
        filtered = filtered.filter(t => t.category === reportCategory.value);
    }
    
    return filtered.sort((a, b) => new Date(b.date) - new Date(a.date));
});

const reportSummary = computed(() => {
    const filtered = filteredReportTransactions.value;
    const revenue = filtered.filter(t => t.type === 'revenue').reduce((sum, t) => sum + t.amount, 0);
    const expenses = filtered.filter(t => t.type === 'expense').reduce((sum, t) => sum + t.amount, 0);
    
    return {
        revenue,
        expenses,
        profit: revenue - expenses,
        transactionCount: filtered.length
    };
});

const totalRevenue = computed(() => {
    return summary.value.revenue;
});

const totalExpenses = computed(() => {
    return summary.value.expenses;
});

const netProfit = computed(() => {
    return summary.value.profit;
});

const monthlyData = computed(() => {
    const months = {};
    transactions.value.forEach(t => {
        const month = t.date.substring(0, 7); // YYYY-MM
        if (!months[month]) {
            months[month] = { revenue: 0, expenses: 0 };
        }
        months[month][t.type === 'revenue' ? 'revenue' : 'expenses'] += t.amount;
    });
    return months;
});

// Função para obter data local no formato YYYY-MM-DD
const getLocalDateString = () => {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

// Métodos
const openModal = (type, item = null) => {
    modalType.value = type;
    editingItem.value = item;
    
    if (item) {
        form.value = { ...item };
    } else {
        form.value = {
            description: '',
            amount: '',
            category: '',
            date: getLocalDateString(),
            type: type
        };
    }
    
    showModal.value = true;
};

const resetForm = () => {
    form.value = {
        description: '',
        amount: '',
        category: '',
        date: getLocalDateString(),
        type: modalType.value,
        service_id: '',
        service_name: '',
        client_id: '',
        client_name: ''
    };
    serviceSearchTerm.value = '';
    clientSearchTerm.value = '';
    showServiceDropdown.value = false;
    showClientDropdown.value = false;
};

const closeModal = () => {
    showModal.value = false;
    editingItem.value = null;
    resetForm();
};

const saveTransaction = async () => {
    console.log('🚀 FUNÇÃO SAVETRANSACTION CHAMADA!');
    console.log('🚀 Iniciando saveTransaction...');
    console.log('📝 Dados do formulário:', form.value);
    console.log('📝 Modal type:', modalType.value);
    console.log('📝 Show modal:', showModal.value);
    console.log('📝 Validação dos campos:');
    console.log('  - Description:', form.value.description);
    console.log('  - Amount:', form.value.amount);
    console.log('  - Category:', form.value.category);
    console.log('  - Date:', form.value.date);
    console.log('  - Type:', form.value.type);
    
    try {
        const transactionData = {
            description: form.value.description,
            amount: parseFloat(form.value.amount),
            category: form.value.category,
            date: form.value.date,
            type: form.value.type,
            client_id: form.value.client_id || null,
            notes: form.value.notes || null
        };
        
        console.log('📊 Dados da transação preparados:', transactionData);
        console.log('📦 Dados da transação:', transactionData);
        
        if (editingItem.value) {
            console.log('✏️ Editando transação existente:', editingItem.value.id);
            // Editar transação existente
            const response = await fetch(`/api/transactions/${editingItem.value.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(transactionData)
            });
            
            console.log('📡 Resposta da API (PUT):', response.status, response.statusText);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Erro na resposta PUT:', errorText);
                throw new Error('Erro ao atualizar transação');
            }
            
            console.log('✅ Transação atualizada com sucesso');
        } else {
            console.log('➕ Criando nova transação');
            // Criar nova transação
            console.log('🔍 Verificando CSRF token:', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
            
            const response = await fetch('/api/transactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(transactionData)
            });
            
            console.log('📡 Resposta da API (POST):', response.status, response.statusText);
            console.log('📡 Headers da resposta:', [...response.headers.entries()]);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Erro na resposta POST:', errorText);
                throw new Error('Erro ao criar transação');
            }
            
            const responseData = await response.json();
            console.log('✅ Transação criada com sucesso:', responseData);
        }
        
        // Recarregar dados
        console.log('🔄 Recarregando dados...');
        await loadTransactions();
        await loadSummary();
        console.log('✅ Dados recarregados com sucesso');
        
        console.log('🚪 Fechando modal...');
        closeModal();
        console.log('✅ Modal fechado');
    } catch (error) {
        console.error('❌ Erro ao salvar transação:', error);
        alert('Erro ao salvar transação. Tente novamente.');
    }
};

const deleteTransaction = async (id) => {
    if (confirm('Tem certeza que deseja excluir esta transação?')) {
        try {
            const response = await fetch(`/api/transactions/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (!response.ok) {
                throw new Error('Erro ao excluir transação');
            }
            
            console.log('✅ Transação excluída com sucesso');
            
            // Recarregar dados
            await loadTransactions();
            await loadSummary();
        } catch (error) {
            console.error('❌ Erro ao excluir transação:', error);
            alert('Erro ao excluir transação. Tente novamente.');
        }
    }
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR');
};

// Gráficos
const initCharts = () => {
    // Gráfico de fluxo de caixa
    const cashFlowCtx = document.getElementById('cashFlowChart');
    if (cashFlowCtx) {
        const months = Object.keys(monthlyData.value).sort();
        const revenueData = months.map(m => monthlyData.value[m].revenue);
        const expenseData = months.map(m => monthlyData.value[m].expenses);
        
        new Chart(cashFlowCtx, {
            type: 'line',
            data: {
                labels: months.map(m => {
                    const [year, month] = m.split('-');
                    return new Date(year, month - 1).toLocaleDateString('pt-BR', { month: 'short', year: 'numeric' });
                }),
                datasets: [
                    {
                        label: 'Receitas',
                        data: revenueData,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    },
                    {
                        label: 'Despesas',
                        data: expenseData,
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Gráfico de categorias
    const categoriesCtx = document.getElementById('categoriesChart');
    if (categoriesCtx) {
        const categoryData = {};
        transactions.value.forEach(t => {
            if (t.type === 'revenue') {
                categoryData[t.category] = (categoryData[t.category] || 0) + t.amount;
            }
        });
        
        new Chart(categoriesCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(categoryData),
                datasets: [{
                    data: Object.values(categoryData),
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                        '#06B6D4'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
};

// Fechar dropdowns ao clicar fora
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        showServiceDropdown.value = false;
        showClientDropdown.value = false;
    }
};

// Watchers para reatividade dos relatórios
watch([reportPeriod, reportType, reportCategory, reportStartDate, reportEndDate], () => {
    updateReports();
    loadReportSummary();
}, { deep: true });

// Watcher para filtros de transações
watch([searchTerm, filterType], () => {
    loadTransactions();
}, { debounce: 300 });

onMounted(async () => {
    // Carregar dados iniciais
    initializeReportDates();
    await loadTransactions();
    await loadSummary();
    
    // Inicializar gráficos após carregar dados
    setTimeout(initCharts, 500);
    
    document.addEventListener('click', handleClickOutside);
    updateReports();
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <Head title="Gestão Financeira" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestão Financeira
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Cards de Resumo -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 text-green-500">💰</div>
                            <div>
                                <div class="text-2xl font-bold text-green-600">{{ formatCurrency(totalRevenue) }}</div>
                                <div class="text-sm text-gray-600">Total de Receitas</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4 text-red-500">💸</div>
                            <div>
                                <div class="text-2xl font-bold text-red-600">{{ formatCurrency(totalExpenses) }}</div>
                                <div class="text-sm text-gray-600">Total de Despesas</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="text-3xl mr-4" :class="netProfit >= 0 ? 'text-green-500' : 'text-red-500'">📊</div>
                            <div>
                                <div class="text-2xl font-bold" :class="netProfit >= 0 ? 'text-green-600' : 'text-red-600'">
                                    {{ formatCurrency(netProfit) }}
                                </div>
                                <div class="text-sm text-gray-600">Lucro Líquido</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navegação por Abas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 px-6">
                            <button @click="activeTab = 'overview'" 
                                    :class="activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Visão Geral
                            </button>
                            <button @click="activeTab = 'transactions'" 
                                    :class="activeTab === 'transactions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Transações
                            </button>
                            <button @click="activeTab = 'reports'" 
                                    :class="activeTab === 'reports' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Relatórios
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Conteúdo das Abas -->
                <div v-if="activeTab === 'overview'">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Gráfico de Fluxo de Caixa -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Fluxo de Caixa</h3>
                            <canvas id="cashFlowChart" width="400" height="200"></canvas>
                        </div>

                        <!-- Gráfico de Categorias -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Receitas por Categoria</h3>
                            <canvas id="categoriesChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div v-if="activeTab === 'transactions'">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <!-- Cabeçalho com Filtros e Ações -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                                    <input v-model="searchTerm" type="text" placeholder="Buscar transações..."
                                           class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                    <select v-model="filterType" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                                        <option value="all">Todos os tipos</option>
                                        <option value="revenue">Receitas</option>
                                        <option value="expense">Despesas</option>
                                    </select>
                                </div>
                                <div class="flex space-x-2">
                                    <PrimaryButton @click="openModal('revenue')" class="bg-green-600 hover:bg-green-700">
                                        + Receita
                                    </PrimaryButton>
                                    <DangerButton @click="openModal('expense')">
                                        + Despesa
                                    </DangerButton>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de Transações -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="transaction in filteredTransactions" :key="transaction.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(transaction.date) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ transaction.description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ transaction.category }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="transaction.type === 'revenue' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ transaction.type === 'revenue' ? 'Receita' : 'Despesa' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" 
                                            :class="transaction.type === 'revenue' ? 'text-green-600' : 'text-red-600'">
                                            {{ formatCurrency(transaction.amount) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <EditButton @click="openModal(transaction.type, transaction)" />
                                                <DeleteButton @click="deleteTransaction(transaction.id)" />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div v-if="activeTab === 'reports'">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <!-- Filtros do Relatório -->
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Relatórios Financeiros</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                                    <select v-model="reportPeriod" @change="updateReports" 
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                        <option value="7">Últimos 7 dias</option>
                                        <option value="30">Últimos 30 dias</option>
                                        <option value="90">Últimos 90 dias</option>
                                        <option value="365">Último ano</option>
                                        <option value="custom">Personalizado</option>
                                        <option value="all">Todos os períodos</option>
                                    </select>
                                </div>
                                
                                <div v-if="reportPeriod === 'custom'">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Inicial</label>
                                    <input v-model="reportStartDate" type="date" @change="updateReports"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                </div>
                                
                                <div v-if="reportPeriod === 'custom'">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Final</label>
                                    <input v-model="reportEndDate" type="date" @change="updateReports"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                    <select v-model="reportType" @change="updateReports"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                        <option value="all">Todos</option>
                                        <option value="revenue">Receitas</option>
                                        <option value="expense">Despesas</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                                    <select v-model="reportCategory" @change="updateReports"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                        <option value="all">Todas</option>
                                        <optgroup label="Receitas">
                                            <option v-for="category in categories.revenue" :key="category" :value="category">
                                                {{ category }}
                                            </option>
                                        </optgroup>
                                        <optgroup label="Despesas">
                                            <option v-for="category in categories.expense" :key="category" :value="category">
                                                {{ category }}
                                            </option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <PrimaryButton @click="updateReports">
                                    🔄 Atualizar
                                </PrimaryButton>
                                
                                <div class="flex space-x-2">
                                    <SecondaryButton @click="printReport">
                                        🖨️ Imprimir
                                    </SecondaryButton>
                                    <PrimaryButton @click="exportReportCSV" class="bg-green-600 hover:bg-green-700">
                                        📊 Exportar CSV
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resumo do Relatório -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <div class="text-sm text-green-600 font-medium">Total de Receitas</div>
                                    <div class="text-2xl font-bold text-green-700">{{ formatCurrency(reportSummary.revenue) }}</div>
                                </div>
                                
                                <div class="bg-red-50 p-4 rounded-lg">
                                    <div class="text-sm text-red-600 font-medium">Total de Despesas</div>
                                    <div class="text-2xl font-bold text-red-700">{{ formatCurrency(reportSummary.expenses) }}</div>
                                </div>
                                
                                <div class="p-4 rounded-lg" :class="reportSummary.profit >= 0 ? 'bg-blue-50' : 'bg-orange-50'">
                                    <div class="text-sm font-medium" :class="reportSummary.profit >= 0 ? 'text-blue-600' : 'text-orange-600'">Lucro/Prejuízo</div>
                                    <div class="text-2xl font-bold" :class="reportSummary.profit >= 0 ? 'text-blue-700' : 'text-orange-700'">
                                        {{ formatCurrency(reportSummary.profit) }}
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-sm text-gray-600 font-medium">Total de Transações</div>
                                    <div class="text-2xl font-bold text-gray-700">{{ reportSummary.transactionCount }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tabela de Transações do Relatório -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="transaction in filteredReportTransactions" :key="transaction.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDate(transaction.date) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ transaction.description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ transaction.category }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="transaction.type === 'revenue' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                                {{ transaction.type === 'revenue' ? 'Receita' : 'Despesa' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" 
                                            :class="transaction.type === 'revenue' ? 'text-green-600' : 'text-red-600'">
                                            {{ formatCurrency(transaction.amount) }}
                                        </td>
                                    </tr>
                                    <tr v-if="filteredReportTransactions.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Nenhuma transação encontrada para os filtros selecionados.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Transação -->
        <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ editingItem ? 'Editar' : 'Nova' }} {{ modalType === 'revenue' ? 'Receita' : 'Despesa' }}
                    </h3>
                    
                    <form @submit.prevent="saveTransaction" @submit="console.log('📋 EVENTO SUBMIT DISPARADO!')" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                            <input v-model="form.description" type="text" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                                   placeholder="Digite a descrição...">
                        </div>
                        
                        <!-- Campo de Serviço (apenas para receitas) -->
                        <div v-if="modalType === 'revenue'" class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Serviço</label>
                            <div class="relative">
                                <input v-model="serviceSearchTerm" 
                                       @input="showServiceDropdown = true"
                                       @focus="showServiceDropdown = true"
                                       type="text" 
                                       placeholder="Digite ou selecione um serviço..."
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm pr-8">
                                <button v-if="form.service_id" 
                                        @click="clearServiceSelection"
                                        type="button"
                                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    ✕
                                </button>
                            </div>
                            
                            <!-- Dropdown de Serviços -->
                            <div v-if="showServiceDropdown && filteredServices.length > 0" 
                                 class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                <div v-for="service in filteredServices" 
                                     :key="service.id"
                                     @click="selectService(service)"
                                     class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <div class="font-medium text-sm">{{ service.name }}</div>
                                    <div class="text-xs text-gray-500">{{ service.category }} - R$ {{ service.price.toFixed(2) }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Campo de Cliente (apenas para receitas) -->
                        <div v-if="modalType === 'revenue'" class="relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                            <div class="relative">
                                <input v-model="clientSearchTerm" 
                                       @input="showClientDropdown = true"
                                       @focus="showClientDropdown = true"
                                       type="text" 
                                       placeholder="Digite ou selecione um cliente..."
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm pr-8">
                                <button v-if="form.client_id" 
                                        @click="clearClientSelection"
                                        type="button"
                                        class="absolute right-2 top-2 text-gray-400 hover:text-gray-600">
                                    ✕
                                </button>
                            </div>
                            
                            <!-- Dropdown de Clientes -->
                            <div v-if="showClientDropdown && filteredClients.length > 0" 
                                 class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-y-auto">
                                <div v-for="client in filteredClients" 
                                     :key="client.id"
                                     @click="selectClient(client)"
                                     class="px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <div class="font-medium text-sm">{{ client.name }}</div>
                                    <div class="text-xs text-gray-500">{{ client.email }} - {{ client.phone }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor</label>
                            <input v-model="form.amount" type="number" step="0.01" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                                   placeholder="0,00">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                            <select v-model="form.category" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">Selecione uma categoria</option>
                                <option v-for="category in categories[modalType]" :key="category" :value="category">
                                    {{ category }}
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                            <input v-model="form.date" type="date" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-4">
                            <SecondaryButton type="button" @click="closeModal">
                                Cancelar
                            </SecondaryButton>
                            <button type="button" @click="saveTransaction" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">
                                Teste Direto
                            </button>
                            <PrimaryButton type="submit" 
                                    @click="console.log('🔘 BOTÃO SALVAR CLICADO!', form.value)"
                                    :class="modalType === 'revenue' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'">
                                {{ editingItem ? 'Atualizar' : 'Salvar' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>