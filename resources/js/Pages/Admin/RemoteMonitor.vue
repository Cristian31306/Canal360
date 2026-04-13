<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    services: Object
});

const steps = ref([]);
const isSimulating = ref(false);

const runSimulation = async () => {
    isSimulating.ref = true;
    steps.value = [];
    
    try {
        const response = await axios.post(route('remote.simulate'));
        steps.value = response.data.steps;
    } catch (error) {
        console.error("Error en la simulación:", error);
    } finally {
        isSimulating.value = false;
    }
};
</script>

<template>
    <Head title="Monitor Sistemas Distribuidos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-black text-xl text-slate-800 leading-tight uppercase tracking-tighter">
                Examen: <span class="text-blue-600">Sistemas Distribuidos</span>
            </h2>
        </template>

        <div class="py-12 px-4 max-w-7xl mx-auto space-y-8">
            <!-- Sección Registry -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-3xl border border-slate-100">
                <div class="p-8 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-widest">Service Registry (Guía 6)</h3>
                        <p class="text-sm text-slate-500 font-medium mt-1">Directorio dinámico de nombres lógicos y puntos de acceso.</p>
                    </div>
                    <div class="px-4 py-1.5 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest animate-pulse">
                        Registry Online
                    </div>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-900 text-white text-[10px] uppercase font-black tracking-[0.2em]">
                            <tr>
                                <th class="px-8 py-4">Nombre del Servicio</th>
                                <th class="px-8 py-4">Dirección Remota (IP:Port)</th>
                                <th class="px-8 py-4">Protocolo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(endpoint, name) in services" :key="name" class="hover:bg-slate-50/80 transition-colors group">
                                <td class="px-8 py-5 font-bold text-slate-800 text-sm italic">{{ name }}</td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg font-mono text-xs font-bold border border-blue-100">{{ endpoint }}</span>
                                </td>
                                <td class="px-8 py-5 text-xs font-black text-slate-400 uppercase tracking-widest">HP-RPC / JSON-Marshal</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sección Simulador -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-blue-600 p-8 rounded-3xl shadow-xl text-white relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-2xl font-black leading-none mb-2 uppercase tracking-tighter">Simulador de Transparencia</h3>
                            <p class="text-blue-100 text-sm font-medium mb-8 leading-relaxed">
                                Ejecuta una llamada remota completa siguiendo los procesos de Marshaling y Unmarshaling (Guía 5).
                            </p>
                            <button 
                                @click="runSimulation"
                                :disabled="isSimulating"
                                class="w-full py-4 bg-white text-blue-600 rounded-2xl font-black uppercase tracking-widest hover:bg-blue-50 transition-all shadow-lg active:scale-95 disabled:opacity-50"
                            >
                                <span v-if="!isSimulating">⚡ Disparar RPC</span>
                                <span v-else>Simulando...</span>
                            </button>
                        </div>
                        <div class="absolute -right-8 -bottom-8 text-blue-500/20 group-hover:scale-110 transition-transform duration-700">
                            <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Conceptos de Examen</h4>
                        <ul class="space-y-3">
                            <li class="flex items-center text-xs font-bold text-slate-700">
                                <div class="w-2 h-2 rounded-full bg-green-500 mr-3"></div> Marshaling de Objetos
                            </li>
                            <li class="flex items-center text-xs font-bold text-slate-700">
                                <div class="w-2 h-2 rounded-full bg-blue-500 mr-3"></div> Proxy/Stub Dinámico
                            </li>
                            <li class="flex items-center text-xs font-bold text-slate-700">
                                <div class="w-2 h-2 rounded-full bg-purple-500 mr-3"></div> Transparencia de Red
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Log de Pasos -->
                <div class="lg:col-span-2 space-y-6">
                    <div v-if="steps.length === 0" class="h-full min-h-[400px] border-2 border-dashed border-slate-200 rounded-3xl flex flex-col items-center justify-center text-slate-300">
                        <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <p class="font-black uppercase tracking-widest text-xs">Esperando simulación...</p>
                    </div>

                    <div v-for="(step, index) in steps" :key="index" class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500" :style="{ animationDelay: (index * 150) + 'ms' }">
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                            <div class="flex items-center">
                                <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px] font-black mr-3">{{ index + 1 }}</span>
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter">{{ step.title }}</h4>
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 italic">Procesado con éxito</span>
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-medium text-slate-500 mb-4 italic">{{ step.desc }}</p>
                            <div class="bg-slate-900 rounded-2xl p-4 font-mono text-[11px] text-emerald-400 overflow-x-auto shadow-inner">
                                <pre>{{ JSON.stringify(step.data, null, 2) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.font-mono {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
}
</style>
