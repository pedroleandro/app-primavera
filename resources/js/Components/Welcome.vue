<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

const faturamentoAnual = ref(0);
const faturamentoAtual = ref(0);
const faturamentoMesAnterior = ref(0);

const totalPedidos = ref(0);
const totalPedidosMesAtual = ref(0);
const totalPedidosMesAnterior = ref(0);

const clienteRanking = ref([]);
const produtosRanking = ref([]);

const ultimosPedidos = ref([]);

const lastUpdated = ref('');

const filiais = ref([{ codigoFilial: '', filial: 'Todas' }]);
const filialSelecionada = ref('Todas');

const carregarFiliais = async () => {
    try {
        const response = await axios.get('/api/dashboard/filiais');
        filiais.value = [{ codigoFilial: '99', filial: 'Todas' }, ...response.data];
    } catch (error) {
        console.error('Erro ao carregar filiais', error);
    }
};

const carregarDashboard = () => {
    const filtros = {
        filial: filialSelecionada.value
    };

    axios.get('/api/dashboard/faturamento', { params: filtros })
        .then(response => {
            const data = response.data;
            faturamentoAnual.value = parseFloat(data.faturamentoAnual);
            faturamentoAtual.value = parseFloat(data.faturamentoAtual);
        })
        .catch(error => {
            console.error('Erro ao carregar os dados do Faturamento', error);
        });

    axios.get('/api/dashboard/faturamento-mes-anterior', { params: filtros })
        .then(response => {
            const data = response.data;
            faturamentoMesAnterior.value = parseFloat(data.faturamentoMesAnterior);
        })
        .catch(error => {
            console.error('Erro ao carregar os dados do Faturamento do Mes Anterior', error);
        });

    axios.get('/api/dashboard/pedidos', { params: filtros })
        .then(response => {
            const data = response.data;
            totalPedidos.value = data.totalPedidos;
            totalPedidosMesAtual.value = data.totalPedidosMesAtual;
        })
        .catch(error => {
            console.error('Erro ao carregar os dados dos Pedidos', error);
        });

    axios.get('/api/dashboard/pedidos-mes-anterior', { params: filtros })
        .then(response => {
            const data = response.data;
            totalPedidosMesAnterior.value = data.totalPedidosMesAnterior;
        })
        .catch(error => {
            console.error('Erro ao carregar os dados dos Pedidos do Mes Anterior', error);
        });

    axios.get('/api/dashboard/ranking-clientes', { params: filtros })
        .then(response => {
            clienteRanking.value = response.data.map(cliente => ({
                nomeCliente: cliente.nomeFantasiaCliente,
                totalFaturado: cliente.totalFaturado,
                totalPedidos: cliente.totalPedidos
            }));
        })
        .catch(error => {
            console.error('Erro ao carregar o ranking dos clientes', error);
        });

    axios.get('/api/dashboard/ranking-produtos', { params: filtros })
        .then(response => {
            produtosRanking.value = response.data.map(produto => ({
                nomeProduto: produto.produto,
                totalVendido: produto.totalVendido,
                totalFaturado: produto.totalFaturado
            }));
        })
        .catch(error => {
            console.error('Erro ao carregar o ranking de produtos', error);
        });

    axios.get('/api/dashboard/ultimos-pedidos', { params: filtros })
        .then(response => {
            ultimosPedidos.value = response.data.map(pedido => ({
                cliente: pedido.nomeFantasiaCliente,
                valor: pedido.valorFaturadoPedido
            }));
        })
        .catch(error => {
            console.error('Erro ao carregar os dados dos Últimos Pedidos', error);
        });
};

onMounted(() => {
    carregarFiliais();
    carregarDashboard();

    setTimeout(() => {
        lastUpdated.value = new Date().toLocaleDateString('pt-BR');
    }, 1000);
});

watch(filialSelecionada, () => {
    carregarDashboard();
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const formatNumber = (value) => {
    return new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 }).format(value);
};
</script>


