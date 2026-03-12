<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    clientes: Array,
    ramos: Array,
});

const form = useForm({
    cliente_ids: [],
    tipo_riesgo: props.ramos.length > 0 ? props.ramos[0].nombre : '',
    identificador: '',
    descripcion: '',
    es_nad: false,
    numero_nad: '',
});

// Lógica de Buscador de Clientes
const clientSearch = ref('');
const showClientResults = ref(false);

const filteredClients = computed(() => {
    if (!clientSearch.value) return [];
    const search = clientSearch.value.toLowerCase();
    return props.clientes.filter(c => 
        (c.nombre_razon_social.toLowerCase().includes(search) || 
         c.numero_documento.includes(search)) &&
        !form.cliente_ids.includes(c.id)
    ).slice(0, 10);
});

const selectedClients = computed(() => {
    return props.clientes.filter(c => form.cliente_ids.includes(c.id));
});

const addClient = (clientId) => {
    if (!form.cliente_ids.includes(clientId)) {
        form.cliente_ids.push(clientId);
    }
    clientSearch.value = '';
    showClientResults.value = false;
};

const removeClient = (clientId) => {
    form.cliente_ids = form.cliente_ids.filter(id => id !== clientId);
    // Si queda solo 1 o ninguno, apagar NAD
    if (form.cliente_ids.length <= 1) {
        form.es_nad = false;
        form.numero_nad = '';
    }
};

