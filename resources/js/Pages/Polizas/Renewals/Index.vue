<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    polizas: Object,
    filters: Object,
    aseguradoras: Array,
    ramos: Array,
    clientes: Array,
});

const currentTab = ref(props.filters.tab || 'upcoming');
const showLiquidateModal = ref(false);
const selectedPoliza = ref(null);

const liquidateForm = useForm({
    liquidacion: '',
});

const finalizeForm = useForm({
    numero_poliza: '',
    anexo: 0,
    expedicion_fecha: '',
    inicio_vigencia: '',
    fin_vigencia: '',
    valor_asegurado: 0,
    prima_antes_iva: 0,
    prima_total: 0,
    tasa: 0,
});

const showFinalizeModal = ref(false);

// Estado para búsqueda y filtros
const searchQuery = ref(props.filters?.search || '');
const showFilters = ref(false);
const advancedFilters = ref({
    aseguradora_id: props.filters?.aseguradora_id || '',
    ramo_id: props.filters?.ramo_id || '',
    cliente_id: props.filters?.cliente_id || '',
    anio: props.filters?.anio || '',
    fecha_tipo: props.filters?.fecha_tipo || 'fin_vigencia',
});

// Estado para Alertas Premium
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
    // Si hay flashes por dependencias o errores, mostrarlos en modal premium
    if (page.props.flash?.error) {
        showAlert('Alerta', page.props.flash.error, 'danger');
    } else if (page.props.flash?.success) {
        showAlert('Éxito', page.props.flash.success, 'success');
    }
});

