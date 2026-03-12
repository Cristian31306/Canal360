<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    riesgo: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';
    return new Intl.DateTimeFormat('es-CO', { year: 'numeric', month: 'long', day: 'numeric' }).format(date);
};
</script>

<template>

    <Head title="Detalle del Riesgo" />

    <AuthenticatedLayout>

        <template #header>
            <div class="flex items-center gap-4 w-full">

                <Link :href="route('riesgos.index')" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>

                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">

                    <h2 class="text-2xl font-bold text-gray-900 border-l-4 border-blue-600 pl-3 dark:text-gray-100">
                        {{ riesgo.tipo_riesgo }}
                    </h2>

                    <Link :href="route('riesgos.edit', riesgo.id)"
                        class="inline-flex items-center rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500">
                        Editar Riesgo
                    </Link>

                </div>
            </div>
        </template>

        <div class="max-w-6xl mx-auto py-6 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- COLUMNA IZQUIERDA -->

                <div class="md:col-span-1 space-y-6">

                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800 overflow-hidden">

                        <div class="p-6 text-center">

                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ riesgo.identificador || 'Sin identificador' }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ riesgo.tipo_riesgo }}
                            </p>

                            <span v-if="riesgo.es_nad"
                                class="inline-block mt-3 text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-1 rounded">
                                NAD {{ riesgo.numero_nad }}
                            </span>

                        </div>

                        <div class="border-t border-gray-100 p-6 text-sm">

                            <div class="flex justify-between py-2">
                                <span class="text-gray-500">Actualizado</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ formatDate(riesgo.updated_at) }}
                                </span>
                            </div>

                        </div>

                    </div>


                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800 overflow-hidden">

                        <div class="p-6">

                            <h3 class="text-base font-semibold mb-4 text-gray-900 dark:text-white">
                                Descripción
                            </h3>

                            <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">
                                {{ riesgo.descripcion || 'Sin observaciones registradas.' }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- COLUMNA DERECHA -->

                <div class="md:col-span-2 space-y-6">


                    <!-- CLIENTES -->

                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800 overflow-hidden">

                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">

                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Clientes Asociados
                            </h3>

                        </div>

                        <div class="p-6 space-y-4">

                            <div v-for="cliente in riesgo.clientes" :key="cliente.id"
                                class="flex justify-between items-center border-b pb-3 last:border-none">

                                <div>

                                    <Link :href="route('clientes.show', cliente.id)"
                                        class="font-semibold text-gray-900 dark:text-white">
                                        {{ cliente.nombre_razon_social }}
                                    </Link>

                                    <p class="text-xs text-gray-500">
                                        {{ cliente.tipo_documento }} {{ cliente.numero_documento }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- POLIZAS -->

                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800 overflow-hidden">

                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">

                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Pólizas Asociadas
                            </h3>

                        </div>

                        <div class="p-6 space-y-4">

                            <div v-if="riesgo.polizas?.length" v-for="poliza in riesgo.polizas" :key="poliza.id"
                                class="flex justify-between items-center border-b pb-3 last:border-none">

                                <div>

                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        {{ poliza.numero_poliza }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        {{ poliza.aseguradora?.nombre }}
                                    </p>

                                </div>

                                <span :class="poliza.estado === 'Activa'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'" class="text-xs px-2 py-1 rounded font-semibold">

                                    {{ poliza.estado }}

                                </span>

                            </div>

                            <div v-else class="text-center text-sm text-gray-400 py-6">
                                No hay pólizas asociadas
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </AuthenticatedLayout>
</template>