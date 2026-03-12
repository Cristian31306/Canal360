<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    aseguradora: Object,
});

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n.charAt(0)).slice(0, 2).join('').toUpperCase();
};
</script>

<template>
    <Head :title="'Detalles: ' + aseguradora.nombre" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 w-full">
                <Link :href="route('aseguradoras.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors flex-shrink-0" title="Volver al directorio">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4 overflow-hidden">
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-indigo-600 pl-3 dark:text-gray-100 flex items-center gap-3">
                        <span class="truncate">{{ aseguradora.nombre }}</span>
                    </h2>
                    <Link
                        :href="route('aseguradoras.edit', aseguradora.id)"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Editar Aseguradora
                    </Link>
                </div>
            </div>
        </template>

        <div class="max-w-5xl mx-auto py-6 space-y-6">
            
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-hidden">
                <div class="px-4 py-6 sm:px-8 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-6">
                        <div class="h-20 w-20 flex-shrink-0">
                            <div v-if="aseguradora.logo_url" class="h-20 w-20 rounded-xl bg-white border border-gray-200 flex items-center justify-center p-1 overflow-hidden shadow-sm dark:border-gray-700">
                                <img :src="aseguradora.logo_url" alt="" class="h-full w-full object-contain" />
                            </div>
                            <div v-else class="h-20 w-20 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-3xl shadow-sm border border-indigo-200 dark:bg-indigo-900/40 dark:border-indigo-800">
                                {{ getInitials(aseguradora.nombre) }}
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ aseguradora.nombre }}</h1>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <span class="font-medium text-gray-700 dark:text-gray-300">NIT:</span> {{ aseguradora.nit }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-4 py-6 sm:px-8">
                    <h3 class="text-lg font-semibold leading-7 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-6">Contactos Especializados</h3>
                    
                    <div v-if="aseguradora.contactos && aseguradora.contactos.length > 0" class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-900/50 dark:ring-gray-700">
                        <ul role="list" class="divide-y divide-gray-100 dark:divide-gray-800/60">
                            <li v-for="contacto in aseguradora.contactos" :key="contacto.id" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-x-6 py-5 px-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <div class="flex min-w-0 gap-x-4 items-center">
                                    <div class="h-12 w-12 flex-none rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-semibold ring-1 ring-inset ring-indigo-600/20 text-lg shadow-sm">
                                        {{ getInitials(contacto.nombre) }}
                                    </div>
                                    <div class="min-w-0 flex-auto">
                                        <p class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">{{ contacto.nombre }}</p>
                                        <div class="mt-1 flex items-center gap-2">
                                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-900/30 dark:text-green-400 dark:ring-green-500/20 shadow-sm">{{ contacto.rol }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0 flex flex-col sm:items-end space-y-2">
                                    <a v-if="contacto.email" :href="'mailto:' + contacto.email" class="text-sm leading-6 text-gray-600 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors flex items-center gap-2 bg-gray-50 dark:bg-gray-800/80 px-3 py-1.5 rounded-md ring-1 ring-gray-200 dark:ring-gray-700 w-full sm:w-auto overflow-hidden">
                                        <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                        <span class="truncate">{{ contacto.email }}</span>
                                    </a>
                                    <a v-if="contacto.telefono" :href="'tel:' + contacto.telefono" class="text-sm leading-6 text-gray-600 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors flex items-center gap-2 bg-gray-50 dark:bg-gray-800/80 px-3 py-1.5 rounded-md ring-1 ring-gray-200 dark:ring-gray-700 w-full sm:w-auto">
                                        <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        {{ contacto.telefono }}
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                    <div v-else class="text-center py-12 bg-gray-50 dark:bg-gray-900 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Sin contactos</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Esta aseguradora no tiene especialistas registrados.</p>
                        <div class="mt-6">
                            <Link :href="route('aseguradoras.edit', aseguradora.id)" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-indigo-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-indigo-400 dark:ring-gray-700 dark:hover:bg-gray-700">
                                Agregar Contactos
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Cards decorativas proximo modulo (Ramos/Polizas) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 p-6 flex flex-col justify-center items-center text-center opacity-60">
                    <svg class="h-10 w-10 text-gray-400 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">Estadísticas de Pólizas</h4>
                    <p class="text-xs text-gray-500 mt-1">Próximamente verás cuántas pólizas tienes con esta compañía.</p>
                </div>
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 p-6 flex flex-col justify-center items-center text-center opacity-60">
                    <svg class="h-10 w-10 text-gray-400 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">Ramos Configurados</h4>
                    <p class="text-xs text-gray-500 mt-1">Próximamente listaremos los ramos que puedes comercializar aquí.</p>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