// Observar cambios en flash para mostrar alertas dinámicamente
watch(() => page.props.flash, (flash) => {
    if (flash?.error) {
        showAlert('Alerta', flash.error, 'danger');
    } else if (flash?.success) {
        showAlert('Éxito', flash.success, 'success');
    }
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

const handleSearch = () => {
    router.get(
        route('polizas.renewals'),
        { 
            tab: currentTab.value,
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
        fecha_tipo: 'fin_vigencia',
    };
    handleSearch();
};

const changeTab = (tab) => {
    currentTab.value = tab;
    router.get(route('polizas.renewals'), { tab: tab, ...advancedFilters.value, search: searchQuery.value }, { preserveState: true, preserveScroll: true });
};

const openLiquidateModal = (poliza) => {
    selectedPoliza.value = poliza;
    liquidateForm.liquidacion = poliza.liquidacion || '';
    showLiquidateModal.value = true;
};

const submitLiquidation = () => {
    liquidateForm.post(route('polizas.liquidate', selectedPoliza.value.id), {
        onSuccess: () => {
            showLiquidateModal.value = false;
            liquidateForm.reset();
        },
    });
};

const sendToInsurance = (poliza) => {
    confirmation.value = {
        show: true,
        title: 'Confirmar Trámite',
        message: '¿Confirmas que esta póliza ha sido enviada a la aseguradora?',
        type: 'info',
        confirmLabel: 'Confirmar Envío',
        callback: () => router.post(route('polizas.send-to-insurance', poliza.id))
    };
};

const cancelRenewal = (poliza) => {
    confirmation.value = {
        show: true,
        title: 'Cancelar Trámite',
        message: '¿Estás seguro de cancelar este trámite? El registro se eliminará y la póliza original volverá a aparecer en "Por Liquidar".',
        type: 'danger',
        confirmLabel: 'Borrar Trámite',
        callback: () => router.delete(route('polizas.cancel-renewal', poliza.id))
    };
};

const openFinalizeModal = (poliza) => {
    selectedPoliza.value = poliza;
    finalizeForm.reset();
    finalizeForm.clearErrors();
    showFinalizeModal.value = true;
};

// Autocalcular fin de vigencia (1 año después)
watch(() => finalizeForm.inicio_vigencia, (newVal) => {
    if (newVal) {
        const date = new Date(newVal);
        date.setFullYear(date.getFullYear() + 1);
        finalizeForm.fin_vigencia = date.toISOString().substring(0, 10);
    }
});

// Autocalcular Tasa
watch([() => finalizeForm.prima_antes_iva, () => finalizeForm.valor_asegurado], ([prima, valor]) => {
    if (valor > 0) {
        finalizeForm.tasa = ((prima / valor) * 100).toFixed(6);
    } else {
        finalizeForm.tasa = 0;
    }
});

const submitFinalize = () => {
    finalizeForm.post(route('polizas.finalize-renewal', selectedPoliza.value.id), {
        onSuccess: () => {
            showFinalizeModal.value = false;
        },
    });
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatCurrency = (val) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(val);
};

const getStatusBadge = (estado) => {
    const classes = {
        'vigente': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'liquidada': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'en_proceso': 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
        'vencida': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
    };
    return classes[estado] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Gestión de Renovaciones" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-600 rounded-lg shadow-lg shadow-emerald-500/30">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-emerald-600 pl-3 dark:text-gray-100 uppercase">
                    Renovaciones <span class="text-emerald-600">&</span> Trámites
                </h2>
            </div>
        </template>

        <div class="pt-2 pb-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mt-2 flex flex-col sm:flex-row gap-4">
                    <!-- PIPELINE TABS -->
                    <div class="flex-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-lg flex gap-1 shadow-sm overflow-x-auto border border-gray-200 dark:border-gray-700">
                        <button @click="changeTab('upcoming')"
                            :class="[currentTab === 'upcoming' ? 'bg-white dark:bg-gray-700 text-emerald-600 shadow-sm border border-gray-200 dark:border-gray-600' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300', 'flex-1 min-w-[120px] py-2 px-4 rounded-md font-bold text-xs uppercase tracking-tight transition-all duration-200 flex items-center justify-center gap-2']">
                            <span class="w-2 h-2 rounded-full bg-emerald-500" v-if="currentTab === 'upcoming'"></span>
                            1. Por Liquidar
                        </button>
                        <button @click="changeTab('liquidated')"
                            :class="[currentTab === 'liquidated' ? 'bg-white dark:bg-gray-700 text-emerald-600 shadow-sm border border-gray-200 dark:border-gray-600' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300', 'flex-1 min-w-[120px] py-2 px-4 rounded-md font-bold text-xs uppercase tracking-tight transition-all duration-200 flex items-center justify-center gap-2']">
                            <span class="w-2 h-2 rounded-full bg-emerald-500" v-if="currentTab === 'liquidated'"></span>
                            2. Liquidadas
                        </button>
                        <button @click="changeTab('processing')"
                            :class="[currentTab === 'processing' ? 'bg-white dark:bg-gray-700 text-emerald-600 shadow-sm border border-gray-200 dark:border-gray-600' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300', 'flex-1 min-w-[120px] py-2 px-4 rounded-md font-bold text-xs uppercase tracking-tight transition-all duration-200 flex items-center justify-center gap-2']">
                            <span class="w-2 h-2 rounded-full bg-emerald-500" v-if="currentTab === 'processing'"></span>
                            3. En Proceso
                        </button>
                        <button @click="changeTab('lost')"
                            :class="[currentTab === 'lost' ? 'bg-white dark:bg-gray-700 text-red-600 shadow-sm border border-gray-200 dark:border-gray-600' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300', 'flex-1 min-w-[120px] py-2 px-4 rounded-md font-bold text-xs uppercase tracking-tight transition-all duration-200 flex items-center justify-center gap-2']">
                            <span class="w-2 h-2 rounded-full bg-red-500" v-if="currentTab === 'lost'"></span>
                            4. Perdidas
                        </button>
                    </div>
                </div>

                <!-- Toolbar (Unificado con Directorio) -->
                <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col lg:flex-row gap-4 items-center">
                    <div class="flex-1 w-full max-w-2xl">
                        <form @submit.prevent="handleSearch" class="flex gap-2 w-full">
                            <div class="relative rounded-md shadow-sm flex-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" v-model="searchQuery"
                                    id="search-input"
                                    name="search"
                                    class="block w-full rounded-md border-0 py-2.5 pl-10 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-600 dark:bg-gray-900 dark:text-white dark:ring-gray-700 shadow-sm transition-all"
                                    placeholder="Buscar por póliza, cliente o aseguradora...">
                            </div>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors">
                                Buscar
                            </button>
                            <button v-if="filters?.search || filters?.aseguradora_id || filters?.ramo_id || filters?.cliente_id || filters?.anio" type="button" @click="clearSearch"
                                class="inline-flex items-center justify-center rounded-md bg-white px-3 text-red-600 ring-1 ring-inset ring-gray-300 hover:bg-red-50 focus-visible:outline-red-600 dark:bg-gray-700 dark:ring-gray-600 transition-colors">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button type="button" @click="showFilters = !showFilters"
                                :class="[showFilters ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-white text-gray-700 ring-gray-300 hover:bg-gray-50']"
                                class="inline-flex items-center justify-center rounded-md px-4 py-2.5 text-sm font-semibold shadow-sm ring-1 ring-inset focus-visible:outline-emerald-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 transition-colors">
                                <svg class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.972.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55a.75.75 0 01-1.219-.585V11.75a2.25 2.25 0 00-.659-1.59l-4.682-4.683a2.25 2.25 0 01-.659-1.59V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                                </svg>
                                Filtros
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Advanced Filters Panel -->
                <div v-show="showFilters" class="mt-4 p-6 bg-gray-50 dark:bg-gray-800/30 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="filter-aseguradora" class="block text-xs font-medium text-gray-500 mb-1">Aseguradora</label>
                            <select v-model="advancedFilters.aseguradora_id" id="filter-aseguradora" name="aseguradora_id" @change="handleSearch"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-emerald-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <option value="">Todas</option>
                                <option v-for="aseg in aseguradoras" :key="aseg.id" :value="aseg.id">{{ aseg.nombre }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="filter-ramo" class="block text-xs font-medium text-gray-500 mb-1">Ramo</label>
                            <select v-model="advancedFilters.ramo_id" id="filter-ramo" name="ramo_id" @change="handleSearch"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-emerald-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <option value="">Todos</option>
                                <option v-for="ramo in ramos" :key="ramo.id" :value="ramo.id">{{ ramo.nombre }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="filter-cliente" class="block text-xs font-medium text-gray-500 mb-1">Cliente</label>
                            <select v-model="advancedFilters.cliente_id" id="filter-cliente" name="cliente_id" @change="handleSearch"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-emerald-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <option value="">Todos</option>
                                <option v-for="cli in clientes" :key="cli.id" :value="cli.id">{{ cli.nombre_razon_social }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Año e Historial</label>
                            <div class="flex gap-2">
                                <input type="number" v-model="advancedFilters.anio" @change="handleSearch" placeholder="Año (Ej: 2024)"
                                    class="block w-24 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-emerald-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <select v-model="advancedFilters.fecha_tipo" @change="handleSearch"
                                    class="block flex-1 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-emerald-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    <option value="fin_vigencia">Vencimiento</option>
                                    <option value="expedicion_fecha">Expedición</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LISTADO -->
                <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:ring-gray-700 overflow-hidden transition-all duration-300">
                    <div class="p-0">
                        <div v-if="polizas.data.length > 0" class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800/50">
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wider">Identificador / Póliza</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wider">Cliente</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wider">Aseguradora / Ramo</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wider">Vigencia</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wider">Valor Total</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="poliza in polizas.data" :key="poliza.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ poliza.riesgo?.identificador || '' }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ poliza.numero_poliza }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300 uppercase truncate max-w-[200px]">
                                                    {{ poliza.clientes[0]?.nombre_razon_social }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-medium text-gray-900 dark:text-gray-200 uppercase">{{ poliza.aseguradora.nombre }}</span>
                                                <span class="text-[10px] text-gray-500 uppercase">{{ poliza.ramo.nombre }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                    Vence: {{ formatDate(poliza.fin_vigencia) }}
                                                </span>
                                                <span v-if="new Date(poliza.fin_vigencia) < new Date()" class="inline-flex mt-1 items-center rounded-md bg-red-100 px-2 py-0.5 text-[10px] font-medium text-red-700 ring-1 ring-inset ring-red-600/20 w-fit">
                                                    Vencida
                                                </span>
                                                <span v-else class="inline-flex mt-1 items-center rounded-md bg-green-100 px-2 py-0.5 text-[10px] font-medium text-green-700 ring-1 ring-inset ring-green-600/20 w-fit">
                                                    Vigente
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ formatCurrency(poliza.prima_total) }}
                                                </span>
                                                <span class="text-[10px] text-gray-500">
                                                    Tasa: {{ poliza.tasa }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex justify-end items-center gap-3">
                                                <!-- Acción según pestaña -->
                                                <button v-if="currentTab === 'upcoming' || currentTab === 'lost'"
                                                    @click="openLiquidateModal(poliza)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md transition-all shadow-sm active:scale-95">
                                                    Liquidar
                                                </button>

                                                <button v-if="currentTab === 'liquidated'"
                                                    @click="sendToInsurance(poliza)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-md transition-all shadow-sm active:scale-95">
                                                    Tramitar
                                                </button>

                                                <button v-if="currentTab === 'processing'"
                                                    @click="openFinalizeModal(poliza)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md transition-all shadow-sm active:scale-95">
                                                    Expedir
                                                </button>

                                                <!-- ELIMINAR / CANCELAR (Solo para trámites) -->
                                                <button v-if="currentTab === 'liquidated' || currentTab === 'processing'"
                                                    @click="cancelRenewal(poliza)"
                                                    class="p-2 text-slate-400 hover:text-red-600 transition-colors"
                                                    title="Eliminar Trámite">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>

                                                <Link :href="route('polizas.show', poliza.id)"
                                                    class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </Link>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-20 flex flex-col items-center">
                            <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-black text-slate-700 dark:text-white uppercase tracking-tight">Todo al día</h3>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">No hay pólizas pendientes en esta sección.</p>
                        </div>

                        <div v-if="polizas.data.length > 0"
                            class="border-t border-gray-200 px-6 py-4 flex items-center justify-between dark:border-gray-700">
                            <div class="flex-1 flex items-center justify-between">
                                <div>
                                    <p v-if="polizas.links" class="text-xs text-gray-700 dark:text-gray-300">
                                        Mostrando <span class="font-medium">{{ polizas.from }}</span> a <span class="font-medium">{{ polizas.to }}</span> de <span class="font-medium">{{ polizas.total }}</span> resultados
                                    </p>
                                    <p v-else class="text-xs text-gray-700 dark:text-gray-300 uppercase font-black tracking-widest">
                                        Total de registros: <span class="font-black text-emerald-600">{{ polizas.data.length }}</span>
                                    </p>
                                </div>
                                <nav v-if="polizas.links" class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                    <Link v-for="(link, k) in polizas.links" :key="k" :href="link.url || '#'" :class="[
                                        link.active ? 'z-10 bg-emerald-600 text-white' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:text-gray-300 dark:ring-gray-600',
                                        !link.url ? 'pointer-events-none opacity-50' : '',
                                        'relative inline-flex items-center px-3 py-1.5 text-xs font-semibold'
                                    ]" v-html="link.label">
                                    </Link>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL LIQUIDAR -->
        <Modal :show="showLiquidateModal" @close="showLiquidateModal = false" max-width="2xl">
            <div class="p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-emerald-100 rounded-lg text-emerald-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Liquidar Renovación</h2>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-widest mt-1">Ingresa la guía para este año</p>
                    </div>
                </div>

                <!-- Resumen Póliza Actual -->
                <div v-if="selectedPoliza" class="mb-6 grid grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Póliza Actual</span>
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ selectedPoliza.numero_poliza }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Vencimiento</span>
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ formatDate(selectedPoliza.fin_vigencia) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Valor Asegurado</span>
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ formatCurrency(selectedPoliza.valor_asegurado) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Prima Total</span>
                        <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ formatCurrency(selectedPoliza.prima_total) }}</span>
                    </div>
                </div>

                <form @submit.prevent="submitLiquidation" class="space-y-6">
                    <div>
                        <InputLabel for="liquidacion" value="Cálculos / Liquidación de este Año" class="uppercase text-[10px] font-bold tracking-widest text-gray-500 mb-2" />
                        <textarea
                            id="liquidacion"
                            v-model="liquidateForm.liquidacion"
                            rows="4"
                            class="w-full bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-emerald-500 transition-all font-mono text-xs p-4 text-gray-700 dark:text-gray-300 shadow-sm"
                            placeholder="Ej: Aumento de prima 5%, cambio de tasa..."
                            required
                        ></textarea>
                        
                        <!-- Guía Anterior -->
                        <div v-if="currentTab === 'upcoming' || currentTab === 'lost'" class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30 rounded-xl">
                            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Guía de esta póliza (Histórico):
                            </p>
                            <div class="text-[11px] text-amber-800/80 dark:text-amber-400/80 whitespace-pre-line leading-relaxed italic">{{ selectedPoliza?.liquidacion || 'Sin liquidación registrada del periodo anterior.' }}</div>
                        </div>

                        <div v-else-if="selectedPoliza?.poliza_anterior?.liquidacion" class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/30 rounded-xl">
                            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Guía de la póliza anterior (Histórico):
                            </p>
                            <div class="text-[11px] text-amber-800/80 dark:text-amber-400/80 whitespace-pre-line leading-relaxed italic">{{ selectedPoliza.poliza_anterior.liquidacion }}</div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <SecondaryButton @click="showLiquidateModal = false" class="rounded-md px-6">Cancelar</SecondaryButton>
                        <PrimaryButton :disabled="liquidateForm.processing" class="rounded-md shadow-sm px-8 uppercase tracking-widest text-xs font-bold bg-emerald-600 hover:bg-emerald-500">
                            Confirmar Liquidación
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- MODAL FINALIZAR / EXPEDIR -->
        <Modal :show="showFinalizeModal" @close="showFinalizeModal = false" max-width="xl">
            <div class="p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-emerald-100 rounded-lg text-emerald-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Finalizar Expedición</h2>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-widest mt-1">Confirma los datos finales de la póliza</p>
                    </div>
                </div>

                <form @submit.prevent="submitFinalize" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-1">
                            <InputLabel for="numero_poliza" value="Número de Póliza Definitivo" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />
                            <TextInput id="numero_poliza" v-model="finalizeForm.numero_poliza" type="text" class="mt-1 block w-full rounded-xl" required />
                            <div v-if="finalizeForm.errors.numero_poliza" class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ finalizeForm.errors.numero_poliza }}</div>
                        </div>
                        <div>
                            <InputLabel for="anexo" value="Anexo" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />
                            <TextInput id="anexo" v-model="finalizeForm.anexo" type="number" class="mt-1 block w-full rounded-xl" required />
                            <div v-if="finalizeForm.errors.anexo" class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ finalizeForm.errors.anexo }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <InputLabel for="expedicion_fecha" value="Fecha Expedición" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />
                            <TextInput id="expedicion_fecha" v-model="finalizeForm.expedicion_fecha" type="date" class="mt-1 block w-full rounded-xl" required />
                            <div v-if="finalizeForm.errors.expedicion_fecha" class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ finalizeForm.errors.expedicion_fecha }}</div>
                        </div>
                        <div>
                            <InputLabel for="inicio_vigencia" value="Inicio Vigencia" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />
                            <TextInput id="inicio_vigencia" v-model="finalizeForm.inicio_vigencia" type="date" class="mt-1 block w-full rounded-xl" required />
                            <div v-if="finalizeForm.errors.inicio_vigencia" class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ finalizeForm.errors.inicio_vigencia }}</div>
                        </div>
                        <div>
                            <InputLabel for="fin_vigencia" value="Fin Vigencia" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />
                            <TextInput id="fin_vigencia" v-model="finalizeForm.fin_vigencia" type="date" class="mt-1 block w-full rounded-xl" required />
                            <div v-if="finalizeForm.errors.fin_vigencia" class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ finalizeForm.errors.fin_vigencia }}</div>
                        </div>
                    </div>

                    <hr class="border-slate-100 dark:border-slate-700">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="valor_asegurado" value="Valor Asegurado" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />
                            <TextInput id="valor_asegurado" v-model="finalizeForm.valor_asegurado" type="number" step="0.01" class="mt-1 block w-full rounded-xl" required />
                            <div v-if="finalizeForm.errors.valor_asegurado" class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ finalizeForm.errors.valor_asegurado }}</div>
                        </div>
                        <div>
                            <InputLabel for="tasa" value="Tasa (%) - Autocalculada" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />
                            <TextInput id="tasa" v-model="finalizeForm.tasa" type="number" step="0.000001" class="mt-1 block w-full rounded-xl bg-slate-50 dark:bg-slate-900" readonly />
                            <div v-if="finalizeForm.errors.tasa" class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ finalizeForm.errors.tasa }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="prima_antes_iva" value="Prima neta" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />
                            <TextInput id="prima_antes_iva" v-model="finalizeForm.prima_antes_iva" type="number" step="0.01" class="mt-1 block w-full rounded-xl" required />
                            <div v-if="finalizeForm.errors.prima_antes_iva" class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ finalizeForm.errors.prima_antes_iva }}</div>
                        </div>
                        <div>
                            <InputLabel for="prima_total" value="Prima Total" class="uppercase text-[10px] font-black tracking-widest text-slate-400" />
                            <TextInput id="prima_total" v-model="finalizeForm.prima_total" type="number" step="0.01" class="mt-1 block w-full rounded-xl border-blue-200 bg-blue-50/30 font-black text-blue-700" required />
                            <div v-if="finalizeForm.errors.prima_total" class="text-red-500 text-[10px] font-bold mt-1 uppercase">{{ finalizeForm.errors.prima_total }}</div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <SecondaryButton @click="showFinalizeModal = false" class="rounded-md px-6">Cancelar</SecondaryButton>
                        <PrimaryButton :disabled="finalizeForm.processing" class="rounded-md shadow-sm px-8 bg-emerald-600 hover:bg-emerald-500 uppercase tracking-widest text-xs font-bold">
                            Expedir Póliza
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- MODAL DE CONFIRMACIÓN PREMIUM -->
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
    </AuthenticatedLayout>
</template>
