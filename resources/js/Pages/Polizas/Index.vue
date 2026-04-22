<script setup>
import { ref, watch, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    polizas: Object,
    filters: Object,
    aseguradoras: Array,
    ramos: Array,
    clientes: Array,
});

const searchQuery = ref(props.filters?.search || '');
const showFilters = ref(false);

const advancedFilters = ref({
    aseguradora_id: props.filters?.aseguradora_id || '',
    ramo_id: props.filters?.ramo_id || '',
    cliente_id: props.filters?.cliente_id || '',
    anio: props.filters?.anio || '',
    fecha_tipo: props.filters?.fecha_tipo || 'inicio_vigencia',
});

const handleSearch = () => {
    router.get(
        route('polizas.index'),
        { 
            search: searchQuery.value,
            ...advancedFilters.value
        },
        { preserveState: true, replace: true }
    );
};



const clearSearch = () => {
    searchQuery.value = '';
    advancedFilters.value = {
        aseguradora_id: '',
        ramo_id: '',
        cliente_id: '',
        anio: '',
        fecha_tipo: 'inicio_vigencia',
    };
    handleSearch();
};

const getStatusClass = (estado) => {
    const classes = {
        'vigente': 'bg-green-100 text-green-700 ring-green-600/20',
        'vencida': 'bg-red-100 text-red-700 ring-red-600/20',
        'renovada': 'bg-blue-100 text-blue-700 ring-blue-600/20',
        'cancelada': 'bg-gray-100 text-gray-700 ring-gray-600/20',
    };
    return classes[estado] || 'bg-gray-100 text-gray-700 ring-gray-600/20';
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('es-CO');
};

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

const deletePoliza = (id) => {
    confirmation.value = {
        show: true,
        title: 'Eliminar Póliza',
        message: '¿Estás seguro de que deseas eliminar esta póliza? Esta acción borrará también sus registros de cartera si no tienen abonos.',
        type: 'danger',
        confirmLabel: 'Eliminar',
        callback: () => router.delete(route('polizas.destroy', id))
    };
};
</script>

