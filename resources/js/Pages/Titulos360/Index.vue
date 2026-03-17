<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    titulos: Object,
    filters: Object,
    aseguradoras: Array,
});

const searchQuery = ref(props.filters?.search || '');
const showFilters = ref(false);

const advancedFilters = ref({
    aseguradora_id: props.filters?.aseguradora_id || '',
    cliente_canal: props.filters?.cliente_canal || '',
    inicio_desde: props.filters?.inicio_desde || '',
    inicio_hasta: props.filters?.inicio_hasta || '',
    fin_desde: props.filters?.fin_desde || '',
    fin_hasta: props.filters?.fin_hasta || '',
    mes_inicio: props.filters?.mes_inicio || '',
    mes_fin: props.filters?.mes_fin || '',
    sort: props.filters?.sort || 'created_at',
    direction: props.filters?.direction || 'desc',
});

const meses = [
    { id: 1, nombre: 'Enero' },
    { id: 2, nombre: 'Febrero' },
    { id: 3, nombre: 'Marzo' },
    { id: 4, nombre: 'Abril' },
    { id: 5, nombre: 'Mayo' },
    { id: 6, nombre: 'Junio' },
    { id: 7, nombre: 'Julio' },
    { id: 8, nombre: 'Agosto' },
    { id: 9, nombre: 'Septiembre' },
    { id: 10, nombre: 'Octubre' },
    { id: 11, nombre: 'Noviembre' },
    { id: 12, nombre: 'Diciembre' },
];

const handleSearch = () => {
    router.get(
        route('titulos-360.index'),
        { 
            search: searchQuery.value,
            ...advancedFilters.value
        },
        { preserveState: true, replace: true }
    );
};

const toggleSort = (column) => {
    if (advancedFilters.value.sort === column) {
        advancedFilters.value.direction = advancedFilters.value.direction === 'asc' ? 'desc' : 'asc';
    } else {
        advancedFilters.value.sort = column;
        advancedFilters.value.direction = 'asc';
    }
    handleSearch();
};

const clearFilters = () => {
    searchQuery.value = '';
    advancedFilters.value = {
        aseguradora_id: '',
        cliente_canal: '',
        inicio_desde: '',
        inicio_hasta: '',
        fin_desde: '',
        fin_hasta: '',
        mes_inicio: '',
        mes_fin: '',
        sort: 'created_at',
        direction: 'desc',
    };
    handleSearch();
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
    callback: null
});

const deleteTitulo = (id) => {
    confirmation.value = {
        show: true,
        title: 'Eliminar Título',
        message: '¿Estás seguro de que deseas eliminar este registro de título minero?',
        callback: () => router.delete(route('titulos-360.destroy', id))
    };
};
</script>

