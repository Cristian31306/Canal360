<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Pagination from '../../Components/Pagination.vue';

// Función debounce local para evitar dependencias externas problemáticas en el build
const debounce = (fn, wait) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), wait);
    };
};

const props = defineProps({
    carteras: Object,
    filters: Object,
    aseguradoras: Array,
    ramos: Array,
    clientes: Array,
    estados: Array,
    years: Array // Años dinámicos desde el backend
});

const searchQuery = ref(props.filters.search || '');
const showFilters = ref(false);

const advancedFilters = ref({
    aseguradora_id: props.filters.aseguradora_id || '',
    ramo_id: props.filters.ramo_id || '',
    cliente_id: props.filters.cliente_id || '',
    estado: props.filters.estado || '',
    anio: props.filters.anio || '',
});

const handleSearch = debounce(() => {
    router.get(route('cartera.index'), {
        search: searchQuery.value,
        ...advancedFilters.value
    }, {
        preserveState: true,
        replace: true
    });
}, 300);

// Observar cambios en filtros avanzados para disparar búsqueda automática
watch(() => advancedFilters.value, handleSearch, { deep: true });
watch(searchQuery, handleSearch);

const clearSearch = () => {
    searchQuery.value = '';
    advancedFilters.value = {
        aseguradora_id: '',
        ramo_id: '',
        cliente_id: '',
        estado: '',
        anio: '',
    };
    handleSearch();
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
    }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Intl.DateTimeFormat('es-CO', { year: 'numeric', month: 'short', day: 'numeric' }).format(new Date(dateString));
};

const getStatusClass = (status) => {
    switch (status) {
        case 'pagado': return 'bg-emerald-100 text-emerald-800 ring-emerald-600/20';
        case 'pendiente': return 'bg-amber-100 text-amber-800 ring-amber-600/20';
        case 'vencido': return 'bg-rose-100 text-rose-800 ring-rose-600/20';
        case 'acuerdo_pago': return 'bg-blue-100 text-blue-800 ring-blue-600/20';
        default: return 'bg-gray-100 text-gray-800 ring-gray-600/20';
    }
};
</script>