const submit = () => {
    form.post(route('riesgos.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nuevo Riesgo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 w-full">
                <Link :href="route('riesgos.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-blue-600 pl-3 dark:text-gray-100 flex items-center gap-3 overflow-hidden">
                        <span class="truncate">Nuevo Riesgo</span>
                    </h2>
                </div>
            </div>
        </template>

        <div class="max-w-4xl mx-auto py-8">
            <form @submit.prevent="submit" class="space-y-8">
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-visible">
                    <div class="px-6 py-8 sm:p-10">
                        <div class="grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-6">
                            
                            <!-- Buscador de Clientes (Múltiples) -->
                            <div class="sm:col-span-6 space-y-5">
                                <div class="mb-2">
                                    <label class="block text-sm font-semibold leading-6 text-gray-900 dark:text-gray-200">Clientes Asociados <span class="text-red-500">*</span></label>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Busca y agrega los clientes vinculados a este riesgo (pueden ser varios).</p>
                                </div>

                                <div class="relative">
                                    <div class="flex gap-2">
                                        <div class="relative flex-1">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <input 
                                                type="text" 
                                                v-model="clientSearch" 
                                                @focus="showClientResults = true"
                                                class="block w-full rounded-md border-0 py-2.5 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600" 
                                                placeholder="Escribe nombre o documento..."
                                            >
                                        </div>
                                    </div>

                                    <!-- Resultados del Buscador -->
                                    <div v-if="showClientResults && filteredClients.length > 0" class="absolute z-50 mt-1 w-full rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-700 dark:ring-gray-600 overflow-hidden">
                                        <div v-for="cliente in filteredClients" :key="cliente.id" 
                                             @click="addClient(cliente.id)"
                                             class="cursor-pointer select-none px-4 py-2 text-sm text-gray-900 hover:bg-blue-600 hover:text-white dark:text-gray-200 flex justify-between items-center transition-colors"
                                        >
                                            <span>{{ cliente.nombre_razon_social }} ({{ cliente.numero_documento }})</span>
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lista de Seleccionados -->
                                <div v-if="selectedClients.length > 0" class="flex flex-wrap gap-3 mt-6 pb-2">
                                    <div v-for="cliente in selectedClients" :key="cliente.id" 
                                         class="inline-flex items-center gap-x-2 rounded-md bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 ring-1 ring-inset ring-blue-700/20 dark:bg-blue-900/30 dark:text-blue-400 dark:ring-blue-400/30 shadow-sm"
                                    >
                                        <span>{{ cliente.nombre_razon_social }}</span>
                                        <button type="button" @click="removeClient(cliente.id)" class="inline-flex items-center justify-center h-5 w-5 rounded-full hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors text-blue-600 dark:text-blue-400" title="Eliminar de la lista">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-red-500" v-if="form.errors.cliente_ids">{{ form.errors.cliente_ids }}</p>

                                <!-- Opcion NAD (Solo si hay > 1 cliente) -->
                                <div v-if="form.cliente_ids.length > 1" class="bg-blue-50/50 dark:bg-blue-900/10 rounded-lg p-5 border border-blue-100 dark:border-blue-800/50 mt-4 space-y-4 animate-in fade-in slide-in-from-top-2">
                                    <div class="relative flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input id="es_nad" v-model="form.es_nad" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label for="es_nad" class="font-semibold text-gray-900 dark:text-white">¿Es una Agrupación de Datos (NAD)?</label>
                                            <p class="text-gray-500 dark:text-gray-400">Marca esta opción si este riesgo tiene un número de agrupación asignado.</p>
                                        </div>
                                    </div>
                                    
                                    <div v-if="form.es_nad" class="ml-7 animate-in fade-in slide-in-from-top-1 transition-all">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Número de NAD <span class="text-red-500">*</span></label>
                                        <input 
                                            type="text" 
                                            v-model="form.numero_nad" 
                                            required
                                            class="block w-full max-w-xs rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600" 
                                            placeholder="Ingresa el número NAD..."
                                        >
                                        <p class="mt-1 text-xs text-red-500" v-if="form.errors.numero_nad">{{ form.errors.numero_nad }}</p>
                                    </div>
                                </div>
                            </div>

                            <hr class="sm:col-span-6 border-gray-100 dark:border-gray-700 my-4">

                            <!-- Tipo de Riesgo e Identificador -->
                            <div class="sm:col-span-3 space-y-2">
                                <div>
                                    <label class="block text-sm font-semibold leading-6 text-gray-900 dark:text-gray-200">Tipo de Riesgo <span class="text-red-500">*</span></label>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Selecciona según los Ramos configurados.</p>
                                </div>
                                <select v-model="form.tipo_riesgo" required class="block w-full rounded-md border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    <option value="" disabled>Seleccione un ramo...</option>
                                    <option v-for="ramo in ramos" :key="ramo.id" :value="ramo.nombre">{{ ramo.nombre }}</option>
                                </select>
                                <p class="mt-1 text-xs text-red-500" v-if="form.errors.tipo_riesgo">{{ form.errors.tipo_riesgo }}</p>
                            </div>

                            <div class="sm:col-span-3 space-y-2">
                                <div>
                                    <label class="block text-sm font-semibold leading-6 text-gray-900 dark:text-gray-200">Identificador (Opcional)</label>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Placa, Cédula asegurada, etc.</p>
                                </div>
                                <input 
                                    type="text" 
                                    v-model="form.identificador" 
                                    class="block w-full rounded-md border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600" 
                                    placeholder="Ej: LJL695"
                                >
                                <p class="mt-1 text-xs text-red-500" v-if="form.errors.identificador">{{ form.errors.identificador }}</p>
                            </div>

                            <!-- Descripción -->
                            <div class="sm:col-span-6 space-y-2">
                                <div>
                                    <label class="block text-sm font-semibold leading-6 text-gray-900 dark:text-gray-200">Descripción / Detalles Adicionales</label>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Modelo, maquinaria, datos específicos para vida/salud, etc.</p>
                                </div>
                                <textarea 
                                    v-model="form.descripcion" 
                                    rows="4" 
                                    class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600" 
                                    placeholder="Ej: Marcación especial, modelo 2024, etc."
                                ></textarea>
                                <p class="mt-1 text-xs text-red-500" v-if="form.errors.descripcion">{{ form.errors.descripcion }}</p>
                            </div>
                            
                        </div>
                    </div>

                    <!-- Footer / Actions -->
                    <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-6 py-6 sm:px-10 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-xl">
                        <Link :href="route('riesgos.index')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:text-gray-700 transition-colors">Cancelar</Link>
                        <button 
                            type="submit" 
                            :disabled="form.processing || form.cliente_ids.length === 0" 
                            class="rounded-md bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-xl hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            <span v-if="form.processing" class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]"></span>
                            Guardar Riesgo
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Backdrop para cerrar buscador al hacer click afuera -->
        <div v-if="showClientResults" @click="showClientResults = false" class="fixed inset-0 z-40 transparent"></div>
    </AuthenticatedLayout>
</template>
