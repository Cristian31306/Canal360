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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h2
                        class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-indigo-600 pl-3 dark:text-gray-100 uppercase">
                        Portales <span class="text-indigo-600">&</span> Credenciales
                    </h2>
                </div>
                <Link :href="route('portales.create')"
                    class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-xl shadow-indigo-500/20 hover:bg-indigo-500 hover:-translate-y-0.5 transition-all outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2">
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Nuevo Portal
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Toolbar -->
                <div
                    class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 w-full">
                        <form @submit.prevent="handleSearch" class="flex gap-2 w-full">
                            <div class="relative rounded-md shadow-sm flex-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z"
                                            clip-rule="evenodd" />
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
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Crids de Portales -->
                <div v-if="portales.data.length > 0"
                    class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Portal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aseguradora
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Contraseña
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Link</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="portal in portales.data" :key="portal.id"
                                class="hover:bg-gray-50 dark:hover:bg-gray-900/40 transition">

                                <!-- Portal -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center font-bold text-xs">
                                            {{ getInitials(portal.nombre) }}
                                        </div>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white uppercase">
                                            {{ portal.nombre }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Aseguradora -->
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    {{ portal.aseguradora?.nombre || 'Portal Propio' }}
                                </td>

                                <!-- Usuario -->
                                <td class="px-6 py-4 text-sm font-mono text-gray-700 dark:text-gray-300">
                                    {{ portal.usuario }}
                                </td>

                                <!-- Password -->
                                <td class="px-6 py-4 text-sm font-mono text-gray-700 dark:text-gray-300">
                                    {{ portal.password }}
                                </td>

                                <!-- Link -->
                                <td class="px-6 py-4">
                                    <a v-if="portal.link" :href="portal.link" target="_blank"
                                        class="text-indigo-600 text-xs font-bold hover:underline">
                                        Abrir
                                    </a>
                                    <span v-else class="text-gray-400 text-xs">—</span>
                                </td>

                                <!-- Acciones -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">

                                        <!-- Editar -->
                                        <Link :href="route('portales.edit', portal.id)"
                                            class="text-gray-400 hover:text-amber-500 transition-colors" title="Editar">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </Link>

                                        <!-- Eliminar -->
                                        <button @click="deletePortal(portal)"
                                            class="text-gray-400 hover:text-red-500 transition-colors" title="Eliminar">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="text-center py-20 flex flex-col items-center">
                    <div
                        class="w-20 h-20 bg-indigo-50 dark:bg-indigo-900/20 rounded-full flex items-center justify-center mb-4 text-indigo-300">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-black text-slate-700 dark:text-white uppercase tracking-tight">Sin portales
                        registrados</h3>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Empieza agregando las
                        credenciales de tus herramientas.</p>
                </div>

                <div class="mt-8">
                    <Pagination :links="portales.links" />
                </div>
            </div>
        </div>

        <ConfirmationModal :show="confirmation.show" :title="confirmation.title" :message="confirmation.message"
            :type="confirmation.type" :confirm-label="confirmation.confirmLabel" @close="confirmation.show = false"
            @confirm="() => {
                confirmation.show = false;
                if (confirmation.callback) confirmation.callback();
            }" />
    </AuthenticatedLayout>
</template>