<template>
    <Head title="Gestión de Cartera" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-blue-600 pl-3 dark:text-gray-100 uppercase tracking-widest">
                    Gestión de Cartera
                </h2>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Table Card -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700">
                <!-- Toolbar -->
                <div class="border-b border-gray-200 px-4 py-4 sm:px-6 flex flex-col sm:flex-row justify-between items-center gap-4 dark:border-gray-700 font-sans">
                    <div class="w-full sm:w-auto flex-1 max-w-lg">
                        <form @submit.prevent="handleSearch" class="flex gap-2 w-full">
                            <div class="relative rounded-md shadow-sm flex-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" v-model="searchQuery"
                                    class="block w-full rounded-md border-0 py-2.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600"
                                    placeholder="Buscar por póliza, cliente o aseguradora...">
                            </div>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors">
                                Buscar
                            </button>
                            <button v-if="searchQuery || advancedFilters.aseguradora_id || advancedFilters.ramo_id || advancedFilters.cliente_id || advancedFilters.anio || advancedFilters.estado"
                                type="button" @click="clearSearch"
                                class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2.5 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 dark:bg-gray-700 dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors"
                                title="Limpiar búsqueda">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button type="button" @click="showFilters = !showFilters"
                                :class="[showFilters ? 'bg-blue-50 text-blue-700 ring-blue-600/20' : 'bg-white text-gray-700 ring-gray-300 hover:bg-gray-50']"
                                class="inline-flex items-center justify-center rounded-md px-4 py-2.5 text-sm font-semibold shadow-sm ring-1 ring-inset focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 transition-colors">
                                <svg class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.972.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55a.75.75 0 01-1.219-.585V11.75a2.25 2.25 0 00-.659-1.59l-4.682-4.683a2.25 2.25 0 01-.659-1.59V2.34a.75.75 0 01.628-.74z" clip-rule="evenodd" />
                                </svg>
                                Filtros
                            </button>
                            <a :href="route('cartera.export', advancedFilters)"
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
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        <!-- Aseguradora -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-tighter">Aseguradora</label>
                            <select v-model="advancedFilters.aseguradora_id"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <option value="">Todas</option>
                                <option v-for="aseg in aseguradoras" :key="aseg.id" :value="aseg.id">{{ aseg.nombre }}</option>
                            </select>
                        </div>
                        <!-- Ramo -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-tighter">Ramo</label>
                            <select v-model="advancedFilters.ramo_id"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <option value="">Todos</option>
                                <option v-for="ramo in ramos" :key="ramo.id" :value="ramo.id">{{ ramo.nombre }}</option>
                            </select>
                        </div>
                        <!-- Cliente -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-tighter">Cliente</label>
                            <select v-model="advancedFilters.cliente_id"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <option value="">Todos</option>
                                <option v-for="cli in clientes" :key="cli.id" :value="cli.id">{{ cli.nombre_razon_social }}</option>
                            </select>
                        </div>
                        <!-- Estado -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-tighter">Estado</label>
                            <select v-model="advancedFilters.estado"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600 capitalize">
                                <option value="">Todos</option>
                                <option v-for="est in estados" :key="est" :value="est">{{ est.replace('_', ' ') }}</option>
                            </select>
                        </div>
                        <!-- Año -->
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-tighter">Año Expedición</label>
                            <select v-model="advancedFilters.anio"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <option value="">Todos</option>
                                <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 dark:text-gray-200">Póliza / Cliente</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">Aseguradora / Ramo</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 dark:text-gray-200">Saldo Pendiente</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900 dark:text-gray-200">Días Mora</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900 dark:text-gray-200">Estado</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-right font-black uppercase tracking-widest text-[10px] text-gray-400">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            <tr v-for="cartera in carteras.data" :key="cartera.id" class="hover:bg-gray-50 transition-colors dark:hover:bg-gray-700/50">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6 font-sans">
                                    <div class="font-bold text-gray-900 dark:text-white">#{{ cartera.poliza.numero_poliza }}</div>
                                    <div class="text-xs text-gray-500 uppercase font-black tracking-tighter truncate max-w-[200px]">{{ cartera.poliza.clientes[0]?.nombre_razon_social }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400 font-sans">
                                    <div class="font-medium text-gray-900 dark:text-gray-300 uppercase tracking-tighter">{{ cartera.poliza.aseguradora.nombre }}</div>
                                    <div class="text-[10px] font-bold text-blue-500 uppercase">{{ cartera.poliza.ramo.nombre }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-right font-sans">
                                    <div class="font-black text-gray-900 dark:text-white">{{ formatCurrency(cartera.saldo_pendiente) }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold">Total: {{ formatCurrency(cartera.valor_a_pagar) }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center font-sans">
                                    <span :class="[cartera.dias_en_cartera > 30 ? 'text-rose-600 bg-rose-50' : 'text-slate-600 bg-slate-50', 'inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-black ring-1 ring-inset ring-gray-200']">
                                        {{ cartera.dias_en_cartera }} d
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-center font-sans">
                                    <span :class="[getStatusClass(cartera.estado), 'inline-flex items-center rounded-md px-2.5 py-1 text-[10px] font-black uppercase tracking-widest ring-1 ring-inset shadow-sm capitalize']">
                                        {{ cartera.estado.replace('_', ' ') }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 font-sans">
                                    <Link :href="route('cartera.show', cartera.id)" class="text-blue-600 hover:text-blue-900 font-black uppercase text-[10px] tracking-widest flex items-center justify-end gap-1 group">
                                        Gestionar
                                        <svg class="h-4 w-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="carteras.data.length === 0">
                                <td colspan="6" class="py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <h3 class="mt-2 text-sm font-black text-gray-400 uppercase tracking-widest">No se encontraron deudas</h3>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="carteras.data.length > 0 && carteras.links" class="border-t border-gray-200 px-4 py-4 sm:px-6 dark:border-gray-700 bg-gray-50/20">
                    <Pagination :links="carteras.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
