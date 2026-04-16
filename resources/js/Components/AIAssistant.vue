<script setup>
import { ref } from 'vue';

const isOpen = ref(false);
const showPulse = ref(true);

const toggleAssistant = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) showPulse.value = false;
};

const quickLinks = [
    { label: '¿Cómo reportar siniestro?', icon: '⚠️' },
    { label: 'Cotizar Seguro Auto', icon: '🚗' },
    { label: 'Contacto con Asesor', icon: '👤' },
];
</script>

<template>
    <div class="fixed bottom-6 left-6 z-[60] font-sans">
        <!-- Main Button -->
        <button 
            @click="toggleAssistant"
            class="relative w-14 h-14 sm:w-16 sm:h-16 bg-slate-900 rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-transform group overflow-hidden"
        >
            <div v-if="showPulse" class="absolute inset-0 bg-blue-500 rounded-full animate-ping opacity-20"></div>
            
            <!-- AI Icon Representation -->
            <div class="relative z-10 text-white">
                <svg v-if="!isOpen" class="w-8 h-8 sm:w-9 sm:h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <svg v-else class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            
            <!-- Glow effect -->
            <div class="absolute inset-0 bg-gradient-to-tr from-blue-600/20 to-cyan-400/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        </button>

        <!-- Assistant Panel -->
        <transition 
            enter-active-class="transition duration-300 ease-out" 
            enter-from-class="transform -translate-x-10 opacity-0 scale-90" 
            enter-to-class="transform translate-x-0 opacity-100 scale-100" 
            leave-active-class="transition duration-200 ease-in" 
            leave-from-class="transform translate-x-0 opacity-100 scale-100" 
            leave-to-class="transform -translate-x-10 opacity-0 scale-90"
        >
            <div v-if="isOpen" class="absolute bottom-20 left-0 w-[300px] sm:w-[350px] bg-white rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-100 overflow-hidden">
                <!-- Header -->
                <div class="bg-slate-900 p-6 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/20 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-1">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-widest text-blue-400">Asistente Agéntico 2025</span>
                        </div>
                        <h4 class="text-xl font-bold">Hola, soy el asistente virtual de Canal</h4>
                    </div>
                </div>

                <!-- content -->
                <div class="p-6 space-y-4">
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <p class="text-sm text-slate-600 leading-relaxed">
                            ¿En qué puedo ayudarte hoy? Puedo ayudarte a cotizar una póliza o resolver dudas sobre tus seguros actuales.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Preguntas frecuentes</p>
                        <button 
                            v-for="link in quickLinks" 
                            :key="link.label"
                            class="w-full flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-500 hover:bg-blue-50 transition-all text-left group"
                        >
                            <span class="text-lg opacity-80 group-hover:opacity-100">{{ link.icon }}</span>
                            <span class="text-sm font-semibold text-slate-700 group-hover:text-blue-700">{{ link.label }}</span>
                        </button>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-4 bg-slate-50 border-t border-slate-100 text-center">
                    <p class="text-[10px] text-slate-400">Canal Asesores | Tecnología y Seguridad</p>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
/* Scoped styles if needed */
</style>
