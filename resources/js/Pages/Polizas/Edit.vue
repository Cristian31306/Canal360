<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import SearchableSelect from '@/Components/SearchableSelect.vue';

const props = defineProps({
    poliza: Object,
    aseguradoras: Array,
    ramos: Array,
    riesgos: Array,
    clientes: Array,
    roles: Array
});

const form = useForm({
    numero_poliza: props.poliza.numero_poliza,
    aseguradora_id: props.poliza.aseguradora_id,
    ramo_id: props.poliza.ramo_id,
    riesgo_id: props.poliza.riesgo_id,
    expedicion_fecha: props.poliza.expedicion_fecha ? props.poliza.expedicion_fecha.split('T')[0] : '',
    inicio_vigencia: props.poliza.inicio_vigencia ? props.poliza.inicio_vigencia.split('T')[0] : '',
    fin_vigencia: props.poliza.fin_vigencia ? props.poliza.fin_vigencia.split('T')[0] : '',
    valor_asegurado: props.poliza.valor_asegurado,
    prima_antes_iva: props.poliza.prima_antes_iva,
    prima_total: props.poliza.prima_total,
    tasa: props.poliza.tasa || 0,
    estado: props.poliza.estado,
    clientes: [] // Array de { id, rol, nombre }
});

// --- MAPEADO PARA SEARCHABLE SELECTS ---
const mappedRiesgos = computed(() => props.riesgos.map(r => ({
    ...r,
    label_display: `${r.tipo_riesgo} (${r.identificador || 'S/I'})`
})));

const mappedClientes = computed(() => props.clientes.map(c => ({
    ...c,
    label_display: `${c.nombre_razon_social} (${c.numero_documento})`
})));

// Cargar clientes existentes al montar
onMounted(() => {
    if (props.poliza.clientes) {
        form.clientes = props.poliza.clientes.map(c => ({
            id: c.id,
            rol: c.pivot.rol,
            nombre: c.nombre_razon_social
        }));
    }
});

// --- AUTOMATIZACIÓN DE FECHAS ---
watch(() => form.inicio_vigencia, (newVal) => {
    if (newVal) {
        const date = new Date(newVal);
        date.setFullYear(date.getFullYear() + 1);
        form.fin_vigencia = date.toISOString().split('T')[0];
    }
});

