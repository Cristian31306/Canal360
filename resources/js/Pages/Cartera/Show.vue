<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    cartera: Object
});

const showAbonoForm = ref(false);

const form = useForm({
    monto: props.cartera.saldo_pendiente,
    fecha_pago: new Date().toISOString().split('T')[0],
    metodo_pago: 'transferencia',
    referencia: '',
    observaciones: '',
});

const submitAbono = () => {
    form.post(route('cartera.abonos.store', props.cartera.id), {
        onSuccess: () => {
            showAbonoForm.value = false;
            form.reset();
        },
    });
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
    return new Intl.DateTimeFormat('es-CO', { year: 'numeric', month: 'long', day: 'numeric' }).format(new Date(dateString));
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

const confirmation = ref({
    show: false,
    title: '',
    message: '',
    type: 'danger',
    confirmLabel: 'Confirmar',
    callback: null
});

const deleteCartera = () => {
    confirmation.value = {
        show: true,
        title: 'Eliminar Registro de Cartera',
        message: '¿Estás seguro de que deseas eliminar este registro de cartera? Esta acción solo es posible si no hay abonos registrados.',
        type: 'danger',
        confirmLabel: 'Eliminar',
        callback: () => router.delete(route('cartera.destroy', props.cartera.id))
    };
};

const deleteAbono = (id) => {
    confirmation.value = {
        show: true,
        title: 'Eliminar Abono',
        message: '¿Estás seguro de que deseas eliminar este abono? El saldo de la cartera se actualizará automáticamente.',
        type: 'danger',
        confirmLabel: 'Eliminar',
        callback: () => router.delete(route('cartera.abonos.destroy', id))
    };
};

const goBack = () => window.history.back();
</script>

<template>
    <Head title="Detalle de Cartera" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <button @click="goBack" type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </button>
                <h2 class="text-xl font-black text-gray-900 border-l-4 border-blue-600 pl-3 dark:text-gray-100 uppercase tracking-widest">
                    Gestión de Cobro: #{{ cartera.poliza.numero_poliza }}
                </h2>
                <div class="ml-auto" v-if="cartera.abonos.length === 0">
                    <button @click="deleteCartera" class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-red-100 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Eliminar Registro
                    </button>
                </div>
            </div>
        </template>

        <div class="py-4 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            
            <!-- Resumen de Deuda -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-gray-700 p-6 border-t-8 border-blue-600">
                    <div class="flex flex-col sm:flex-row justify-between items-start gap-6">
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Póliza y Cliente</span>
                            <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">#{{ cartera.poliza.numero_poliza }}</h3>
                            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mt-1 uppercase">{{ cartera.poliza.clientes[0]?.nombre_razon_social }}</p>
                            
                            <div class="mt-6 grid grid-cols-2 gap-x-12 gap-y-4">
                                <div>
                                    <span class="text-[10px] font-black uppercase text-gray-400">Fecha Límite</span>
                                    <p class="text-sm font-bold dark:text-white">{{ formatDate(cartera.fecha_limite) }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] font-black uppercase text-gray-400">Días en Cartera</span>
                                    <p class="text-sm font-bold text-rose-600">{{ cartera.dias_en_cartera }} días</p>
                                </div>
                                <div>
                                    <span class="text-[10px] font-black uppercase text-gray-400">Aseguradora</span>
                                    <p class="text-sm font-bold dark:text-white">{{ cartera.poliza.aseguradora.nombre }}</p>
                                </div>
                                <div>
                                    <span class="text-[10px] font-black uppercase text-gray-400">Ramo</span>
                                    <p class="text-sm font-bold dark:text-white">{{ cartera.poliza.ramo.nombre }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-blue-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-blue-100 dark:border-blue-900/30 flex flex-col items-center justify-center min-w-[200px]">
                            <span class="text-[10px] font-black uppercase text-blue-600 mb-2">Estado de Cuenta</span>
                            <span :class="[getStatusClass(cartera.estado), 'rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest mb-6 ring-1']">
                                {{ cartera.estado.replace('_', ' ') }}
                            </span>
                            <span class="text-[10px] font-black uppercase text-gray-400">Saldo Pendiente</span>
                            <span class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ formatCurrency(cartera.saldo_pendiente) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Resumen Financiero Rápido -->
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-gray-700 p-6 flex flex-col justify-center gap-6">
                    <div>
                        <span class="text-[10px] font-black uppercase text-gray-400">Valor Total Póliza</span>
                        <p class="text-xl font-black text-gray-900 dark:text-white">{{ formatCurrency(cartera.valor_a_pagar) }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-gray-400">Total Abonado</span>
                        <p class="text-xl font-black text-emerald-600">{{ formatCurrency(cartera.total_abonado) }}</p>
                    </div>
                    <button @click="showAbonoForm = true" v-if="cartera.saldo_pendiente > 0" 
                        class="w-full bg-emerald-600 text-white rounded-xl py-4 font-black uppercase text-xs tracking-widest hover:bg-emerald-500 transition-all shadow-lg hover:shadow-emerald-200 shadow-emerald-700/20">
                        Registrar Abono
                    </button>
                </div>
            </div>

            <!-- Historial de Abonos -->
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-900/20">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Historial de Pagos / Abonos</h3>
                    <div class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-xs">
                        {{ cartera.abonos.length }}
                    </div>
                </div>
                <div class="p-0">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50/30 dark:bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Fecha Pago</th>
                                <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Método</th>
                                <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest text-right">Monto</th>
                                <th class="px-6 py-3 text-left text-[10px] font-black text-gray-500 uppercase tracking-widest">Referencia / Observación</th>
                                <th class="px-6 py-3 text-center text-[10px] font-black text-gray-500 uppercase tracking-widest">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="abono in cartera.abonos" :key="abono.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-900/20 transition-colors">
                                <td class="px-6 py-3 whitespace-nowrap text-sm font-bold dark:text-white">{{ formatDate(abono.fecha_pago) }}</td>
                                <td class="px-6 py-3 whitespace-nowrap"><span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-lg text-[10px] font-black uppercase text-gray-600 dark:text-gray-300 ring-1 ring-gray-200">{{ abono.metodo_pago }}</span></td>
                                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-black text-emerald-600">{{ formatCurrency(abono.monto) }}</td>
                                <td class="px-6 py-3 text-xs text-gray-500 dark:text-gray-400 italic">
                                    {{ abono.referencia || 'N/A' }}
                                    <p v-if="abono.observaciones" class="mt-1 opacity-70">{{ abono.observaciones }}</p>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <button @click="deleteAbono(abono.id)" class="text-gray-400 hover:text-red-500 transition-colors">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="cartera.abonos.length === 0">
                                <td colspan="4" class="px-8 py-12 text-center text-gray-400">
                                    <p class="text-[10px] font-black uppercase tracking-widest">No se han registrado pagos aún para esta póliza</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal / Formulario de Abono -->
            <div v-if="showAbonoForm" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden ring-1 ring-white/10">
                    <div class="px-8 py-6 bg-emerald-600 text-white flex justify-between items-center">
                        <h3 class="text-xl font-black uppercase tracking-widest">Registrar Pago</h3>
                        <button @click="showAbonoForm = false" class="hover:rotate-90 transition-transform">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="submitAbono" class="p-8 space-y-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Monto a Pagar</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                    <input v-model="form.monto" type="number" step="0.01" required
                                        class="w-full pl-7 pr-4 py-3 rounded-2xl border-gray-200 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                                </div>
                                <p v-if="form.errors.monto" class="mt-1 text-[10px] text-rose-500 font-bold uppercase">{{ form.errors.monto }}</p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Fecha Recibo</label>
                                <input v-model="form.fecha_pago" type="date" required
                                    class="w-full rounded-2xl border-gray-200 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Método de Pago</label>
                            <select v-model="form.metodo_pago" class="w-full rounded-2xl border-gray-200 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white">
                                <option value="transferencia">Transferencia Bancaria</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="consignacion">Consignación</option>
                                <option value="tarjeta">Tarjeta Débito/Crédito</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Referencia (Opcional)</label>
                            <input v-model="form.referencia" type="text" placeholder="Ej: Comprobante #123456"
                                class="w-full rounded-2xl border-gray-200 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white" />
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase mb-1">Observaciones</label>
                            <textarea v-model="form.observaciones" rows="2"
                                class="w-full rounded-2xl border-gray-200 focus:ring-emerald-500 focus:border-emerald-500 dark:bg-gray-900 dark:border-gray-700 dark:text-white"></textarea>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="button" @click="showAbonoForm = false" 
                                class="flex-1 px-4 py-3 border-2 border-gray-100 rounded-2xl text-xs font-black uppercase text-gray-400 hover:bg-gray-50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="form.processing"
                                class="flex-[2] bg-emerald-600 text-white rounded-2xl py-3 font-black uppercase text-xs tracking-widest hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-700/20 disabled:opacity-50">
                                Confirmar Pago
                            </button>
                        </div>
                    </form>
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
