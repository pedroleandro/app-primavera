<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { format } from 'date-fns';
import api from './../api.js'

const rankingProdutos = ref([]);
const filiais = ref([{ codigoFilial: 'Todas', filial: 'Todas' }]);
const filialSelecionada = ref('Todas');

const hoje = new Date();
const dataInicial = ref(format(new Date(hoje.getFullYear(), hoje.getMonth(), 1), 'yyyy-MM-dd'));
const dataFinal = ref(format(new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0), 'yyyy-MM-dd'));

const carregarFiliais = () => {
    api.get('/dashboard/filiais')
        .then(response => {
            filiais.value = [{ codigoFilial: 'Todas', filial: 'Todas' }, ...response.data];
        })
        .catch(error => {
            console.error('Erro ao carregar filiais', error);
        });
};

const carregarRanking = () => {
    api.get('/dashboard/ranking-produtos-completo', {
        params: {
            filial: filialSelecionada.value,
            dataInicial: dataInicial.value,
            dataFinal: dataFinal.value
        }
    })
        .then(response => {
            rankingProdutos.value = response.data.map((produto, index) => ({
                posicao: index + 1,
                nome: produto.produto,
                total: parseFloat(produto.totalVendido) || 0,
                quantidade: produto.totalPedidos
            }));
        })
        .catch(error => {
            console.error('Erro ao carregar o ranking de produtos', error);
        });
};

onMounted(() => {
    carregarFiliais();
    carregarRanking();
});

const formatNumber = (value) => {
    return new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">

        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-6 text-center">
            🏆 Ranking de Produtos - Mês Atual
        </h2>

        <!-- Filtros -->
        <div class="flex flex-col md:flex-row justify-center items-center gap-4 mb-4">
            <div>
                <label class="font-semibold text-gray-700 dark:text-gray-300 mr-2">Filial:</label>
                <select v-model="filialSelecionada" @change="carregarRanking"
                        class="px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option v-for="filial in filiais" :key="filial.codigoFilial" :value="filial.filial">
                        {{ filial.codigoFilial }} - {{ filial.filial }}
                    </option>
                </select>
            </div>

            <div>
                <label class="font-semibold text-gray-700 dark:text-gray-300 mr-2">Data Inicial:</label>
                <input type="date" v-model="dataInicial" @change="carregarRanking"
                       class="px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>

            <div>
                <label class="font-semibold text-gray-700 dark:text-gray-300 mr-2">Data Final:</label>
                <input type="date" v-model="dataFinal" @change="carregarRanking"
                       class="px-4 py-2 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-left border-collapse">
                <thead>
                <tr class="border-b bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    <th class="px-4 py-3 text-center">#</th>
                    <th class="px-4 py-3">Produto</th>
                    <th class="px-4 py-3">Faturamento</th>
                    <th class="px-4 py-3 text-center">Quantidade Vendida</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="produto in rankingProdutos" :key="produto.posicao"
                    class="border-b transition-all hover:bg-gray-100 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 font-bold text-center text-gray-900 dark:text-gray-200">
                        {{ produto.posicao }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">
                        {{ produto.nome }}
                    </td>
                    <td class="px-4 py-3 text-green-500 font-semibold">
                        R$ {{ produto.total.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">
                        {{ produto.quantidade.toLocaleString('pt-BR') }}
                    </td>
                </tr>
                </tbody>
            </table>
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

tr:hover {
    transform: scale(1.02);
    transition: all 0.2s ease-in-out;
}
</style>
