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
                        <!-- Icon Background with Safelisted Colors -->
                        <div class="absolute rounded-lg p-3" 
                            :class="{
                                'bg-indigo-500': item.name === 'Total Clientes',
                                'bg-emerald-500': item.name === 'Pólizas Vigentes',
                                'bg-orange-500': item.name === 'Riesgos Registrados',
                                'bg-rose-500': item.name === 'Cartera por Cobrar'
                            }">
                            
                            <!-- Hardcoded SVGs to ensure visibility -->
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <!-- Total Clientes -->
                                <path v-if="item.name === 'Total Clientes'" stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                
                                <!-- Pólizas Vigentes -->
                                <path v-else-if="item.name === 'Pólizas Vigentes'" stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                
                                <!-- Riesgos Registrados -->
                                <path v-else-if="item.name === 'Riesgos Registrados'" stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.74c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                
                                <!-- Cartera por Cobrar -->
                                <path v-else-if="item.name === 'Cartera por Cobrar'" stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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

                        <Link :href="route('cartera.index')" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-rose-400 transition-colors dark:border-gray-700 dark:hover:bg-gray-700 group block">
                            <div class="mx-auto w-10 h-10 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Abono Cartera</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
