<script setup>
import { ref, watch, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    riesgo: Object,
});

const confirmation = ref({
    show: false,
    title: '',
    message: '',
    type: 'danger',
    confirmLabel: 'Confirmar',
    callback: null
});

const page = usePage();

onMounted(() => {
    if (page.props.flash?.error) showAlert('Alerta', page.props.flash.error, 'danger');
    if (page.props.flash?.success) showAlert('Éxito', page.props.flash.success, 'success');
});

watch(() => page.props.flash, (flash) => {
    if (flash?.error) showAlert('Alerta', flash.error, 'danger');
    if (flash?.success) showAlert('Éxito', flash.success, 'success');
}, { deep: true });

const showAlert = (title, message, type = 'danger') => {
    confirmation.value = {
        show: true,
        title,
        message: message.includes('No se puede eliminar') ? `Alerta: ${message}` : message,
        type,
        confirmLabel: 'Entendido',
        callback: null
    };
};

const deleteRiesgo = () => {
    confirmation.value = {
        show: true,
        title: 'Eliminar Riesgo',
        message: '¿Estás seguro de que deseas eliminar este riesgo? Esta acción no se puede deshacer si tiene pólizas vinculadas.',
        type: 'danger',
        confirmLabel: 'Eliminar',
        callback: () => router.delete(route('riesgos.destroy', props.riesgo.id))
    };
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';
    return new Intl.DateTimeFormat('es-CO', { year: 'numeric', month: 'long', day: 'numeric' }).format(date);
};

const goBack = () => window.history.back();
</script>

<template>

    <Head title="Detalle del Riesgo" />

    <AuthenticatedLayout>

        <template #header>
            <div class="flex items-center gap-4 w-full">

                <button @click="goBack" type="button" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </button>

                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">

                    <h2 class="text-2xl font-bold text-gray-900 border-l-4 border-blue-600 pl-3 dark:text-gray-100">
                        {{ riesgo.tipo_riesgo }}
                    </h2>

                    <div class="flex items-center gap-3">
                        <Link :href="route('riesgos.edit', riesgo.id)"
                            class="inline-flex items-center rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500">
                            Editar Riesgo
                        </Link>
                        <button @click="deleteRiesgo"
                            class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-sm font-bold text-red-600 shadow-sm ring-1 ring-inset ring-red-300 hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:ring-red-900/50 transition-all">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar
                        </button>
                    </div>

                </div>
            </div>
        </template>

        <div class="max-w-6xl mx-auto py-4 space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- COLUMNA IZQUIERDA -->

                <div class="md:col-span-1 space-y-6">

                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800 overflow-hidden">

                        <div class="p-4 text-center">

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

                        <div class="border-t border-gray-100 p-4 text-sm">

                            <div class="flex justify-between py-2">
                                <span class="text-gray-500">Actualizado</span>
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ formatDate(riesgo.updated_at) }}
                                </span>
                            </div>

                        </div>

                    </div>


                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800 overflow-hidden">

                        <div class="p-4">

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

                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">

                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Clientes Asociados
                            </h3>

                        </div>

                        <div class="p-4 space-y-4">

                            <div v-for="cliente in riesgo.clientes" :key="cliente.id"
                                class="border-b pb-4 mb-4 last:border-none last:pb-0 last:mb-0">
                                
                                <div class="flex justify-between items-center mb-3">
                                    <div>
                                        <Link :href="route('clientes.show', cliente.id)"
                                            class="font-semibold text-gray-900 dark:text-white hover:text-blue-600 transition-colors">
                                            {{ cliente.nombre_razon_social }}
                                        </Link>
                                        <p class="text-xs text-gray-500">
                                            {{ cliente.tipo_documento }} {{ cliente.numero_documento }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Credenciales ANNA del Cliente -->
                                <div v-if="cliente.anna_credentials && cliente.anna_credentials.length > 0" class="mt-2 space-y-2">
                                    <div v-for="cred in cliente.anna_credentials" :key="cred.id" class="bg-blue-50 dark:bg-blue-900/10 p-2 rounded border border-blue-100 dark:border-blue-900/30 flex justify-between items-center gap-4">
                                        <div class="flex items-center gap-2">
                                            <svg class="h-3 w-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                            </svg>
                                            <span class="text-[10px] font-bold text-blue-700 dark:text-blue-300 uppercase">ANNA:</span>
                                            <span class="text-xs font-mono text-gray-700 dark:text-gray-200">{{ cred.usuario }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase">Pass:</span>
                                            <span class="text-xs font-mono text-gray-700 dark:text-gray-200">{{ cred.password }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>


                    <!-- POLIZAS -->

                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800 overflow-hidden">

                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">

                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Pólizas Asociadas
                            </h3>

                        </div>

                        <div class="p-4 space-y-4">

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

                            <div v-else class="text-center text-sm text-gray-400 py-4">
                                No hay pólizas asociadas
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </AuthenticatedLayout>

    <ConfirmationModal
        :show="confirmation.show"
        :title="confirmation.title"
        :message="confirmation.message"
        :type="confirmation.type"
        :confirm-label="confirmation.confirmLabel"
        @close="confirmation.show = false"
        @confirm="() => {
            confirmation.show = false;
            if (confirmation.callback) confirmation.callback();
        }"
    />
</template>