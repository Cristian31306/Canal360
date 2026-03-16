<script setup>
import { ref, watch, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    ramos: Object,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');

const handleSearch = () => {
    router.get(
        route('ramos.index'),
        { search: searchQuery.value },
        { preserveState: true, replace: true }
    );
};

const clearSearch = () => {
    searchQuery.value = '';
    handleSearch();
};

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
    if (page.props.flash?.error) showAlert('Alerta', page.props.flash.error, 'danger');
    if (page.props.flash?.success) showAlert('Éxito', page.props.flash.success, 'success');
});

watch(() => page.props.flash, (flash) => {
    if (flash?.error) showAlert('Alerta', flash.error, 'danger');
    if (flash?.success) showAlert('Éxito', flash.success, 'success');
}, { deep: true });

const showAlert = (title, message, type = 'danger') => {
    confirmation.value = {
        show: true,
        title,
        message: message.includes('No se puede eliminar') ? `Alerta: ${message}` : message,
        type,
        confirmLabel: 'Entendido',
        callback: null
    };
};

const deleteRamo = (id) => {
    confirmation.value = {
        show: true,
        title: 'Eliminar Ramo',
        message: '¿Estás seguro de que deseas eliminar este ramo? Esta acción no se puede deshacer si no tiene pólizas asociadas.',
        type: 'danger',
        confirmLabel: 'Eliminar',
        callback: () => router.delete(route('ramos.destroy', id))
    };
};
</script>

<template>
    <Head title="Directorio de Ramos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center w-full gap-4">
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-blue-600 pl-3 dark:text-gray-100">
                    Ramos / Productos
                </h2>
                <Link
                    :href="route('ramos.create')"
                    class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all flex-shrink-0"
                >
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Nuevo Ramo
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Table Card -->
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700">
                <!-- Toolbar -->
                <div class="border-b border-gray-200 px-4 py-4 sm:px-6 flex flex-col sm:flex-row justify-between items-center gap-4 dark:border-gray-700">
                    <div class="w-full sm:w-auto flex-1 max-w-lg">
                        <form @submit.prevent="handleSearch" class="flex gap-2 w-full">
                            <div class="relative rounded-md shadow-sm flex-1">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    v-model="searchQuery" 
                                    class="block w-full rounded-md border-0 py-2.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600" 
                                    placeholder="Buscar ramo..."
                                >
                            </div>
                            <button 
                                type="submit" 
                                class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors"
                            >
                                Buscar
                            </button>
                            <button 
                                v-if="filters?.search"
                                type="button" 
                                @click="clearSearch"
                                class="inline-flex items-center justify-center rounded-md bg-white px-3 py-2.5 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 dark:bg-gray-700 dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors"
                                title="Limpiar búsqueda"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800/50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 dark:text-gray-200">Nombre del Ramo</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">Fecha de Registro</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                            <tr v-for="ramo in ramos.data" :key="ramo.id" class="hover:bg-gray-50 transition-colors dark:hover:bg-gray-700/50">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6 dark:text-white">
                                    {{ ramo.nombre }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                    {{ new Date(ramo.created_at).toLocaleDateString('es-CO') }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('ramos.edit', ramo.id)" class="text-gray-400 hover:text-amber-500 transition-colors" title="Editar">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </Link>
                                        <button @click="deleteRamo(ramo.id)" class="text-gray-400 hover:text-red-500 transition-colors" title="Eliminar">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="ramos.data.length === 0">
                                <td colspan="3" class="py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Sin ramos encontrados</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza registrando un nuevo ramo o producto.</p>
                                    <div class="mt-6">
                                        <Link :href="route('ramos.create')" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                            </svg>
                                            Nuevo Ramo
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div v-if="ramos.data.length > 0 && ramos.links" class="border-t border-gray-200 px-4 py-3 sm:px-6 flex items-center justify-between dark:border-gray-700">
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Mostrando
                                <span class="font-medium">{{ ramos.from }}</span>
                                al
                                <span class="font-medium">{{ ramos.to }}</span>
                                de
                                <span class="font-medium">{{ ramos.total }}</span>
                                resultados
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <Link v-for="(link, k) in ramos.links" :key="k" 
                                      :href="link.url || '#'"
                                      :class="[ 
                                          link.active ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700',
                                          !link.url ? 'pointer-events-none opacity-50' : '',
                                          'relative inline-flex items-center px-4 py-2 text-sm font-semibold'
                                      ]"
                                      :disabled="!link.url"
                                      preserve-scroll
                                      v-html="link.label"
                                >
                                </Link>
                            </nav>
                        </div>
                    </div>
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
