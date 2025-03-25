<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RankingProdutos from '@/Components/RankingProdutos.vue';
</script>

<template>
    <AppLayout title="Produtos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ranking de Produtos
            </h2>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <RankingProdutos />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
