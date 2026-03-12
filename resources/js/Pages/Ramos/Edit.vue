<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    ramo: Object,
});

const form = useForm({
    nombre: props.ramo.nombre,
});

const submit = () => {
    form.put(route('ramos.update', props.ramo.id), {
        preserveScroll: true,
    });
};

const deleteRecord = () => {
    if (confirm('¿Estás seguro de que deseas eliminar este Ramo? Las pólizas asociadas podrían quedar huérfanas o generar errores.')) {
        router.delete(route('ramos.destroy', props.ramo.id));
    }
};
</script>

<template>
    <Head title="Editar Ramo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 w-full">
                <Link :href="route('ramos.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-amber-500 pl-3 dark:text-gray-100 flex items-center gap-3 overflow-hidden">
                        <span class="truncate">Editar Ramo: {{ props.ramo.nombre }}</span>
                    </h2>
                    
                    <button @click="deleteRecord" type="button" class="inline-flex items-center rounded-md bg-red-50 text-red-700 border border-red-200 px-3 py-2 text-sm font-semibold shadow-sm hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800 transition-colors">
                        <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar
                    </button>
                </div>
            </div>
        </template>

        <div class="max-w-3xl mx-auto py-6 space-y-6">
            <form @submit.prevent="submit">
                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl md:col-span-2 dark:bg-gray-800 dark:ring-gray-700">
                    <div class="px-4 py-6 sm:p-8">
                        <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                            
                            <div class="sm:col-span-6">
                                <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Nombre del Ramo <span class="text-red-500">*</span></label>
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 mb-2">Ej: Automóviles, Vida Individual, etc.</div>
                                <div class="mt-2">
                                    <input type="text" v-model="form.nombre" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                </div>
                                <p class="mt-1 text-xs text-red-500" v-if="form.errors.nombre">{{ form.errors.nombre }}</p>
                            </div>
                            
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-xl">
                        <Link :href="route('ramos.index')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:text-gray-700 transition-colors">Cancelar</Link>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-amber-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600 transition-all disabled:opacity-75 disabled:cursor-not-allowed flex items-center gap-2">
                            <span v-if="form.processing" class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status"></span>
                            Actualizar Ramo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
