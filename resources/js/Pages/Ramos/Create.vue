<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nombre: '',
});

const submit = () => {
    form.post(route('ramos.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nuevo Ramo" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 w-full">
                <Link :href="route('ramos.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-blue-600 pl-3 dark:text-gray-100 flex items-center gap-3 overflow-hidden">
                        <span class="truncate">Nuevo Ramo</span>
                    </h2>
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
                                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 mb-2">Ej: Vehículos, Vida Individual, Responsabilidad Civil, etc.</div>
                                <div class="mt-2">
                                    <input type="text" v-model="form.nombre" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                </div>
                                <p class="mt-1 text-xs text-red-500" v-if="form.errors.nombre">{{ form.errors.nombre }}</p>
                            </div>
                            
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 py-4 sm:px-8 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-xl">
                        <Link :href="route('ramos.index')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:text-gray-700 transition-colors">Cancelar</Link>
                        <button type="submit" :disabled="form.processing" class="rounded-md bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all disabled:opacity-75 disabled:cursor-not-allowed flex items-center gap-2">
                            <span v-if="form.processing" class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spin_1.5s_linear_infinite]" role="status"></span>
                            Guardar Ramo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