<template>
    <Head title="CRM - Títulos Mineros" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-amber-600 pl-3 dark:text-gray-100 uppercase">
                    CRM: Títulos Mineros
                </h2>
                <Link :href="route('titulos-360.create')"
                    class="inline-flex items-center rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-bold text-white shadow-xl shadow-amber-500/20 hover:bg-amber-500 transition-all uppercase tracking-widest">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    Nuevo Título
                </Link>
            </div>
        </template>

        <div class="space-y-6 px-4 sm:px-0">
            <!-- Buscador y Filtros -->
            <div class="bg-white dark:bg-gray-800 shadow-xl shadow-amber-500/5 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" v-model="searchQuery" @keyup.enter="handleSearch"
                            placeholder="Buscar por Título, Nombre, Par, Municipio..."
                            class="block w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-0 focus:ring-2 focus:ring-amber-500 rounded-2xl text-sm transition-all dark:text-white">
                    </div>
                    <div class="flex gap-2">
                        <button @click="showFilters = !showFilters" 
                            :class="[showFilters ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600']"
                            class="px-6 py-3 rounded-2xl font-bold text-xs uppercase tracking-widest transition-all">
                            Filtros
                        </button>
                        <button @click="handleSearch" class="px-8 py-3 bg-slate-900 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-all">
                            Buscar
                        </button>
                    </div>
                </div>

                <!-- Panel de Filtros Avanzados -->
                <div v-show="showFilters" class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Aseguradora</label>
                            <select v-model="advancedFilters.aseguradora_id" @change="handleSearch" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl text-sm focus:ring-amber-500 dark:text-white">
                                <option value="">Todas</option>
                                <option v-for="aseg in aseguradoras" :key="aseg.id" :value="aseg.id">{{ aseg.nombre }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Cliente Canal</label>
                            <select v-model="advancedFilters.cliente_canal" @change="handleSearch" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl text-sm focus:ring-amber-500 dark:text-white">
                                <option value="">Todos</option>
                                <option value="si">Solo Clientes Canal</option>
                                <option value="no">Solo Externos</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filtros por Mes -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100 dark:border-gray-700 border-dashed">
                        <div>
                            <label class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2">Filtrar por Mes de Inicio</label>
                            <select v-model="advancedFilters.mes_inicio" @change="handleSearch" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl text-sm focus:ring-amber-500 dark:text-white">
                                <option value="">Todos los meses</option>
                                <option v-for="mes in meses" :key="mes.id" :id="mes.id" :value="mes.id">{{ mes.nombre }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-amber-600 uppercase tracking-widest mb-2">Filtrar por Mes de Fin</label>
                            <select v-model="advancedFilters.mes_fin" @change="handleSearch" class="w-full bg-gray-50 dark:bg-gray-900 border-0 rounded-xl text-sm focus:ring-amber-500 dark:text-white">
                                <option value="">Todos los meses</option>
                                <option v-for="mes in meses" :key="mes.id" :id="mes.id" :value="mes.id">{{ mes.nombre }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filtros de Fecha -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-gray-50/50 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-100 dark:border-gray-700">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-amber-600 uppercase tracking-[0.2em]">Rango Inicio Vigencia</label>
                            <div class="flex items-center gap-3">
                                <input type="date" v-model="advancedFilters.inicio_desde" @change="handleSearch" class="flex-1 bg-white dark:bg-gray-800 border-0 rounded-xl text-xs focus:ring-amber-500 dark:text-white">
                                <span class="text-gray-400 font-bold">a</span>
                                <input type="date" v-model="advancedFilters.inicio_hasta" @change="handleSearch" class="flex-1 bg-white dark:bg-gray-800 border-0 rounded-xl text-xs focus:ring-amber-500 dark:text-white">
                            </div>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-black text-amber-600 uppercase tracking-[0.2em]">Rango Fin Vigencia</label>
                            <div class="flex items-center gap-3">
                                <input type="date" v-model="advancedFilters.fin_desde" @change="handleSearch" class="flex-1 bg-white dark:bg-gray-800 border-0 rounded-xl text-xs focus:ring-amber-500 dark:text-white">
                                <span class="text-gray-400 font-bold">a</span>
                                <input type="date" v-model="advancedFilters.fin_hasta" @change="handleSearch" class="flex-1 bg-white dark:bg-gray-800 border-0 rounded-xl text-xs focus:ring-amber-500 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button @click="clearFilters" class="text-[10px] font-black text-red-500 uppercase tracking-widest hover:text-red-600">
                            Limpiar todos los filtros
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de Resultados -->
            <div class="bg-white dark:bg-gray-800 shadow-xl shadow-amber-500/5 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div v-if="titulos.data.length > 0" class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th @click="toggleSort('titulo')" class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:text-amber-600 transition-colors">
                                    <div class="flex items-center gap-1">
                                        Título / Par
                                        <svg v-if="advancedFilters.sort === 'titulo'" class="h-3 w-3" :class="[advancedFilters.direction === 'asc' ? 'rotate-180' : '']" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                                    </div>
                                </th>
                                <th @click="toggleSort('nombre')" class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:text-amber-600 transition-colors">
                                    <div class="flex items-center gap-1">
                                        Titular
                                        <svg v-if="advancedFilters.sort === 'nombre'" class="h-3 w-3" :class="[advancedFilters.direction === 'asc' ? 'rotate-180' : '']" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                                    </div>
                                </th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Ubicación</th>
                                <th @click="toggleSort('fecha_inicio')" class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:text-amber-600 transition-colors">
                                    <div class="flex items-center gap-1">
                                        Vigencia
                                        <svg v-if="advancedFilters.sort === 'fecha_inicio'" class="h-3 w-3" :class="[advancedFilters.direction === 'asc' ? 'rotate-180' : '']" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                                    </div>
                                </th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Aseguradora</th>
                                <th @click="toggleSort('valor_asegurado')" class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:text-amber-600 transition-colors">
                                    <div class="flex items-center gap-1">
                                        Valor Asegurado
                                        <svg v-if="advancedFilters.sort === 'valor_asegurado'" class="h-3 w-3" :class="[advancedFilters.direction === 'asc' ? 'rotate-180' : '']" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" /></svg>
                                    </div>
                                </th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Estado</th>
                                <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="titulo in titulos.data" :key="titulo.id" class="hover:bg-amber-50/20 dark:hover:bg-amber-900/10 transition-colors group">
                                <td class="px-6 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ titulo.titulo }}</span>
                                        <span class="text-[10px] text-amber-600 font-bold tracking-widest">{{ titulo.par || 'S/P' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ titulo.nombre }}</span>
                                        <span class="text-[10px] text-gray-400 truncate max-w-[200px]">{{ titulo.minerales }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase">{{ titulo.municipio }}</span>
                                        <span class="text-[10px] text-gray-400 font-medium">{{ titulo.departamento }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6 font-mono text-xs">
                                    <div class="flex flex-col text-gray-500">
                                        <span>{{ formatDate(titulo.fecha_inicio) }}</span>
                                        <span>{{ formatDate(titulo.fecha_fin) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-300">
                                        {{ titulo.aseguradora?.nombre || titulo.aseguradora_nombre || 'S/I' }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 font-black text-sm text-gray-900 dark:text-white">
                                    {{ formatCurrency(titulo.valor_asegurado) }}
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span v-if="titulo.cliente_canal" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        Cliente Canal
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-100">
                                        Externo
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <Link :href="route('titulos-360.edit', titulo.id)" class="p-2 text-gray-400 hover:text-amber-600 transition-colors">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M18.364 5.636l-3.536 3.536m0 0l3.536 3.536m-3.536-3.536L15 4" />
                                            </svg>
                                        </Link>
                                        <button @click="deleteTitulo(titulo.id)" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="px-6 py-8 border-t border-gray-50 dark:border-gray-700">
                        <Pagination :links="titulos.links" />
                    </div>
                </div>

                <div v-else class="text-center py-24 flex flex-col items-center">
                    <div class="w-24 h-24 bg-amber-50 dark:bg-amber-900/20 rounded-full flex items-center justify-center mb-6 text-amber-300">
                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-slate-700 dark:text-white uppercase tracking-tight">Sin registros 360</h3>
                    <p class="text-slate-400 text-sm font-bold uppercase tracking-widest mt-2">Comienza a mapear todos los títulos mineros de interés.</p>
                </div>
            </div>
        </div>

        <ConfirmationModal
            :show="confirmation.show"
            :title="confirmation.title"
            :message="confirmation.message"
            type="danger"
            confirm-label="Eliminar"
            @close="confirmation.show = false"
            @confirm="() => {
                confirmation.show = false;
                if (confirmation.callback) confirmation.callback();
            }"
        />
    </AuthenticatedLayout>
</template>
