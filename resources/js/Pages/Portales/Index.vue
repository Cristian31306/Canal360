<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    portales: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');

// Diálogo de confirmación
const confirmation = ref({
    show: false,
    title: '',
    message: '',
    type: 'danger',
    confirmLabel: 'Confirmar',
    callback: null
});

const page = usePage();

onMounted(() => {
    if (page.props.flash?.success) showAlert('Éxito', page.props.flash.success, 'success');
});

const showAlert = (title, message, type = 'success') => {
    confirmation.value = {
        show: true,
        title,
        message,
        type,
        confirmLabel: 'Entendido',
        callback: null
    };
};

const handleSearch = () => {
    router.get(route('portales.index'), { search: searchQuery.value }, { preserveState: true, replace: true });
};

const clearSearch = () => {
    searchQuery.value = '';
    handleSearch();
};

const deletePortal = (portal) => {
    confirmation.value = {
        show: true,
        title: 'Eliminar Portal',
        message: `¿Estás seguro de que deseas eliminar el portal "${portal.nombre}"? Esta acción no se puede deshacer.`,
        type: 'danger',
        confirmLabel: 'Eliminar',
        callback: () => router.delete(route('portales.destroy', portal.id))
    };
};

const getInitials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(n => n.charAt(0)).slice(0, 2).join('').toUpperCase();
};
</script>

<template>
    <Head title="Portales de la Agencia" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-500/30">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-indigo-600 pl-3 dark:text-gray-100 uppercase">
                        Portales <span class="text-indigo-600">&</span> Credenciales
                    </h2>
                </div>
                <Link
                    :href="route('portales.create')"
                    class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-xl shadow-indigo-500/20 hover:bg-indigo-500 hover:-translate-y-0.5 transition-all outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                >
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Nuevo Portal
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Toolbar -->
                <div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 w-full">
                        <form @submit.prevent="handleSearch" class="flex gap-2 w-full">
                            <div class="relative rounded-md shadow-sm flex-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" v-model="searchQuery"
                                    class="block w-full rounded-md border-0 py-2.5 pl-10 text-sm text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 dark:bg-gray-900 dark:text-white dark:ring-gray-700 shadow-sm transition-all"
                                    placeholder="Buscar por nombre, usuario o aseguradora...">
                            </div>
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 transition-colors">
                                Buscar
                            </button>
                            <button v-if="filters?.search" type="button" @click="clearSearch"
                                class="inline-flex items-center justify-center rounded-md bg-white px-3 text-red-600 ring-1 ring-inset ring-gray-300 hover:bg-red-50 dark:bg-gray-700 dark:ring-gray-600 transition-colors">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Crids de Portales -->
                <div v-if="portales.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="portal in portales.data" :key="portal.id" 
                        class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group">
                        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-50 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center font-bold">
                                    {{ getInitials(portal.nombre) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ portal.nombre }}</h3>
                                    <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-wider">{{ portal.aseguradora?.nombre || 'Portal Propio' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Usuario</label>
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <span class="text-sm font-mono text-gray-700 dark:text-gray-300">{{ portal.usuario }}</span>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Contraseña</label>
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-100 dark:border-gray-700">
                                    <span class="text-sm font-mono text-gray-700 dark:text-gray-300">{{ portal.password }}</span>
                                </div>
                            </div>
                            
                            <div v-if="portal.link" class="pt-2">
                                <a :href="portal.link" target="_blank" 
                                    class="inline-flex items-center gap-2 text-xs font-bold text-indigo-600 hover:text-indigo-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 00-2 2v4a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Ir al portal
                                </a>
                            </div>

                            <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-50 dark:border-gray-700 border-dashed">
                                <Link :href="route('portales.edit', portal.id)" class="p-2 text-gray-400 hover:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M18.364 5.636l-3.536 3.536m0 0l3.536 3.536m-3.536-3.536L15 4" />
                                    </svg>
                                </Link>
                                <button @click="deletePortal(portal)" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-20 flex flex-col items-center">
                    <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center mb-4 text-indigo-300">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-700 dark:text-white uppercase tracking-tight">Sin portales registrados</h3>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Empieza agregando las credenciales de tus herramientas.</p>
                </div>

                <div class="mt-8">
                    <Pagination :links="portales.links" />
                </div>
            </div>
        </div>

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
    </AuthenticatedLayout>
</template>
