<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Balancete from '@/Components/Balancete.vue';
</script>

<template>
    <AppLayout title="Balancete">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Balancete
            </h2>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Balancete />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
