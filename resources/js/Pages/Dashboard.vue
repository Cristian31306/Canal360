<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Array,
});
</script>

<template>
    <Head title="Tablero de Control" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-blue-600 pl-3 dark:text-gray-100">
                Tablero de Control
            </h2>
        </template>

        <div class="space-y-6">
            <!-- Stats grid -->
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="item in stats" :key="item.name" class="relative overflow-hidden rounded-xl bg-white px-4 pb-12 pt-5 shadow-md shadow-gray-200/50 sm:px-6 sm:pt-6 dark:bg-gray-800 dark:shadow-none transition-transform duration-300 hover:scale-[1.02]">
                    <dt>
                        <div class="absolute rounded-lg p-3" :class="item.background">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                            </svg>
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-gray-500 dark:text-gray-400">{{ item.name }}</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ item.stat }}</p>
                    </dd>
                </div>
            </dl>

            <!-- Shortcuts Bar -->
            <div class="mt-8 bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-3 mb-4 dark:text-white dark:border-gray-700">Accesos Rápidos</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                        <Link :href="route('clientes.create')" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-blue-400 transition-colors dark:border-gray-700 dark:hover:bg-gray-700 group block">
                            <div class="mx-auto w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Nuevo Cliente</span>
                        </Link>
                        
                        <Link :href="route('polizas.create')" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-emerald-400 transition-colors dark:border-gray-700 dark:hover:bg-gray-700 group block">
                            <div class="mx-auto w-10 h-10 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Emitir Póliza</span>
                        </Link>

                        <Link :href="route('riesgos.create')" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-orange-400 transition-colors dark:border-gray-700 dark:hover:bg-gray-700 group block">
                            <div class="mx-auto w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Registrar Riesgo</span>
                        </Link>

                        <button class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-rose-400 transition-colors dark:border-gray-700 dark:hover:bg-gray-700 group block w-full">
                            <div class="mx-auto w-10 h-10 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Abono Cartera</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
