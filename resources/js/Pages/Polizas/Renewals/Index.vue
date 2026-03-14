<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    polizas: Object,
    filters: Object,
    aseguradoras: Array,
    ramos: Array,
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

const changeTab = (tab) => {
    currentTab.value = tab;
    router.get(route('polizas.renewals'), { tab: tab }, { preserveState: true, preserveScroll: true });
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
    if (confirm('¿Confirmas que esta póliza ha sido enviada a la aseguradora?')) {
        router.post(route('polizas.send-to-insurance', poliza.id));
    }
};

const cancelRenewal = (poliza) => {
    if (confirm('¿Estás seguro de cancelar este trámite? El registro se eliminará y la póliza original volverá a aparecer en "Por Liquidar".')) {
        router.delete(route('polizas.cancel-renewal', poliza.id));
    }
};

const openFinalizeModal = (poliza) => {
    selectedPoliza.value = poliza;
    finalizeForm.reset();
    finalizeForm.clearErrors();
    
    // El usuario pidió no llenar ningún campo, pero conservamos el numero_poliza base si es útil
    // No, dice "todo para llenar", así que reseteamos todo.
    
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
                <div class="p-2 bg-blue-600 rounded-lg shadow-lg shadow-blue-500/30">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight uppercase">
                    Renovaciones <span class="text-blue-600">&</span> Trámites
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- TABS PIPELINE -->
                <div class="mb-8 p-1 bg-slate-200 dark:bg-slate-800 rounded-2xl flex gap-1 shadow-inner overflow-x-auto">
                    <button @click="changeTab('upcoming')"
                        :class="[currentTab === 'upcoming' ? 'bg-white dark:bg-slate-700 text-blue-600 shadow-md scale-[1.02]' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300', 'flex-1 min-w-[150px] py-3 px-4 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all duration-200 flex items-center justify-center gap-2']">
                        <span class="w-2 h-2 rounded-full bg-blue-500" v-if="currentTab === 'upcoming'"></span>
                        1. Por Liquidar
                    </button>
                    <button @click="changeTab('liquidated')"
                        :class="[currentTab === 'liquidated' ? 'bg-white dark:bg-slate-700 text-blue-600 shadow-md scale-[1.02]' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300', 'flex-1 min-w-[150px] py-3 px-4 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all duration-200 flex items-center justify-center gap-2']">
                        <span class="w-2 h-2 rounded-full bg-blue-500" v-if="currentTab === 'liquidated'"></span>
                        2. Liquidadas
                    </button>
                    <button @click="changeTab('processing')"
                        :class="[currentTab === 'processing' ? 'bg-white dark:bg-slate-700 text-blue-600 shadow-md scale-[1.02]' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300', 'flex-1 min-w-[150px] py-3 px-4 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all duration-200 flex items-center justify-center gap-2']">
                        <span class="w-2 h-2 rounded-full bg-blue-500" v-if="currentTab === 'processing'"></span>
                        3. En Proceso
                    </button>
                    <button @click="changeTab('lost')"
                        :class="[currentTab === 'lost' ? 'bg-white dark:bg-slate-700 text-red-600 shadow-md scale-[1.02]' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300', 'flex-1 min-w-[150px] py-3 px-4 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all duration-200 flex items-center justify-center gap-2']">
                        <span class="w-2 h-2 rounded-full bg-red-500" v-if="currentTab === 'lost'"></span>
                        4. No Renovadas
                    </button>
                </div>

                <!-- LISTADO -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-3xl border border-gray-100 dark:border-gray-700 transition-all duration-300">
                    <div class="p-6">
                        <div v-if="polizas.data.length > 0" class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Póliza / Cliente</th>
                                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Aseguradora / Ramo</th>
                                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Valores / Tasa</th>
                                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Vencimiento</th>
                                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                                    <tr v-for="poliza in polizas.data" :key="poliza.id" class="group hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-colors">
                                        <td class="px-4 py-5">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-slate-800 dark:text-white tracking-tight leading-tight uppercase group-hover:text-blue-600 transition-colors">
                                                    {{ poliza.numero_poliza }}
                                                </span>
                                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wide truncate max-w-[200px]">
                                                    {{ poliza.clientes[0]?.nombre_razon_social }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-5">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase">{{ poliza.aseguradora.nombre }}</span>
                                                <span class="text-[10px] font-medium text-slate-400 uppercase">{{ poliza.ramo.nombre }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-5">
                                            <div class="flex flex-col">
                                                <div class="flex items-center gap-1">
                                                    <span class="text-xs font-black text-slate-700 dark:text-slate-200">
                                                        {{ formatCurrency(poliza.prima_total) }}
                                                    </span>
                                                    <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 px-1.5 rounded">
                                                        {{ poliza.tasa }}%
                                                    </span>
                                                </div>
                                                <span class="text-[10px] font-bold text-slate-400 uppercase">
                                                    Aseg: {{ formatCurrency(poliza.valor_asegurado) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-5">
                                            <div class="flex flex-col">
                                                <span :class="[
                                                    new Date(poliza.fin_vigencia) < new Date() ? 'text-red-600' : 'text-slate-700',
                                                    'text-xs font-black tracking-tight'
                                                ]">
                                                    {{ formatDate(poliza.fin_vigencia) }}
                                                </span>
                                                <span class="text-[10px] uppercase font-bold text-slate-400">
                                                    {{ new Date(poliza.fin_vigencia) < new Date() ? 'Vencida' : 'Vence pronto' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-5 text-right">
                                            <div class="flex justify-end gap-2">
                                                <!-- Acción según pestaña -->
                                                <button v-if="currentTab === 'upcoming' || currentTab === 'lost'"
                                                    @click="openLiquidateModal(poliza)"
                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                                                    Liquidar
                                                </button>

                                                <button v-if="currentTab === 'liquidated'"
                                                    @click="sendToInsurance(poliza)"
                                                    class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-amber-500/20 active:scale-95">
                                                    Enviar a Aseguradora
                                                </button>

                                                <button v-if="currentTab === 'processing'"
                                                    @click="openFinalizeModal(poliza)"
                                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-green-500/20 active:scale-95">
                                                    Expedir / Finalizar
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

                        <div class="mt-8">
                            <Pagination :links="polizas.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL LIQUIDAR -->
        <Modal :show="showLiquidateModal" @close="showLiquidateModal = false" max-width="2xl">
            <div class="p-8">
                <div class="flex items-center gap-4 mb-8">
                    <div class="p-3 bg-blue-100 rounded-2xl text-blue-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Liquidar Renovación</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-none mt-1">Ingresa la guía para este año</p>
                    </div>
                </div>

                <!-- Resumen Póliza Actual -->
                <div v-if="selectedPoliza" class="mb-6 grid grid-cols-2 gap-4 p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Póliza Actual</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white">{{ selectedPoliza.numero_poliza }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Vencimiento</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white">{{ formatDate(selectedPoliza.fin_vigencia) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Valor Asegurado</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white">{{ formatCurrency(selectedPoliza.valor_asegurado) }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Prima Total</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-white">{{ formatCurrency(selectedPoliza.prima_total) }}</span>
                    </div>
                </div>

                <form @submit.prevent="submitLiquidation" class="space-y-6">
                    <div>
                        <InputLabel for="liquidacion" value="Cálculos / Liquidación de este Año" class="uppercase text-[10px] font-black tracking-widest text-slate-400 mb-2" />
                        <textarea
                            id="liquidacion"
                            v-model="liquidateForm.liquidacion"
                            rows="4"
                            class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-blue-500 transition-all font-mono text-xs p-4 text-slate-700 dark:text-slate-300 shadow-sm"
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
                        <SecondaryButton @click="showLiquidateModal = false" class="rounded-xl px-6">Cancelar</SecondaryButton>
                        <PrimaryButton :disabled="liquidateForm.processing" class="rounded-xl shadow-lg shadow-blue-500/20 px-8 uppercase tracking-widest text-xs font-black">
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
                    <div class="p-3 bg-green-100 rounded-2xl text-green-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Finalizar Expedición</h2>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-none mt-1">Confirma los datos finales de la póliza</p>
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
                        <SecondaryButton @click="showFinalizeModal = false" class="rounded-xl px-6">Cancelar</SecondaryButton>
                        <PrimaryButton :disabled="finalizeForm.processing" class="rounded-xl shadow-lg shadow-green-500/20 px-8 bg-green-600 hover:bg-green-700 uppercase tracking-widest text-xs font-black">
                            Expedir Póliza
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
