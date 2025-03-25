<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RankingClientes from '@/Components/RankingClientes.vue';
</script>

<template>
    <AppLayout title="Clientes">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ranking de Clientes
            </h2>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <RankingClientes />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
