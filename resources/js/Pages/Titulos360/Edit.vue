<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    titulo: Object,
    aseguradoras: Array,
});

const form = useForm({
    par: props.titulo.par || '',
    titulo: props.titulo.titulo || '',
    nombre: props.titulo.nombre || '',
    minerales: props.titulo.minerales || '',
    departamento: props.titulo.departamento || '',
    municipio: props.titulo.municipio || '',
    etapa: props.titulo.etapa || '',
    fecha_inicio: props.titulo.fecha_inicio || '',
    fecha_fin: props.titulo.fecha_fin || '',
    aseguradora_id: props.titulo.aseguradora_id || '',
    aseguradora_nombre: props.titulo.aseguradora_nombre || '',
    valor_asegurado: props.titulo.valor_asegurado || '',
    correo: props.titulo.correo || '',
    celular: props.titulo.celular || '',
    asesores: props.titulo.asesores || '',
});

const submit = () => {
    form.put(route('titulos-360.update', props.titulo.id));
};

const formatDateLong = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('es-CO', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const goBack = () => window.history.back();
</script>

<template>
    <Head :title="'Editar Título ' + titulo.titulo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <button @click="goBack" type="button" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <div class="flex flex-col">
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-amber-600 pl-3 dark:text-gray-100 uppercase">
                        Editar Título <span class="text-amber-600">{{ titulo.titulo }}</span>
                    </h2>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-3 mt-1">
                        Última Actualización: <span class="text-amber-600/70">{{ formatDateLong(titulo.updated_at) }}</span>
                    </span>
                </div>
            </div>
        </template>

        <div class="py-4">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-xl shadow-amber-500/5 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                    <form @submit.prevent="submit" class="p-4 space-y-4">
                        <!-- Sección 1: Datos Básicos -->
                        <div class="space-y-4">
                            <h3 class="text-xs font-black text-amber-600 uppercase tracking-widest border-b border-amber-100 pb-2">Información Principal</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel for="titulo" value="Código de Título" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="titulo" v-model="form.titulo" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500" placeholder="Ej: ABC-123" required />
                                    <div v-if="form.errors.titulo" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.titulo }}</div>
                                </div>
                                <div>
                                    <InputLabel for="par" value="PAR" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="par" v-model="form.par" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500" placeholder="Ej: 4567" />
                                </div>
                                <div class="md:col-span-1">
                                    <InputLabel for="nombre" value="Nombre del Titular" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="nombre" v-model="form.nombre" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500" placeholder="Nombre completo o Empresa" required />
                                </div>
                                <div class="md:col-span-3">
                                    <InputLabel for="minerales" value="Minerales / Sustancia" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <textarea id="minerales" v-model="form.minerales" rows="2" class="mt-1 block w-full rounded-xl border-gray-200 dark:bg-gray-900 dark:text-white dark:border-gray-700 focus:ring-amber-500" placeholder="Oro, Plata, Cobre..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 2: Ubicación y Etapa -->
                        <div class="space-y-4">
                            <h3 class="text-xs font-black text-amber-600 uppercase tracking-widest border-b border-amber-100 pb-2">Ubicación y Estado</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel for="departamento" value="Departamento" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="departamento" v-model="form.departamento" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500" />
                                </div>
                                <div>
                                    <InputLabel for="municipio" value="Municipio" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="municipio" v-model="form.municipio" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500" />
                                </div>
                                <div>
                                    <InputLabel for="etapa" value="Etapa" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="etapa" v-model="form.etapa" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500" placeholder="Ej: Explotación" />
                                </div>
                            </div>
                        </div>

                        <!-- Sección 3: Aseguradora y Valores -->
                        <div class="space-y-4">
                            <h3 class="text-xs font-black text-amber-600 uppercase tracking-widest border-b border-amber-100 pb-2">Seguros y Vigencia</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="md:col-span-2">
                                    <InputLabel for="aseguradora_id" value="Aseguradora (Sistema)" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <select id="aseguradora_id" v-model="form.aseguradora_id" class="mt-1 block w-full rounded-xl border-gray-200 dark:bg-gray-900 dark:text-white dark:border-gray-700 focus:ring-amber-500 text-sm py-2.5">
                                        <option value="">Seleccione o deje vacío...</option>
                                        <option v-for="aseg in aseguradoras" :key="aseg.id" :value="aseg.id">{{ aseg.nombre }}</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <InputLabel for="aseguradora_nombre" value="Aseguradora (Texto Libre)" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="aseguradora_nombre" v-model="form.aseguradora_nombre" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500" placeholder="Si no está en el sistema" />
                                </div>
                                <div>
                                    <InputLabel for="valor_asegurado" value="Valor Asegurado" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="valor_asegurado" v-model="form.valor_asegurado" type="number" step="0.01" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500 text-sm font-bold" placeholder="0.00" />
                                </div>
                                <div>
                                    <InputLabel for="fecha_inicio" value="Inicio Vigencia" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="fecha_inicio" v-model="form.fecha_inicio" type="date" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500 text-sm" />
                                </div>
                                <div>
                                    <InputLabel for="fecha_fin" value="Fin Vigencia" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="fecha_fin" v-model="form.fecha_fin" type="date" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500 text-sm" />
                                </div>
                                <div class="bg-amber-50 dark:bg-amber-900/10 p-4 rounded-xl border border-amber-100 dark:border-amber-900/20 flex flex-col justify-center">
                                    <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Detección Inteligente</span>
                                    <p class="text-[9px] text-amber-400 font-bold leading-tight">Estado: <span class="uppercase">{{ titulo.cliente_canal ? 'Cliente Canal' : 'Externo' }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 4: Contacto y Otros -->
                        <div class="space-y-4">
                            <h3 class="text-xs font-black text-amber-600 uppercase tracking-widest border-b border-amber-100 pb-2">Contacto y Observaciones</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="correo" value="Correos Electrónicos" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <textarea id="correo" v-model="form.correo" rows="2" class="mt-1 block w-full rounded-xl border-gray-200 dark:bg-gray-900 dark:text-white dark:border-gray-700 focus:ring-amber-500" placeholder="email1@ejemplo.com, email2@ejemplo.com..."></textarea>
                                </div>
                                <div>
                                    <InputLabel for="celular" value="Números de Celular" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <textarea id="celular" v-model="form.celular" rows="2" class="mt-1 block w-full rounded-xl border-gray-200 dark:bg-gray-900 dark:text-white dark:border-gray-700 focus:ring-amber-500" placeholder="300..., 311..., 320..."></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <InputLabel for="asesores" value="Asesores Encargados" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                    <TextInput id="asesores" v-model="form.asesores" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500" placeholder="Nombres de los asesores" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-50 dark:border-gray-700 border-dashed">
                            <SecondaryButton @click="goBack" type="button" class="rounded-xl px-8 uppercase text-xs font-bold tracking-widest">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton :disabled="form.processing" class="rounded-xl px-12 bg-amber-600 hover:bg-amber-500 shadow-xl shadow-amber-500/20 uppercase text-xs font-bold tracking-widest">
                                Actualizar Título
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
