<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    precio: Object,
    meses: Array
});

const form = useForm({
    mes: props.precio.mes,
    anio: props.precio.anio,
    oro: props.precio.oro,
    plata: props.precio.plata,
    platino: props.precio.platino,
});

const submit = () => {
    form.put(route('minerales.update', props.precio.id));
};

const getMesNombre = (mesId) => {
    return props.meses.find(m => m.id === mesId)?.nombre || '';
};
</script>

<template>
    <Head title="Editar Registro de Minerales" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('minerales.index')" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-amber-600 pl-3 dark:text-gray-100 uppercase">
                    Editar Registro: {{ getMesNombre(precio.mes) }} {{ precio.anio }}
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-xl shadow-amber-500/5 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                    <form @submit.prevent="submit" class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <InputLabel for="mes" value="Mes del Registro" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                <select id="mes" v-model="form.mes" 
                                    class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm transition-all text-sm py-3">
                                    <option v-for="mes in meses" :key="mes.id" :value="mes.id">
                                        {{ mes.nombre }}
                                    </option>
                                </select>
                                <div v-if="form.errors.mes" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.mes }}</div>
                            </div>

                            <div>
                                <InputLabel for="anio" value="Año" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                <TextInput id="anio" v-model="form.anio" type="number" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-amber-500" required />
                                <div v-if="form.errors.anio" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.anio }}</div>
                            </div>

                            <div class="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="p-6 bg-amber-50 dark:bg-amber-900/10 rounded-2xl border border-amber-100 dark:border-amber-900/20">
                                    <InputLabel for="oro" value="Precio Oro (COP)" class="uppercase text-[10px] font-black tracking-widest text-amber-600 mb-2" />
                                    <TextInput id="oro" v-model="form.oro" type="number" step="0.01" class="mt-1 block w-full rounded-xl border-amber-200 focus:ring-amber-500 font-bold" placeholder="0.00" required />
                                    <div v-if="form.errors.oro" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.oro }}</div>
                                </div>

                                <div class="p-6 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-700">
                                    <InputLabel for="plata" value="Precio Plata (COP)" class="uppercase text-[10px] font-black tracking-widest text-slate-500 mb-2" />
                                    <TextInput id="plata" v-model="form.plata" type="number" step="0.01" class="mt-1 block w-full rounded-xl border-slate-200 focus:ring-slate-500 font-bold" placeholder="0.00" required />
                                    <div v-if="form.errors.plata" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.plata }}</div>
                                </div>

                                <div class="p-6 bg-indigo-50 dark:bg-indigo-900/10 rounded-2xl border border-indigo-100 dark:border-indigo-900/20">
                                    <InputLabel for="platino" value="Precio Platino (COP)" class="uppercase text-[10px] font-black tracking-widest text-indigo-600 mb-2" />
                                    <TextInput id="platino" v-model="form.platino" type="number" step="0.01" class="mt-1 block w-full rounded-xl border-indigo-200 focus:ring-indigo-500 font-bold" placeholder="0.00" required />
                                    <div v-if="form.errors.platino" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.platino }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-50 dark:border-gray-700 border-dashed">
                            <SecondaryButton @click="router.get(route('minerales.index'))" type="button" class="rounded-xl px-6">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton :disabled="form.processing" class="rounded-xl px-8 bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-500/20 uppercase tracking-widest text-xs font-bold">
                                Actualizar Registro
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
