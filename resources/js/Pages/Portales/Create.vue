<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    aseguradoras: Array
});

const form = useForm({
    nombre: '',
    usuario: '',
    password: '',
    aseguradora_id: '',
    link: '',
    notas: '',
});

const submit = () => {
    form.post(route('portales.store'));
};
</script>

<template>
    <Head title="Nuevo Portal" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('portales.index')" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-indigo-600 pl-3 dark:text-gray-100 uppercase">
                    Registrar Nuevo Portal
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-xl shadow-indigo-500/5 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                    <form @submit.prevent="submit" class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2">
                                <InputLabel for="nombre" value="Nombre del Portal" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                <TextInput id="nombre" v-model="form.nombre" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-indigo-500" placeholder="Ej: Portal Intermediarios Sura, Oficina Virtual..." required />
                                <div v-if="form.errors.nombre" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.nombre }}</div>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <InputLabel for="aseguradora_id" value="Aseguradora Asociada (Opcional)" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                <select id="aseguradora_id" v-model="form.aseguradora_id" 
                                    class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all text-sm py-3">
                                    <option value="">Portal Propio de la Agencia</option>
                                    <option v-for="aseguradora in aseguradoras" :key="aseguradora.id" :value="aseguradora.id">
                                        {{ aseguradora.nombre }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <InputLabel for="usuario" value="Usuario / Login" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                <TextInput id="usuario" v-model="form.usuario" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-indigo-500" required />
                                <div v-if="form.errors.usuario" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.usuario }}</div>
                            </div>

                            <div>
                                <InputLabel for="password" value="Contraseña / Clave" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                <TextInput id="password" v-model="form.password" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-indigo-500" required />
                                <div v-if="form.errors.password" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.password }}</div>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <InputLabel for="link" value="Enlace de Acceso (URL)" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                <TextInput id="link" v-model="form.link" type="url" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-indigo-500" placeholder="https://..." />
                                <div v-if="form.errors.link" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.link }}</div>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <InputLabel for="notas" value="Notas / Observaciones" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                <textarea id="notas" v-model="form.notas" rows="3"
                                    class="mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-all text-sm"
                                    placeholder="Instrucciones especiales de ingreso..."></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-50 dark:border-gray-700 border-dashed">
                            <SecondaryButton @click="router.get(route('portales.index'))" type="button" class="rounded-xl px-6">
                                Cancelar
                            </SecondaryButton>
                            <PrimaryButton :disabled="form.processing" class="rounded-xl px-8 bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-500/20 uppercase tracking-widest text-xs font-bold">
                                Guardar Portal
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
