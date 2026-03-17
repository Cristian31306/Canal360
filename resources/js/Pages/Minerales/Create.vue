<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    meses: Array,
    anio_actual: Number,
    minerales: Array,
});

const form = useForm({
    mes: new Date().getMonth() + 1,
    anio: props.anio_actual,
    precios: props.minerales.reduce((acc, m) => {
        acc[m.id] = '';
        return acc;
    }, {}),
});

const submit = () => {
    form.post(route('minerales.store'));
};
</script>

<template>
    <Head title="Nuevo Registro de Minerales" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('minerales.index')" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-amber-600 pl-3 dark:text-gray-100 uppercase">
                    Registrar Precios de Minerales
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
                                <div v-for="mineral in minerales" :key="mineral.id" 
                                    :class="[
                                        mineral.slug === 'oro' ? 'bg-amber-50 dark:bg-amber-900/10 border-amber-100 dark:border-amber-900/20' : 
                                        mineral.slug === 'plata' ? 'bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-700' :
                                        mineral.slug === 'platino' ? 'bg-indigo-50 dark:bg-indigo-900/10 border-indigo-100 dark:border-indigo-900/20' :
                                        'bg-gray-50 dark:bg-gray-900/50 border-gray-200 dark:border-gray-700',
                                        'p-6 rounded-2xl border'
                                    ]">
                                    <InputLabel :for="mineral.slug" :value="`Precio ${mineral.nombre} (COP)`" 
                                        :class="[
                                            mineral.slug === 'oro' ? 'text-amber-600' : 
                                            mineral.slug === 'plata' ? 'text-slate-500' :
                                            mineral.slug === 'platino' ? 'text-indigo-600' :
                                            'text-gray-600',
                                            'uppercase text-[10px] font-black tracking-widest mb-2'
                                        ]" />
                                    <TextInput :id="mineral.slug" v-model="form.precios[mineral.id]" type="number" step="0.01" 
                                        :class="[
                                            mineral.slug === 'oro' ? 'border-amber-200' : 
                                            mineral.slug === 'plata' ? 'border-slate-200' :
                                            mineral.slug === 'platino' ? 'border-indigo-200' :
                                            'border-gray-200',
                                            'mt-1 block w-full rounded-xl focus:ring-amber-500 font-bold'
                                        ]" placeholder="0.00" required />
                                    <div v-if="form.errors[`precios.${mineral.id}`]" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors[`precios.${mineral.id}`] }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-50 dark:border-gray-700 border-dashed">
                            <SecondaryButton @click="router.get(route('minerales.index'))" type="button" class="rounded-xl px-6">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton :disabled="form.processing" class="rounded-xl px-8 bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-500/20 uppercase tracking-widest text-xs font-bold">
                                Guardar Registro
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