<template>

    <Head title="Directorio de Pólizas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">
                <h2
                    class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-emerald-600 pl-3 dark:text-gray-100">
                    Directorio de Pólizas
                </h2>
                <Link :href="route('polizas.create')"
                    class="inline-flex items-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-all flex-shrink-0">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"
                            stroke-width="2" />
                        <!-- Fallback standard plus para igualar el icono del usuario --->
                        <path
                            d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Agregar Póliza
                </Link>
            </div>
        </template>

        <div class="space-y-4">
            <!-- Table Card -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700">
                <!-- Toolbar -->
                <div
                    class="border-b border-gray-200 px-4 py-3 sm:px-6 flex flex-col sm:flex-row justify-between items-center gap-4 dark:border-gray-700">
                    <div class="w-full sm:w-auto flex-1 max-w-lg">
                        <form @submit.prevent="handleSearch" class="flex gap-2 w-full">
                            <div class="relative rounded-md shadow-sm flex-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" v-model="searchQuery"
                                    class="block w-full rounded-md border-0 py-2.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600"
                                    placeholder="Buscar por póliza, cliente o aseguradora...">
                            </div>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors">
                                Buscar
                            </button>
                             <button v-if="filters?.search || filters?.aseguradora_id || filters?.ramo_id || filters?.cliente_id || filters?.anio" type="button" @click="clearSearch"
                                class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2.5 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 dark:bg-gray-700 dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors"
                                title="Limpiar búsqueda">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button type="button" @click="showFilters = !showFilters"
                                :class="[showFilters ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-white text-gray-700 ring-gray-300 hover:bg-gray-50']"
                                class="inline-flex items-center justify-center rounded-md px-4 py-2.5 text-sm font-semibold shadow-sm ring-1 ring-inset focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 transition-colors">
                                <svg class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.972.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55a.75.75 0 01-1.219-.585V11.75a2.25 2.25 0 00-.659-1.59l-4.682-4.683a2.25 2.25 0 01-.659-1.59V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                                </svg>
                                Filtros
                            </button>
                            <a :href="route('polizas.export', advancedFilters)"
                                class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-emerald-700 shadow-sm ring-1 ring-inset ring-emerald-300 hover:bg-emerald-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 dark:bg-gray-700 dark:text-emerald-400 dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors"
                                title="Exportar a Excel">
                                <svg class="h-5 w-5 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                                Exportar
                            </a>
                        </form>
                    </div>
                </div>

                <!-- Advanced Filters Panel -->
                <div v-show="showFilters" class="border-b border-gray-200 bg-gray-50/50 px-4 py-4 sm:px-6 dark:border-gray-700 dark:bg-gray-800/30">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Aseguradora -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Aseguradora</label>
                            <select v-model="advancedFilters.aseguradora_id" @change="handleSearch"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <option value="">Todas</option>
                                <option v-for="aseg in aseguradoras" :key="aseg.id" :value="aseg.id">{{ aseg.nombre }}</option>
                            </select>
                        </div>
                        <!-- Ramo -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Ramo</label>
                            <select v-model="advancedFilters.ramo_id" @change="handleSearch"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <option value="">Todos</option>
                                <option v-for="ramo in ramos" :key="ramo.id" :value="ramo.id">{{ ramo.nombre }}</option>
                            </select>
                        </div>
                        <!-- Cliente -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Cliente</label>
                            <SearchableSelect 
                                v-model="advancedFilters.cliente_id" 
                                :options="clientes" 
                                label-key="nombre_razon_social" 
                                value-key="id"
                                placeholder="Todos los clientes"
                                @change="handleSearch"
                            />
                        </div>
                        <!-- Año y Tipo Fecha -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Año e Historial</label>
                            <div class="flex gap-2">
                                <input type="number" v-model="advancedFilters.anio" @change="handleSearch" placeholder="Año (Ej: 2024)"
                                    class="block w-24 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <select v-model="advancedFilters.fecha_tipo" @change="handleSearch"
                                    class="block flex-1 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    <option value="inicio_vigencia">Vigencia</option>
                                    <option value="expedicion_fecha">Expedición</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 dark:text-gray-200">
                                    Riesgo y Póliza</th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                    Clientes</th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                    Aseguradora / Ramo</th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                    Vigencia</th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">
                                    Valor
                                    Total</th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900 dark:text-gray-200">
                                    Estado</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            <tr v-for="poliza in polizas.data" :key="poliza.id"
                                class="hover:bg-gray-50 transition-colors dark:hover:bg-gray-700/50">
                                <td class="py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="min-w-0 flex-1">
                                            <div class="font-medium text-gray-900 dark:text-white break-words">
                                                {{ poliza.riesgo?.identificador || 'S/I' }}
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400 text-xs mt-0.5 break-all">
                                                {{ poliza.numero_poliza }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col gap-1">
                                        <div v-for="cliente in poliza.clientes.slice(0, 2)" :key="cliente.id"
                                            class="flex items-center gap-2">
                                            <span
                                                class="font-medium text-gray-800 dark:text-gray-200 break-words">{{
                                                    cliente.nombre_razon_social }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="font-medium text-gray-900 dark:text-gray-300 break-words">{{
                                        poliza.aseguradora.nombre }}
                                    </div>
                                    <div class="text-xs break-words">{{ poliza.ramo.nombre }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div :class="{'text-red-600 font-bold': new Date(poliza.inicio_vigencia) > new Date(poliza.fin_vigencia)}" class="font-medium text-gray-700 dark:text-gray-300">
                                        {{ formatDate(poliza.inicio_vigencia) }}
                                    </div>
                                    <div class="text-xs text-gray-500">al {{ formatDate(poliza.fin_vigencia) }}</div>
                                    <span v-if="new Date(poliza.inicio_vigencia) > new Date(poliza.fin_vigencia)" class="text-[10px] text-red-500 font-bold uppercase tracking-tighter">¡Fecha Irreal!</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-medium text-gray-900 dark:text-white">{{
                                        formatCurrency(poliza.valor_asegurado) }}</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                    <span :class="getStatusClass(poliza.estado)"
                                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset capitalize">
                                        {{ poliza.estado }}
                                    </span>
                                </td>
                                <td
                                    class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('polizas.show', poliza.id)"
                                            class="text-gray-400 hover:text-emerald-600 transition-colors"
                                            title="Ver Detalles">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </Link>
                                        <Link :href="route('polizas.edit', poliza.id)"
                                            class="text-gray-400 hover:text-blue-600 transition-colors" title="Editar">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </Link>
                                        <button @click="deletePoliza(poliza.id)" class="text-gray-400 hover:text-red-500 transition-colors" title="Eliminar">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="polizas.data.length === 0">
                                <td colspan="7" class="py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No se
                                        encontraron
                                        pólizas</h3>
                                    <p v-if="filters?.search" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Intenta con
                                        otros términos de búsqueda.</p>
                                    <p v-else class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza a registrar
                                        las
                                        pólizas de tus clientes.</p>
                                    <div class="mt-6" v-if="!filters?.search">
                                        <Link :href="route('polizas.create')"
                                            class="inline-flex items-center rounded-md bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                            </svg>
                                            Agregar Póliza
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination slot, Laravel standard format with Inertia -->
                <div v-if="polizas.data.length > 0 && polizas.links"
                    class="border-t border-gray-200 px-4 py-3 sm:px-6 flex items-center justify-between dark:border-gray-700">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Mostrando
                                <span class="font-medium">{{ polizas.from }}</span>
                                al
                                <span class="font-medium">{{ polizas.to }}</span>
                                de
                                <span class="font-medium">{{ polizas.total }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <Link v-for="(link, k) in polizas.links" :key="k" :href="link.url || '#'" :class="[
                                    link.active ? 'z-10 bg-emerald-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700',
                                    !link.url ? 'pointer-events-none opacity-50' : '',
                                    'relative inline-flex items-center px-4 py-2 text-sm font-semibold'
                                ]" :disabled="!link.url" preserve-scroll v-html="link.label">
                                </Link>
                            </nav>
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
