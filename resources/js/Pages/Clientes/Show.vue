<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    cliente: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';
    return new Intl.DateTimeFormat('es-CO', { year: 'numeric', month: 'long', day: 'numeric' }).format(date);
};
</script>

<template>
    <Head :title="cliente.nombre_razon_social" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 w-full">
                <Link :href="route('clientes.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-blue-600 pl-3 dark:text-gray-100 flex items-center gap-3 overflow-hidden">
                        <span class="truncate">{{ cliente.nombre_razon_social }}</span>
                        <span class="hidden sm:inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/30 flex-shrink-0">
                            {{ cliente.tipo_persona === 'juridica' ? 'Persona Jurídica' : 'Persona Natural' }}
                        </span>
                    </h2>
                    <Link
                        :href="route('clientes.edit', cliente.id)"
                        class="inline-flex items-center rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600 transition-all flex-shrink-0"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        Editar Cliente
                    </Link>
                </div>
            </div>
        </template>

        <div class="max-w-6xl mx-auto py-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Columna Izquierda: Tarjeta Principal -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-center text-gray-900 dark:text-gray-100 mt-2">{{ cliente.nombre_razon_social }}</h3>
                            <p class="text-center text-gray-500 text-sm mt-1 dark:text-gray-400">{{ cliente.tipo_documento }} {{ cliente.numero_documento }}</p>
                            
                            <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-6">
                                <dl class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                                    <div class="flex justify-between py-3">
                                        <dt class="text-gray-500 font-medium dark:text-gray-400">Teléfono</dt>
                                        <dd class="text-gray-900 dark:text-gray-200">{{ cliente.telefono }}</dd>
                                    </div>
                                    <div class="flex flex-col py-3">
                                        <dt class="text-gray-500 font-medium dark:text-gray-400">Email</dt>
                                        <dd class="text-gray-900 dark:text-gray-200 break-words mt-1">{{ cliente.email }}</dd>
                                    </div>
                                    <div class="flex flex-col py-3">
                                        <dt class="text-gray-500 font-medium dark:text-gray-400">Ubicación</dt>
                                        <dd class="text-gray-900 dark:text-gray-200 mt-1">{{ cliente.direccion }} <br> {{ cliente.ciudad }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Datos Adicionales (CRM) -->
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-hidden">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Información de Sistema</h3>
                            <div class="mt-4 space-y-4 text-sm">
                                <div>
                                    <span class="block text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wide">Fecha de Registro</span>
                                    <span class="block text-gray-900 dark:text-gray-200 mt-1">{{ formatDate(cliente.created_at) }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wide">Nacimiento / Constitución</span>
                                    <span class="block text-gray-900 dark:text-gray-200 mt-1">{{ formatDate(cliente.fecha_nacimiento) }}</span>
                                </div>
                                <div>
                                    <span class="block text-gray-500 dark:text-gray-400 text-xs font-semibold uppercase tracking-wide">Primer Contacto</span>
                                    <span class="block text-gray-900 dark:text-gray-200 mt-1">{{ formatDate(cliente.fecha_contacto) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Pestañas y Detalles Extendidos -->
                <div class="md:col-span-2 space-y-6">

                    <!-- Representante Legal (Si Aplica) -->
                    <div v-if="cliente.tipo_persona === 'juridica'" class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-hidden border-t-4 border-amber-500">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white mb-4">Representante Legal</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ cliente.rep_legal_nombre || 'No registrado' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Documento</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ cliente.rep_legal_documento || 'No registrado' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Teléfono</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ cliente.rep_legal_telefono || 'No registrado' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-gray-200">{{ cliente.rep_legal_email || 'No registrado' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-hidden">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white mb-4">Observaciones</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ cliente.observaciones || 'No hay observaciones registradas para este cliente.' }}</p>
                        </div>
                    </div>

                    <!-- Gestión de Riesgos y Pólizas Protegidas -->
                    <div class="space-y-6">
                        <h3 class="text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest pl-1">Auditoría de Riesgos y Pólizas</h3>
                        
                        <div v-if="cliente.riesgos && cliente.riesgos.length > 0" class="space-y-4">
                            <div v-for="riesgo in cliente.riesgos" :key="riesgo.id" class="bg-white dark:bg-gray-800 ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden shadow-sm">
                                <!-- Cabecera del Riesgo -->
                                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center rotate-hover transition-all">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg flex items-center justify-center font-black text-xs ring-1 ring-blue-200">
                                            R-{{ riesgo.id }}
                                        </div>
                                        <div>
                                            <Link :href="route('riesgos.show', riesgo.id)" class="text-sm font-black text-gray-900 dark:text-white hover:text-blue-600 transition-colors">
                                                {{ riesgo.tipo_riesgo }}
                                            </Link>
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">{{ riesgo.identificador || 'Sin ID' }}</p>
                                        </div>
                                    </div>
                                    <Link :href="route('riesgos.show', riesgo.id)" class="text-xs font-black text-blue-600 uppercase tracking-widest hover:underline">Ver Detalle</Link>
                                </div>
                                
                                <!-- Pólizas Vinculadas al Riesgo -->
                                <div class="px-6 py-4">
                                    <div v-if="riesgo.polizas && riesgo.polizas.length > 0" class="space-y-4">
                                        <div v-for="poliza in riesgo.polizas" :key="poliza.id" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-emerald-50/30 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-800/50 gap-4 group hover:ring-2 hover:ring-emerald-400 transition-all">
                                            <div class="flex items-center gap-4">
                                                <div class="h-10 w-10 flex-shrink-0 bg-white ring-1 ring-gray-200 rounded-lg p-1 dark:bg-gray-700">
                                                    <img v-if="poliza.aseguradora.logo" :src="'/storage/' + poliza.aseguradora.logo" alt="" class="h-full w-full object-contain">
                                                    <span v-else class="h-full w-full flex items-center justify-center text-[10px] font-black uppercase text-gray-400">{{ poliza.aseguradora.nombre.substring(0,2) }}</span>
                                                </div>
                                                <div>
                                                    <Link :href="route('polizas.show', poliza.id)" class="text-xs font-black text-gray-900 dark:text-white group-hover:text-emerald-600 transition-colors uppercase">
                                                        {{ poliza.ramo.nombre }} #{{ poliza.numero_poliza }}
                                                    </Link>
                                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-tighter">{{ poliza.aseguradora.nombre }} | Vigencia: {{ formatDate(poliza.fin_vigencia) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-4 justify-between sm:justify-end">
                                                <span class="inline-flex items-center rounded-md bg-white px-2 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-gray-900 shadow-sm">
                                                    {{ poliza.estado }}
                                                </span>
                                                <Link :href="route('polizas.show', poliza.id)" class="p-2 text-emerald-600 hover:bg-emerald-100 rounded-lg dark:hover:bg-emerald-900/50 transition-colors">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="py-4 text-center border-2 border-dashed border-gray-100 dark:border-gray-700 rounded-xl">
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Este riesgo no posee pólizas activas vinculadas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="bg-white p-12 text-center rounded-2xl ring-1 ring-gray-200 dark:bg-gray-800/30 dark:ring-gray-700 border-2 border-dashed border-gray-100 dark:border-gray-700">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Sin Riesgos Registrados</h3>
                            <p class="mt-1 text-xs text-gray-500">Este cliente aún no tiene activos o riesgos vinculados en la base de datos.</p>
                            <div class="mt-6">
                                <Link :href="route('riesgos.create')" class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-xs font-black text-white shadow-sm hover:bg-blue-500 transition-all uppercase tracking-widest">
                                    Registrar Primer Riesgo
                                </Link>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