<template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">

        <div class="mb-4 flex flex-wrap justify-center gap-4">
            <!-- Filtro por Filial -->
            <div>
                <label for="filial" class="font-semibold text-gray-700 dark:text-gray-300 mr-2">Filial:</label>
                <select id="filial" v-model="filialSelecionada"
                        class="px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option v-for="filial in filiais" :key="filial.codigoFilial" :value="filial.filial">
                        {{ filial.codigoFilial }} - {{ filial.filial }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Cards do Faturamento -->
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 justify-center">

            <!-- Card do Faturamento Total -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-xl p-6 w-full max-w-sm text-center transform transition-all hover:scale-105">
                <div class="text-green-500 text-4xl font-bold">📈</div>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-white mt-4">Faturamento Total</h2>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ formatCurrency(faturamentoAnual) }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Última atualização: {{ lastUpdated }}
                </p>
            </div>

            <!-- Card do Faturamento do Mês Atual -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-xl p-6 w-full max-w-sm text-center transform transition-all hover:scale-105">
                <div class="text-blue-500 text-4xl font-bold">📆</div>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-white mt-4">Faturamento do Mês</h2>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ formatCurrency(faturamentoAtual) }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Última atualização: {{ lastUpdated }}
                </p>
            </div>

            <!-- Card do Faturamento do Mês Anterior -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-xl p-6 w-full max-w-sm text-center transform transition-all hover:scale-105">
                <div class="text-yellow-500 text-4xl font-bold">📊</div>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-white mt-4">Faturamento do Mês Anterior</h2>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ formatCurrency(faturamentoMesAnterior) }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Última atualização: {{ lastUpdated }}
                </p>
            </div>

            <!-- Card de Pedidos Totais -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-xl p-6 w-full max-w-sm text-center transform transition-all hover:scale-105">
                <div class="text-purple-500 text-4xl font-bold">📦</div>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-white mt-4">Pedidos Totais</h2>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ totalPedidos.toLocaleString() }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Última atualização: {{ lastUpdated }}
                </p>
            </div>

            <!-- Card de Pedidos do Mês Atual -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-xl p-6 w-full max-w-sm text-center transform transition-all hover:scale-105">
                <div class="text-indigo-500 text-4xl font-bold">📅</div>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-white mt-4">Pedidos do Mês</h2>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ totalPedidosMesAtual.toLocaleString() }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Última atualização: {{ lastUpdated }}
                </p>
            </div>

            <!-- Card de Pedidos do Mês Anterior -->
            <div
                class="bg-white dark:bg-gray-900 shadow-lg rounded-xl p-6 w-full max-w-sm text-center transform transition-all hover:scale-105">
                <div class="text-red-500 text-4xl font-bold">📊</div>
                <h2 class="text-lg font-semibold text-gray-700 dark:text-white mt-4">Pedidos do Mês Anterior</h2>
                <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                    {{ totalPedidosMesAnterior.toLocaleString() }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                    Última atualização: {{ lastUpdated }}
                </p>
            </div>
        </div>

        <div>
            <!-- Ranking dos Clientes que Mais Compraram -->
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center space-x-2">
                    <span>🏆 Ranking dos Clientes</span>
                    <span class="text-gray-500 text-lg">(Por Faturamento)</span>
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left">
                        <thead>
                        <tr class="border-b">
                            <th class="px-4 py-2 w-16 text-center">Posição</th>
                            <th class="px-4 py-2 w-1/3">Cliente</th>
                            <th class="px-4 py-2 w-1/3">Total Comprado</th>
                            <th class="px-4 py-2 w-1/3">Quantidade de Pedidos</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(cliente, index) in clienteRanking" :key="index"
                            :class="{'bg-gray-50': index % 2 === 0}">
                            <td class="px-4 py-2 font-semibold text-center">{{ index + 1 }}</td>
                            <td class="px-4 py-2">{{ cliente.nomeCliente }}</td>
                            <td class="px-4 py-2">{{ formatCurrency(cliente.totalFaturado) }}</td>
                            <td class="px-4 py-2">{{ cliente.totalPedidos }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Ranking dos Produtos Mais Vendidos -->
            <div class="p-6 bg-white border-b border-gray-200 mt-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center space-x-2">
                    <span>📊 Ranking dos Produtos</span>
                    <span class="text-gray-500 text-lg">(Valor Total das Vendas)</span>
                </h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto text-left">
                        <thead>
                        <tr class="border-b">
                            <th class="px-4 py-2 w-16 text-center">Posição</th>
                            <th class="px-4 py-2 w-1/3">Produto</th>
                            <th class="px-4 py-2 w-1/3">Valor Total das Vendas</th>
                            <th class="px-4 py-2 w-1/3">Quantidade Vendida</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(produto, index) in produtosRanking" :key="index"
                            :class="{'bg-gray-50': index % 2 === 0}">
                            <td class="px-4 py-2 font-semibold text-center">{{ index + 1 }}</td>
                            <td class="px-4 py-2">{{ produto.nomeProduto }}</td>
                            <td class="px-4 py-2">{{ formatCurrency(produto.totalFaturado) }}</td>
                            <td class="px-4 py-2">{{ formatNumber(produto.totalVendido) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <!-- Timeline de Pedidos Faturados -->
            <div class="p-6 bg-white border-b border-gray-200 mt-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center space-x-2">
                    <span>🚀 Últimos Pedidos Faturados</span>
                </h2>
                <div class="overflow-hidden relative">
                    <div class="flex space-x-6 animate-slideRight">
                        <div v-for="(pedido, index) in [...ultimosPedidos, ...ultimosPedidos]"
                             :key="index"
                             class="flex items-center py-2 space-x-2">

                            <div class="flex items-center w-100 truncate">
                                <span class="mr-1">🚀</span>
                                <span class="truncate">{{ pedido.cliente }}</span>
                            </div>
                            <span class="text-green-500">{{ formatCurrency(pedido.valor) }}</span>
                            <span v-if="index !== ultimosPedidos.length * 2 - 1" class="text-gray-400">◆</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-100 dark:bg-gray-900 text-center p-4 mt-8 text-gray-600 dark:text-gray-300 text-sm">
            © {{ new Date().getFullYear() }} <a href="https://minhaempresa.com.br"
                                                class="text-blue-500 hover:underline">Minha Empresa</a>. Todos os
            direitos reservados.
        </footer>
    </div>
</template>

<style scoped>
/* Animações para as linhas ao carregar */
@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translateY(10px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

table tbody tr {
    animation: fadeIn 0.5s ease-out;
}

table td, table th {
    transition: all 0.3s ease;
}

table td:hover {
    background-color: #f7fafc;
}

table th {
    background-color: #f3f4f6;
}

table tr.bg-gray-50 {
    background-color: #f9fafb;
}

h2 {
    font-family: 'Arial', sans-serif;
    font-weight: bold;
    color: #333;
}

h2 span {
    font-size: 1.25rem;
}

h2 span.text-gray-500 {
    font-size: 1rem;
    color: #6B7280;
    margin-left: 8px;
}

@keyframes slideRight {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
}

/* Certifique-se de que os itens são largos o suficiente para cobrir a tela */
.animate-slideRight {
    display: flex;
    width: max-content;
    animation: slideRight 50s linear infinite;
}

/* Estilo dos pedidos ao lado uns dos outros */
.animate-slideRight .flex {
    display: flex;
    gap: 1rem; /* Adiciona um espaçamento entre os itens */
}

.animate-pulse {
    display: flex;
    flex-direction: column;
    animation: slideRight 30s infinite linear;
}

.animate-pulse .flex {
    animation: slideRight 30s infinite linear;
}
</style>
