<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    precios: Object,
    minerales: Array,
    filters: Object,
});

const anioFilter = ref(props.filters?.anio || '');

// Diálogo de confirmación
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
    if (page.props.flash?.success) showAlert('Éxito', page.props.flash.success, 'success');
});

const showAlert = (title, message, type = 'success') => {
    confirmation.value = {
        show: true,
        title,
        message,
        type,
        confirmLabel: 'Entendido',
        callback: null
    };
};

const handleFilter = () => {
    router.get(route('minerales.index'), { anio: anioFilter.value }, { preserveState: true, replace: true });
};

const deleteRegistro = (registro) => {
    confirmation.value = {
        show: true,
        title: 'Eliminar Registro',
        message: `¿Estás seguro de que deseas eliminar el registro de ${getMesNombre(registro.mes)} ${registro.anio}?`,
        type: 'danger',
        confirmLabel: 'Eliminar',
        callback: () => router.delete(route('minerales.destroy', registro.id))
    };
};

const getMesNombre = (mes) => {
    const meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    return meses[mes];
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(val);
};

const formatPercent = (val) => {
    const formatted = new Intl.NumberFormat('es-CO', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(val);
    return val > 0 ? `+${formatted}%` : `${formatted}%`;
};
</script>

<template>

    <Head title="Precios de Minerales" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-600 rounded-lg shadow-lg shadow-amber-500/30">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2
                        class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-amber-600 pl-3 dark:text-gray-100 uppercase">
                        Registro <span class="text-amber-600">de</span> Minerales
                    </h2>
                </div>
                <div class="flex items-center gap-3">
                    <Link :href="route('cat-minerales.index')"
                        class="inline-flex items-center rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-bold text-white shadow-xl hover:bg-slate-700 transition-all outline-none">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Configuración
                    </Link>
                    <Link :href="route('minerales.create')"
                        class="inline-flex items-center rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white shadow-xl shadow-amber-500/20 hover:bg-amber-500 hover:-translate-y-0.5 transition-all outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Nuevo Registro
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filtros -->
                <div
                    class="mb-8 p-4 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex items-center gap-4">
                    <div class="w-48">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Filtrar
                            por
                            Año</label>
                        <input type="number" v-model="anioFilter" @change="handleFilter" placeholder="Ej: 2024"
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:ring-amber-500 text-sm py-2">
                    </div>
                </div>

                <!-- Tabla de Precios -->
                <div
                    class="bg-white dark:bg-gray-800 shadow-xl shadow-amber-500/5 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div v-if="precios.data.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                    <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">
                                        Periodo
                                    </th>
                                    <th v-for="mineral in minerales" :key="mineral.id"
                                        class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">
                                        {{ mineral.nombre }}
                                    </th>
                                    <th
                                        class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right">
                                        Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="precio in precios.data" :key="precio.id"
                                    class="hover:bg-amber-50/20 dark:hover:bg-amber-900/10 transition-colors group">
                                    <td class="px-6 py-6">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{
                                                getMesNombre(precio.mes) }}</span>
                                            <span class="text-xs text-amber-600 font-bold tracking-widest">{{
                                                precio.anio
                                                }}</span>
                                        </div>
                                    </td>

                                    <td v-for="mineral in minerales" :key="mineral.id"
                                        class="px-6 py-6 border-l border-gray-50/50 dark:border-gray-700/50">
                                        <div class="flex flex-col">
                                            <span class="text-base font-black text-gray-900 dark:text-white">{{
                                                formatCurrency(precio[mineral.slug]) }}</span>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span v-if="precio.variaciones[mineral.slug]?.porcentaje !== 0"
                                                    :class="[precio.variaciones[mineral.slug]?.porcentaje > 0 ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20' : 'text-red-600 bg-red-50 dark:bg-red-900/20', 'text-[10px] font-black px-1.5 py-0.5 rounded flex items-center gap-0.5']">
                                                    <svg v-if="precio.variaciones[mineral.slug]?.porcentaje > 0"
                                                        class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M5 15l7-7 7 7" />
                                                    </svg>
                                                    <svg v-else class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                    {{ formatPercent(precio.variaciones[mineral.slug]?.porcentaje) }}
                                                </span>
                                                <span class="text-[10px] text-gray-400 font-medium">{{
                                                    precio.variaciones[mineral.slug]?.porcentaje !== 0 ?
                                                    formatCurrency(Math.abs(precio.variaciones[mineral.slug]?.diferencia))
                                                    : '-'
                                                    }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-6 text-right border-l border-gray-50/50 dark:border-gray-700/50">
                                        <div
                                            class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <Link :href="route('minerales.edit', precio.id)"
                                                class="p-2 text-gray-400 hover:text-amber-600 transition-colors">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                            </Link>
                                            <button @click="deleteRegistro(precio)"
                                                class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div
                            class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/30">
                            <Pagination :links="precios.links" />
                        </div>
                    </div>

                    <div v-else class="text-center py-20 flex flex-col items-center">
                        <div
                            class="w-20 h-20 bg-amber-50 dark:bg-amber-900/20 rounded-full flex items-center justify-center mb-4 text-amber-300">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-700 dark:text-white uppercase tracking-tight">Sin
                            registros de
                            precios</h3>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Lleva el control
                            histórico de
                            los metales mes a mes.</p>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmationModal :show="confirmation.show" :title="confirmation.title" :message="confirmation.message"
            :type="confirmation.type" :confirm-label="confirmation.confirmLabel" @close="confirmation.show = false"
            @confirm="() => {
                confirmation.show = false;
                if (confirmation.callback) confirmation.callback();
            }" />
    </AuthenticatedLayout>
</template>
