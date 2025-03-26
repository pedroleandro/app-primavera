<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import api from './../api.js'

const primeiroDiaMes = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10);
const ultimoDiaMes = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toISOString().slice(0, 10);

const rankingClientes = ref([]);
const filiais = ref([{ codigoFilial: 'Todas', filial: 'Todas' }]);
const filialSelecionada = ref('Todas');
const dataInicial = ref(primeiroDiaMes);
const dataFinal = ref(ultimoDiaMes);

const carregarFiliais = async () => {
    try {
        const response = await api.get('/dashboard/filiais');
        filiais.value = [{ codigoFilial: 'Todas', filial: 'Todas' }, ...response.data];
    } catch (error) {
        console.error('Erro ao carregar filiais', error);
    }
};

const carregarRanking = async () => {
    if (dataFinal.value < dataInicial.value) {
        alert("A data final não pode ser menor que a data inicial.");
        return;
    }

    try {
        const response = await api.get('/dashboard/ranking-clientes-completo', {
            params: {
                filial: filialSelecionada.value,
                dataInicial: dataInicial.value,
                dataFinal: dataFinal.value
            }
        });

        rankingClientes.value = response.data.map((cliente, index) => ({
            posicao: index + 1,
            nome: cliente.nomeFantasiaCliente,
            total: parseFloat(cliente.totalFaturado) || 0,
            pedidos: cliente.totalPedidos
        }));
    } catch (error) {
        console.error('Erro ao carregar o ranking de clientes', error);
    }
};

watch([filialSelecionada, dataInicial, dataFinal], carregarRanking);

onMounted(() => {
    carregarFiliais();
    carregarRanking();
});
</script>

<template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center">
            🏆 Ranking de Clientes - Mês Atual
        </h2>

        <!-- Filtros -->
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

            <!-- Filtro por Data Inicial -->
            <div>
                <label for="dataInicial" class="font-semibold text-gray-700 dark:text-gray-300 mr-2">Data Inicial:</label>
                <input type="date" id="dataInicial" v-model="dataInicial"
                       class="px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>

            <!-- Filtro por Data Final -->
            <div>
                <label for="dataFinal" class="font-semibold text-gray-700 dark:text-gray-300 mr-2">Data Final:</label>
                <input type="date" id="dataFinal" v-model="dataFinal"
                       class="px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-left border-collapse">
                <thead>
                <tr class="border-b bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <th class="px-4 py-3 text-center">#</th>
                    <th class="px-4 py-3">Cliente</th>
                    <th class="px-4 py-3">Faturamento</th>
                    <th class="px-4 py-3 text-center">Pedidos</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="cliente in rankingClientes" :key="cliente.posicao"
                    class="border-b transition-all hover:bg-gray-100 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 font-bold text-center text-gray-900 dark:text-gray-200">
                        {{ cliente.posicao }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">
                        {{ cliente.nome }}
                    </td>
                    <td class="px-4 py-3 text-green-500 font-semibold">
                        R$ {{ cliente.total.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                        {{ cliente.pedidos }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <footer class="bg-gray-100 dark:bg-gray-900 text-center p-4 mt-8 text-gray-600 dark:text-gray-300 text-sm">
            © {{ new Date().getFullYear() }}
            <a href="https://minhaempresa.com.br" class="text-blue-500 hover:underline">
                Minha Empresa
            </a>. Todos os direitos reservados.
        </footer>
    </div>
</template>

<style scoped>

tr:hover {
    transform: scale(1.02);
    transition: all 0.2s ease-in-out;
}
</style>
