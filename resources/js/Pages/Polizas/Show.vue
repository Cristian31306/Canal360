<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    poliza: Object,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-CO', { year: 'numeric', month: 'long', day: 'numeric' }).format(date);
};

const getStatusClass = (estado) => {
    const classes = {
        'vigente': 'bg-green-50 text-green-700 ring-green-600/20',
        'vencida': 'bg-red-50 text-red-700 ring-red-600/20',
        'renovada': 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'cancelada': 'bg-gray-50 text-gray-700 ring-gray-600/20',
    };
    return classes[estado] || 'bg-gray-50 text-gray-700 ring-gray-600/20';
};
</script>

<template>

    <Head title="Detalle de Póliza" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 w-full">
                <Link :href="route('polizas.index')"
                    class="text-gray-500 hover:text-gray-700 transition-colors flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">
                    <h2
                        class="text-2xl font-black text-gray-900 border-l-4 border-emerald-600 pl-3 dark:text-gray-100 uppercase tracking-tighter">
                        Póliza #{{ poliza.numero_poliza }}
                    </h2>
                    <div class="flex items-center gap-3">
                        <Link :href="route('polizas.edit', poliza.id)"
                            class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 transition-all">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                <path
                                    d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0010 3H4.75A2.75 2.75 0 002 5.75v9.5A2.75 2.75 0 004.75 18h9.5A2.75 2.75 0 0017 15.25V10a.75.75 0 00-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5z" />
                            </svg>
                            Editar Expediente
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-6xl mx-auto py-10 px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- GRID DE STATUS RAPIDO -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-2xl ring-1 ring-gray-100 dark:ring-gray-700 shadow-sm">
                    <dt class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Estado Vigencia</dt>
                    <dd :class="getStatusClass(poliza.estado)"
                        class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-black uppercase tracking-widest ring-1 ring-inset">
                        {{ poliza.estado }}
                    </dd>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-2xl ring-1 ring-gray-100 dark:ring-gray-700 shadow-sm">
                    <dt class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Fin de Vigencia</dt>
                    <dd class="text-sm font-bold text-gray-900 dark:text-white">{{ formatDate(poliza.fin_vigencia) }}
                    </dd>
                </div>
                <div
                    class="bg-white dark:bg-gray-800 p-6 rounded-2xl ring-1 ring-gray-100 dark:ring-gray-700 shadow-sm md:col-span-2">
                    <dt class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 text-emerald-600">
                        Prima Total
                    </dt>
                    <dd class="text-2xl font-black text-emerald-600">{{ formatCurrency(poliza.prima_total) }}</dd>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

                <!-- COLUMNA IZQUIERDA: DATOS TECNICOS -->
                <div class="lg:col-span-1 space-y-8">
                    <div class="bg-white dark:bg-gray-800 ring-1 ring-gray-900/5 rounded-2xl overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                Aseguradora &
                                Ramo</h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Compañía
                                </dt>
                                <dd class="flex items-center gap-3">
                                    <div
                                        class="h-10 w-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 font-bold text-xs ring-1 ring-indigo-100">
                                        {{ poliza.aseguradora.nombre.substring(0, 2).toUpperCase() }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{
                                        poliza.aseguradora.nombre
                                    }}</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Ramo
                                    Técnico</dt>
                                <dd class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ poliza.ramo.nombre }}
                                </dd>
                            </div>
                            <div class="pt-4 border-t border-gray-50 dark:border-gray-700">
                                <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Riesgo
                                    Cubierto
                                </dt>
                                <Link :href="route('riesgos.show', poliza.riesgo_id)"
                                    class="block p-3 bg-gray-50 dark:bg-gray-900/30 rounded-xl ring-1 ring-gray-100 dark:ring-gray-700 hover:ring-emerald-400 transition-all">
                                    <p class="text-xs font-black text-gray-900 dark:text-white uppercase">{{
                                        poliza.riesgo.tipo_riesgo }}</p>
                                    <p class="text-[10px] font-bold text-gray-400 mt-0.5 truncate">{{
                                        poliza.riesgo.identificador ||
                                        'Sin ID' }}</p>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 ring-1 ring-gray-900/5 rounded-2xl overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                Liquidación Técnica
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 font-bold uppercase tracking-tighter">Valor Asegurado</span>
                                <span class="font-black text-gray-900 dark:text-white">{{
                                    formatCurrency(poliza.valor_asegurado)
                                }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 font-bold uppercase tracking-tighter">Tasa Aplicada</span>
                                <span class="font-black text-blue-600 dark:text-blue-400">{{ Number(poliza.tasa).toFixed(1) }}%</span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 font-bold uppercase tracking-tighter">Prima Neta</span>
                                <span class="font-black text-gray-900 dark:text-white">{{
                                    formatCurrency(poliza.prima_antes_iva)
                                }}</span>
                            </div>
                            <div
                                class="pt-4 border-t border-gray-50 dark:border-gray-700 flex justify-between items-center">
                                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Total Prima Anual</span>
                                <span class="text-xl font-black text-emerald-600">{{ formatCurrency(poliza.prima_total)
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- COLUMNA DERECHA: CLIENTES Y CRONOGRAMA -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- PARTICIPANTES -->
                    <div class="bg-white dark:bg-gray-800 ring-1 ring-gray-900/5 rounded-2xl overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">
                                Participantes
                                de la Póliza</h3>
                        </div>
                        <div class="p-0">
                            <ul role="list" class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                <li v-for="cliente in poliza.clientes" :key="cliente.id"
                                    class="flex items-center justify-between p-6 hover:bg-gray-50/50 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 font-black uppercase text-[10px]">
                                            {{ cliente.pivot.rol.substring(0, 1) }}
                                        </div>
                                        <div>
                                            <Link :href="route('clientes.show', cliente.id)"
                                                class="text-sm font-black text-gray-900 dark:text-white hover:text-emerald-600 transition-colors">
                                                {{ cliente.nombre_razon_social }}
                                            </Link>
                                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-tight">{{
                                                cliente.tipo_documento }}: {{ cliente.numero_documento }}</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-[10px] font-black uppercase px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-md ring-1 ring-emerald-600/10">
                                        {{ cliente.pivot.rol }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- VIGENCIAS COMPLETA -->
                    <div class="bg-white dark:bg-gray-800 ring-1 ring-gray-900/5 rounded-2xl p-8 shadow-sm">
                        <h3
                            class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-8 text-center">
                            Cronograma de Vigencia</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                            <!-- Línea decorativa entre fechas -->
                            <div
                                class="hidden md:block absolute top-[22px] left-[15%] right-[15%] h-0.5 bg-gray-100 dark:bg-gray-700 -z-0">
                            </div>

                            <div class="relative z-10 text-center">
                                <div
                                    class="h-12 w-12 rounded-full bg-white dark:bg-gray-800 ring-4 ring-gray-50 dark:ring-gray-700 flex items-center justify-center mx-auto mb-4 border border-gray-200">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <p
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 font-bold">
                                    Expedición</p>
                                <p class="text-xs font-black text-gray-900 dark:text-white">{{
                                    formatDate(poliza.expedicion_fecha) }}</p>
                            </div>

                            <div class="relative z-10 text-center">
                                <div
                                    class="h-12 w-12 rounded-full bg-white dark:bg-gray-800 ring-4 ring-emerald-50 dark:ring-emerald-900/20 flex items-center justify-center mx-auto mb-4 border border-emerald-100">
                                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p
                                    class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1 font-bold">
                                    Inicio Cobertura</p>
                                <p class="text-xs font-black text-gray-900 dark:text-white">{{
                                    formatDate(poliza.inicio_vigencia) }}</p>
                            </div>

                            <div class="relative z-10 text-center">
                                <div
                                    class="h-12 w-12 rounded-full bg-white dark:bg-gray-800 ring-4 ring-red-50 dark:ring-red-900/20 flex items-center justify-center mx-auto mb-4 border border-red-100">
                                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-1 font-bold">
                                    Vencimiento Técnico</p>
                                <p class="text-xs font-black text-gray-900 dark:text-white">{{
                                    formatDate(poliza.fin_vigencia)
                                }}</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <!-- FOOTER INFO -->
            <div
                class="pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                <div class="flex gap-8">
                    <span>Creado: <span class="text-gray-600 dark:text-gray-400">{{ new
                        Date(poliza.created_at).toLocaleDateString() }}</span></span>
                    <span>Actualizado: <span class="text-gray-600 dark:text-gray-400">{{ new
                        Date(poliza.updated_at).toLocaleDateString() }}</span></span>
                </div>
                <span>Tasa registrada: {{ Number(poliza.tasa).toFixed(1) }}%</span>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
