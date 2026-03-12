<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Bienvenido a Canal360" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 min-h-screen flex flex-col justify-center items-center selection:bg-[#FF2D20] selection:text-white relative overflow-hidden">
        
        <!-- Elemento decorativo de fondo -->
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute top-[-10%] right-[-10%] w-[50%] h-[50%] bg-teal-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-[-20%] left-[20%] w-[50%] h-[50%] bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        </div>

        <div class="relative z-10 w-full max-w-2xl px-6 lg:max-w-7xl flex flex-col items-center">
            
            <header class="w-full flex justify-end items-center py-6">
                <nav v-if="canLogin" class="flex flex-1 justify-end space-x-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md px-4 py-2 bg-blue-600 text-white font-semibold transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        Ir al Aplicativo
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-4 py-2 text-black ring-1 ring-gray-300 font-semibold transition hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:text-white dark:ring-gray-700 dark:hover:bg-gray-800"
                        >
                            Iniciar Sesión
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md px-4 py-2 bg-gray-800 text-white font-semibold transition hover:bg-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:bg-white dark:text-gray-800 dark:hover:bg-gray-100"
                        >
                            Registrarse
                        </Link>
                    </template>
                </nav>
            </header>

            <main class="mt-16 flex flex-col items-center justify-center text-center w-full">
                <!-- Zona del Logo -->
                <div class="mb-8 flex justify-center w-full">
                    <img src="/logo.png" alt="Canal360" class="h-32 w-auto drop-shadow-lg transition-transform hover:scale-105 duration-300" />
                </div>

                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-4">
                    Gestión Integral de Pólizas
                </h1>
                
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 max-w-2xl leading-relaxed">
                    Centraliza la gestión de tus clientes, activos asegurados, pólizas y carteras en un solo lugar. 
                    Potenciado con automatización para que nunca pierdas una renovación.
                </p>

                <div class="mt-10 flex gap-4">
                   <Link
                        v-if="!$page.props.auth.user"
                        :href="route('login')"
                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-lg hover:bg-blue-700 hover:shadow-xl transition-all duration-300"
                    >
                        Ingresar al Sistema
                    </Link>
                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg shadow-lg hover:bg-blue-700 hover:shadow-xl transition-all duration-300"
                    >
                        Ir al Tablero Principal
                    </Link>
                </div>
            </main>

            <footer class="mt-24 text-center text-sm text-gray-500 dark:text-white/50 w-full py-6">
                &copy; {{ new Date().getFullYear() }} Canal360. Todos los derechos reservados.
            </footer>
        </div>
    </div>
</template>

<style>
/* Utilities for backgrounds */
.animate-blob {
    animation: blob 7s infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
.animation-delay-4000 {
    animation-delay: 4s;
}
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}
</style>