// --- FORMATO DE MONEDA PARA FRONTEND ---
const formatNumber = (val) => {
    if (val === null || val === undefined || val === '') return '';
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

const parseNumber = (val) => {
    if (!val) return 0;
    return parseFloat(val.toString().replace(/\./g, ''));
};

const displayValorAsegurado = ref(formatNumber(form.valor_asegurado || ''));
const displayPrimaNeta = ref(formatNumber(form.prima_antes_iva || ''));
const displayPrimaTotal = ref(formatNumber(form.prima_total || ''));

const onInputValorAsegurado = (e) => {
    if (e.target.value === '') {
        form.valor_asegurado = 0;
        displayValorAsegurado.value = '';
        return;
    }
    const cleanValue = e.target.value.replace(/[^\d.]/g, '').replace(/\./g, '');
    const rawValue = parseFloat(cleanValue) || 0;
    form.valor_asegurado = rawValue;
    displayValorAsegurado.value = formatNumber(rawValue);
};

const onInputPrimaNeta = (e) => {
    if (e.target.value === '') {
        form.prima_antes_iva = 0;
        displayPrimaNeta.value = '';
        return;
    }
    const cleanValue = e.target.value.replace(/[^\d.]/g, '').replace(/\./g, '');
    const rawValue = parseFloat(cleanValue) || 0;
    form.prima_antes_iva = rawValue;
    displayPrimaNeta.value = formatNumber(rawValue);
};

const onInputPrimaTotal = (e) => {
    if (e.target.value === '') {
        form.prima_total = 0;
        displayPrimaTotal.value = '';
        return;
    }
    const cleanValue = e.target.value.replace(/[^\d.]/g, '').replace(/\./g, '');
    const rawValue = parseFloat(cleanValue) || 0;
    form.prima_total = rawValue;
    displayPrimaTotal.value = formatNumber(rawValue);
};

// --- CÁLCULOS FINANCIEROS ---
watch([() => form.prima_antes_iva, () => form.valor_asegurado], ([neta, valor]) => {
    if (valor > 0) {
        form.tasa = parseFloat(((neta / valor) * 100).toFixed(6));
    } else {
        form.tasa = 0;
    }
});

// --- VINCULACIÓN DE RAMO Y CLIENTES AL CAMBIAR RIESGO ---
const initialRiskLoaded = ref(false);
watch(() => form.riesgo_id, (newVal) => {
    // Solo vinculamos automáticamente si NO es la carga inicial
    if (initialRiskLoaded.value && newVal) {
        const riesgo = props.riesgos.find(r => r.id === newVal);
        if (riesgo) {
            // Autocompletar Ramo sin preguntar
            const ramoMatch = props.ramos.find(ramo => ramo.nombre === riesgo.tipo_riesgo);
            if (ramoMatch && form.ramo_id !== ramoMatch.id) {
                form.ramo_id = ramoMatch.id;
            }

            // Validar actualización de clientes
            if (riesgo.clientes && confirm('¿Desea actualizar la lista de clientes con los asociados a este nuevo riesgo?')) {
                form.clientes = riesgo.clientes.map(c => ({
                    id: c.id,
                    rol: 'tomador',
                    nombre: c.nombre_razon_social
                }));
            }
        }
    }
    initialRiskLoaded.value = true;
});

const selectedClienteId = ref('');
const selectedRol = ref('tomador');

const addCliente = () => {
    if (selectedClienteId.value) {
        if (!form.clientes.find(c => c.id === selectedClienteId.value)) {
            const clienteObj = props.clientes.find(c => c.id === selectedClienteId.value);
            form.clientes.push({
                id: clienteObj.id,
                rol: selectedRol.value,
                nombre: clienteObj.nombre_razon_social
            });
            selectedClienteId.value = '';
        }
    }
};

const removeCliente = (index) => {
    form.clientes.splice(index, 1);
};

const submit = () => {
    form.transform((data) => ({
        ...data,
        iva: 0, // Mandamos 0 al ser removido del form
    })).put(route('polizas.update', props.poliza.id));
};
</script>

<template>

    <Head title="Editar Póliza" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 w-full">
                <Link :href="route('polizas.show', poliza.id)"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-emerald-600 pl-3 dark:text-gray-100 flex items-center gap-3 overflow-hidden">
                        <span class="truncate">Editar Póliza #{{ poliza.numero_poliza }}</span>
                    </h2>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto py-6 space-y-6">
            <form @submit.prevent="submit">
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-hidden">
                    <div class="px-4 py-8 sm:p-10 space-y-12">
                        
                        <!-- SECCIÓN 1: IDENTIFICACIÓN -->
                        <div>
                            <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Datos de la Póliza</h3>
                            
                            <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Número de Póliza</label>
                                    <div class="mt-2">
                                        <input v-model="form.numero_poliza" type="text" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Estado</label>
                                    <div class="mt-2">
                                        <select v-model="form.estado" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 capitalize">
                                            <option v-for="st in ['vigente', 'vencida', 'renovada', 'cancelada']" :key="st" :value="st">{{ st }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Aseguradora</label>
                                    <div class="mt-2">
                                        <SearchableSelect 
                                            v-model="form.aseguradora_id" 
                                            :options="aseguradoras" 
                                            labelKey="nombre" 
                                            placeholder="Seleccione Aseguradora..."
                                        />
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Riesgo Asociado</label>
                                    <div class="mt-2">
                                        <SearchableSelect 
                                            v-model="form.riesgo_id" 
                                            :options="mappedRiesgos" 
                                            labelKey="label_display" 
                                            placeholder="Seleccione Riesgo..."
                                        />
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Ramo</label>
                                    <div class="mt-2">
                                        <SearchableSelect 
                                            v-model="form.ramo_id" 
                                            :options="ramos" 
                                            labelKey="nombre" 
                                            placeholder="Seleccione Ramo..."
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 2: VIGENCIAS Y VALORES -->
                        <div>
                            <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Vigencias y Liquidación</h3>
                            
                            <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Fecha Expedición</label>
                                    <div class="mt-2">
                                        <input v-model="form.expedicion_fecha" type="date" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Inicio Vigencia</label>
                                    <div class="mt-2">
                                        <input v-model="form.inicio_vigencia" type="date" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Fin Vigencia (Auto +1 año)</label>
                                    <div class="mt-2">
                                        <input v-model="form.fin_vigencia" type="date" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Valor Asegurado</label>
                                    <div class="mt-2 relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input :value="displayValorAsegurado" @input="onInputValorAsegurado" type="text" inputmode="numeric" class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Prima Neta</label>
                                    <div class="mt-2 relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input :value="displayPrimaNeta" @input="onInputPrimaNeta" type="text" inputmode="numeric" class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Prima Total (Incl. Gastos)</label>
                                    <div class="mt-2 relative rounded-md shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input :value="displayPrimaTotal" @input="onInputPrimaTotal" type="text" inputmode="numeric" class="block w-full rounded-md border-0 py-1.5 pl-7 text-gray-900 ring-1 ring-inset ring-emerald-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 bg-emerald-50 dark:bg-emerald-900/20">
                                    </div>
                                </div>
                                
                                <div class="sm:col-span-6">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Tasa Calculada (Neta / Asegurado): <span class="font-bold text-gray-900 dark:text-white">{{ form.tasa }} %</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN 3: CLIENTES Y ROLES -->
                        <div>
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white flex items-center gap-2">Participantes Asociados</h3>
                            </div>
                            
                            <div class="mt-6 space-y-4">
                                <!-- Agregar Cliente -->
                                <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="grid grid-cols-1 sm:grid-cols-6 gap-4 items-end">
                                        <div class="sm:col-span-3">
                                            <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Vincular Cliente</label>
                                            <div class="mt-1">
                                                <SearchableSelect 
                                                    v-model="selectedClienteId" 
                                                    :options="mappedClientes" 
                                                    labelKey="label_display" 
                                                    placeholder="Buscar en el directorio..."
                                                />
                                            </div>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Rol</label>
                                            <div class="mt-1">
                                                <select v-model="selectedRol" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600 capitalize">
                                                    <option v-for="rol in roles" :key="rol" :value="rol">{{ rol }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="sm:col-span-1">
                                            <button @click.prevent="addCliente" class="w-full rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 dark:bg-emerald-600 dark:hover:bg-emerald-500">
                                                Vincular
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lista de Clientes -->
                                <div class="space-y-3">
                                    <div v-for="(c, index) in form.clientes" :key="index" class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-500 uppercase">
                                                {{ c.rol.substring(0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ c.nombre }}</p>
                                                <p class="text-xs text-gray-500 capitalize">{{ c.rol }}</p>
                                            </div>
                                        </div>
                                        <button @click.prevent="removeCliente(index)" class="text-red-500 hover:text-red-700 p-1">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div v-if="form.clientes.length === 0" class="py-4 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-md">
                                        <p class="text-sm text-gray-500">No hay clientes asociados a esta póliza</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <!-- FOOTER ACTIONS -->
                    <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-xl">
                        <Link :href="route('polizas.show', poliza.id)" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:text-gray-700 transition-colors">Cancelar</Link>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-emerald-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-all disabled:opacity-75 disabled:cursor-not-allowed flex items-center gap-2">
                            <span v-if="form.processing" class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status"></span>
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
