<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    auditorias: Object,
    usuarios: Array,
    filters: Object,
});

const formFilters = ref({
    usuario_id: props.filters?.usuario_id || '',
    accion: props.filters?.accion || '',
    entidad: props.filters?.entidad || '',
    fecha_inicio: props.filters?.fecha_inicio || '',
    fecha_fin: props.filters?.fecha_fin || '',
});

const applyFilters = () => {
    router.get(route('admin.auditoria.index'), formFilters.value, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    formFilters.value = {
        usuario_id: '',
        accion: '',
        entidad: '',
        fecha_inicio: '',
        fecha_fin: '',
    };
    applyFilters();
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatValue = (val) => {
    if (val === null || val === undefined) return '-';
    if (typeof val === 'boolean') return val ? 'Sí' : 'No';
    if (typeof val === 'object') return JSON.stringify(val);
    return val;
};

const keyLabels = {
    nombre: 'Nombre',
    nombre_razon_social: 'Razón Social',
    email: 'Correo',
    numero_documento: 'Documento',
    telefono: 'Teléfono',
    direccion: 'Dirección',
    is_admin: 'Es Admin',
    is_active: 'Activo',
    permisos: 'Permisos',
    numero_poliza: 'N° Póliza',
    prima_total: 'Prima Total',
    valor_asegurado: 'Valor Asegurado',
    estado: 'Estado',
    mes: 'Mes',
    anio: 'Año',
    oro: 'Oro',
    plata: 'Plata',
    platino: 'Platino',
    usuario: 'Usuario',
    nit: 'NIT',
    rol: 'Rol',
    metodo_pago: 'Método de Pago',
    monto: 'Monto',
    referencia: 'Referencia',
    observaciones: 'Observaciones',
    fecha_pago: 'Fecha Pago',
    inicio_vigencia: 'Inicio Vigencia',
    fin_vigencia: 'Fin Vigencia',
    expedicion_fecha: 'Fecha Expedición',
    tasa: 'Tasa',
};

const getAccionColor = (accion) => {
    accion = accion.toLowerCase();
    if (accion.includes('crear') || accion.includes('create')) return 'text-green-600 bg-green-50 border-green-100';
    if (accion.includes('editar') || accion.includes('update')) return 'text-blue-600 bg-blue-50 border-blue-100';
    if (accion.includes('eliminar') || accion.includes('delete')) return 'text-red-600 bg-red-50 border-red-100';
    return 'text-gray-600 bg-gray-50 border-gray-100';
};
</script>

<template>
    <Head title="Auditoría del Sistema" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <div class="p-2 bg-slate-800 rounded-lg shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <h2 class="text-xl font-black uppercase tracking-tight text-gray-800 dark:text-gray-200">
                    Historial de Auditoría
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filtros -->
                <div class="mb-8 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <InputLabel value="Usuario" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" />
                            <select v-model="formFilters.usuario_id" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm py-2">
                                <option value="">Todos</option>
                                <option v-for="user in usuarios" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                        </div>
                        <div>
                            <InputLabel value="Acción" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" />
                            <TextInput v-model="formFilters.accion" placeholder="Ej: Crear, Eliminar..." class="w-full py-2 text-sm" />
                        </div>
                        <div>
                            <InputLabel value="Entidad" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" />
                            <TextInput v-model="formFilters.entidad" placeholder="Ej: Cliente, Póliza..." class="w-full py-2 text-sm" />
                        </div>
                        <div>
                            <InputLabel value="Desde" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" />
                            <TextInput v-model="formFilters.fecha_inicio" type="date" class="w-full py-2 text-sm" />
                        </div>
                        <div>
                            <InputLabel value="Hasta" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1" />
                            <TextInput v-model="formFilters.fecha_fin" type="date" class="w-full py-2 text-sm" />
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <SecondaryButton @click="resetFilters" class="rounded-xl px-4 text-xs font-black uppercase tracking-widest border-gray-200">Limpiar</SecondaryButton>
                        <SecondaryButton @click="applyFilters" class="rounded-xl px-4 text-xs font-black uppercase tracking-widest bg-slate-800 text-white hover:bg-slate-700 border-none">Filtrar</SecondaryButton>
                    </div>
                </div>

                <!-- Lista de Auditoría -->
                <div class="bg-white dark:bg-gray-800 shadow-xl shadow-slate-500/5 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                    <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Fecha y Hora</th>
                                    <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Responsable</th>
                                    <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Acción</th>
                                    <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Entidad</th>
                                    <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Detalles</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="log in auditorias.data" :key="log.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-900/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ formatDate(log.created_at) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-500 uppercase">
                                                {{ log.usuario?.name?.charAt(0) || '?' }}
                                            </div>
                                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ log.usuario?.name || 'Sistema' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="['px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border', getAccionColor(log.accion)]">
                                            {{ log.accion }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ log.entidad_afectada }}</span>
                                            <span class="text-[10px] text-slate-400 font-bold">ID: {{ log.entidad_id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="max-w-xs overflow-hidden">
                                            <details v-if="log.detalles_json" class="group">
                                                <summary class="text-[10px] font-black text-blue-600 cursor-pointer uppercase tracking-widest list-none flex items-center gap-1">
                                                    Ver Cambios
                                                    <svg class="w-3 h-3 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </summary>
                                                <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl text-[10px] border border-gray-100 dark:border-gray-800 max-h-60 overflow-y-auto">
                                                    <div v-if="log.detalles_json.antes || log.detalles_json.despues" class="space-y-3">
                                                        <div v-for="(value, key) in log.detalles_json.despues" :key="key" class="flex flex-col gap-0.5 border-b border-gray-100 dark:border-gray-800 pb-1 last:border-0">
                                                            <span class="font-black text-gray-400 uppercase tracking-tighter">{{ keyLabels[key] || key }}:</span>
                                                            <div class="flex items-center gap-2 flex-wrap">
                                                                <span v-if="log.detalles_json.antes && log.detalles_json.antes[key] !== value" class="line-through text-red-400 font-bold bg-red-50 px-1 rounded truncate max-w-[120px]" :title="log.detalles_json.antes[key]">
                                                                    {{ formatValue(log.detalles_json.antes[key]) }}
                                                                </span>
                                                                <span class="text-slate-700 dark:text-slate-300 font-bold bg-slate-100 dark:bg-slate-800 px-1 rounded">
                                                                    {{ formatValue(value) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div v-else class="grid grid-cols-1 gap-2">
                                                        <div v-for="(value, key) in log.detalles_json" :key="key" class="flex flex-col gap-0.5 border-b border-gray-100 dark:border-gray-800 pb-1 last:border-0">
                                                            <span class="font-black text-gray-400 uppercase tracking-tighter">{{ keyLabels[key] || key }}:</span>
                                                            <span class="text-slate-700 dark:text-slate-300 font-bold bg-slate-100 dark:bg-slate-800 px-1 rounded truncate" :title="value">
                                                                {{ formatValue(value) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </details>
                                            <span v-else class="text-[10px] text-gray-400 font-bold italic uppercase tracking-widest">Sin detalles</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 border-t border-gray-50 dark:border-gray-700">
                        <Pagination :links="auditorias.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
