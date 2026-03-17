<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    status: Number,
});

const title = computed(() => {
    return {
        503: '503: Servicio no disponible',
        500: '500: Error del servidor',
        404: '404: Página no encontrada',
        403: '403: Acceso denegado',
    }[props.status] || `${props.status}: Error inesperado`;
});

const description = computed(() => {
    return {
        503: 'Lo sentimos, estamos realizando tareas de mantenimiento. Estaremos en línea pronto.',
        500: '¡Ups! Algo salió mal en nuestros servidores. Ya estamos trabajando para solucionarlo.',
        404: 'Lo sentimos, la página que estás buscando no existe o fue movida.',
        403: 'Lo sentimos, pero no tienes permisos para acceder a esta página.',
    }[props.status] || 'Ha ocurrido un error inesperado. Por favor, intenta de nuevo más tarde.';
});
</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            
            <!-- Contenedor del Logo (usando el mismo logo de Welcome) -->
            <div class="flex justify-center mb-8">
                <!-- Mostraremos un icono grande por defecto que denote el problema o el logo si está configurado globalmente -->
                <div class="rounded-full bg-blue-100 p-6 flex items-center justify-center dark:bg-blue-900/30">
                    <svg v-if="status === 404" class="w-16 h-16 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <svg v-else-if="status === 403" class="w-16 h-16 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <svg v-else class="w-16 h-16 text-orange-500 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>

            <!-- Título del Error -->
            <h1 class="mt-6 text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ title }}
            </h1>

            <!-- Descripción Amigable -->
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                {{ description }}
            </p>

            <!-- Acciones -->
            <div class="mt-8 flex justify-center space-x-4">
                <Link
                    href="/"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                >
                    Volver al Inicio
                </Link>
            </div>

        </div>
    </div>
</template>
