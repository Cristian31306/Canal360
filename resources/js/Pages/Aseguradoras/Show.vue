<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    aseguradora: Object,
    ramosCount: Array,
    statsAnuales: Array,
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

        <div class="max-w-5xl mx-auto py-6 space-y-8">
            
            <!-- INFORMACIÓN PRINCIPAL -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-hidden">
                <div class="px-4 py-6 sm:px-8 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
                    <div class="flex items-center gap-6">
                        <div class="h-24 w-24 flex-shrink-0">
                            <div v-if="aseguradora.logo_url" class="h-24 w-24 rounded-2xl bg-white border border-gray-200 flex items-center justify-center p-2 overflow-hidden shadow-md dark:border-gray-700">
                                <img :src="aseguradora.logo_url" alt="" class="h-full w-full object-contain" />
                            </div>
                            <div v-else class="h-24 w-24 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-bold text-4xl shadow-md border border-indigo-700">
                                {{ getInitials(aseguradora.nombre) }}
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ aseguradora.nombre }}</h1>
                            <div class="mt-2 flex flex-wrap gap-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 font-bold">
                                    <span class="text-[10px] uppercase tracking-widest text-gray-400">NIT:</span> {{ aseguradora.nit }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700">
                    <!-- Sección de Contactos -->
                    <div class="px-4 py-8 sm:px-8">
                        <h3 class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-6">Contactos Directos</h3>
                        
                        <div v-if="aseguradora.contactos && aseguradora.contactos.length > 0" class="space-y-4">
                            <div v-for="contacto in aseguradora.contactos" :key="contacto.id" class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-900/30 transition-colors group">
                                <div class="h-10 w-10 flex-none rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold ring-1 ring-indigo-200 dark:ring-indigo-700/50 group-hover:scale-110 transition-transform">
                                    {{ getInitials(contacto.nombre) }}
                                </div>
                                <div class="flex-auto min-w-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ contacto.nombre }}</p>
                                    <p class="text-[10px] font-black uppercase text-emerald-600 tracking-tighter">{{ contacto.rol }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a v-if="contacto.email" :href="'mailto:' + contacto.email" class="p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Enviar correo">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </a>
                                    <a v-if="contacto.telefono" :href="'tel:' + contacto.telefono" class="p-2 text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors" title="Llamar">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-sm text-gray-500 italic">Sin contactos registrados.</p>
                    </div>

                    <!-- Sección de Ramos Configuradores -->
                    <div class="px-4 py-8 sm:px-8 bg-gray-50/30 dark:bg-gray-900/10">
                        <h3 class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-6">Ramos Autorizados</h3>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="ramo in aseguradora.ramos" :key="ramo.id" class="inline-flex items-center rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-400">
                                {{ ramo.nombre }}
                            </span>
                            <p v-if="!aseguradora.ramos.length" class="text-sm text-gray-500 italic">No se han configurado ramos específicos.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ESTADÍSTICAS Y PRODUCCIÓN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Producción por Año -->
                <div class="bg-white shadow-xl shadow-gray-200/50 ring-1 ring-gray-900/5 rounded-2xl overflow-hidden dark:bg-gray-800 dark:ring-gray-700 dark:shadow-none">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Producción Histórica</h4>
                        <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <div class="p-0">
                        <div v-if="statsAnuales && statsAnuales.length > 0" class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <div v-for="stat in statsAnuales" :key="stat.anio" class="flex justify-between items-center p-4 hover:bg-gray-50/80 dark:hover:bg-gray-900/20 transition-colors">
                                <span class="text-lg font-black text-gray-900 dark:text-white">{{ stat.anio }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm font-bold text-gray-400">Pólizas:</span>
                                    <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg font-black text-sm shadow-sm">{{ stat.total }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="p-12 text-center">
                            <p class="text-sm text-gray-500 uppercase font-bold tracking-widest">Sin producción aún</p>
                        </div>
                    </div>
                </div>

                <!-- Distribución por Ramos -->
                <div class="bg-white shadow-xl shadow-gray-200/50 ring-1 ring-gray-900/5 rounded-2xl overflow-hidden dark:bg-gray-800 dark:ring-gray-700 dark:shadow-none">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Cartera por Ramos</h4>
                        <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                    </div>
                    <div class="p-0">
                        <div v-if="ramosCount && ramosCount.length > 0" class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <div v-for="ramo in ramosCount" :key="ramo.nombre" class="flex justify-between items-center p-4 hover:bg-gray-50/80 dark:hover:bg-gray-900/20 transition-colors">
                                <span class="text-sm font-black text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ ramo.nombre }}</span>
                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-md font-black text-[10px] uppercase shadow-sm">{{ ramo.total }} pólizas</span>
                            </div>
                        </div>
                        <div v-else class="p-12 text-center">
                            <p class="text-sm text-gray-500 uppercase font-bold tracking-widest">Sin pólizas emitidas</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
