<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    whatsappNumber: String,
});

const step = ref(1);
const formData = ref({
    type: '',
    name: '',
    phone: '',
});

const insuranceTypes = [
    { id: 'auto', label: 'Seguro de Auto', icon: '🚗' },
    { id: 'vida', label: 'Seguro de Vida', icon: '👤' },
    { id: 'hogar', label: 'Seguro de Hogar', icon: '🏠' },
    { id: 'salud', label: 'Seguro de Salud', icon: '🏥' },
    { id: 'empresa', label: 'Riesgos Laborales / Empresa', icon: '🏢' },
];

const selectType = (type) => {
    formData.value.type = type;
    step.value = 2;
};

const nextStep = () => {
    if (formData.value.name && formData.value.phone) {
        step.value = 3;
    }
};

const whatsappUrl = computed(() => {
    const text = encodeURIComponent(`Hola Canal Asesores, me interesa una cotización para ${formData.value.type}. Mi nombre es ${formData.value.name}.`);
    return `https://wa.me/${props.whatsappNumber?.replace(/\s+/g, '')}?text=${text}`;
});

const progress = computed(() => (step.value / 3) * 100);
</script>

<template>
    <div class="bg-white rounded-[2rem] shadow-2xl shadow-blue-900/10 border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <!-- Header & Progress -->
        <div class="px-8 pt-8 pb-4">
            <div class="flex justify-between items-end mb-4">
                <div>
                    <h3 class="text-2xl font-bold text-slate-900">Cotizador Express</h3>
                    <p class="text-slate-500 text-sm">Obtén una asesoría personalizada en segundos</p>
                </div>
                <div class="text-right">
                    <span class="text-blue-600 font-bold text-sm">Paso {{ step }} de 3</span>
                </div>
            </div>
            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                <div 
                    class="h-full bg-blue-600 transition-all duration-500 ease-out"
                    :style="{ width: `${progress}%` }"
                ></div>
            </div>
        </div>

        <!-- content -->
        <div class="p-8 min-h-[350px] flex flex-col justify-center">
            <!-- Step 1: Types -->
            <div v-if="step === 1" class="space-y-4 animate-fade-in">
                <p class="font-bold text-slate-700 mb-4 text-center">¿Qué deseas proteger hoy?</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button 
                        v-for="item in insuranceTypes" 
                        :key="item.id"
                        @click="selectType(item.label)"
                        class="flex items-center gap-4 p-4 rounded-2xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50 transition-all text-left group"
                    >
                        <span class="text-2xl">{{ item.icon }}</span>
                        <span class="font-semibold text-slate-700 group-hover:text-blue-700">{{ item.label }}</span>
                    </button>
                </div>
            </div>

            <!-- Step 2: Details -->
            <div v-if="step === 2" class="space-y-6 animate-fade-in">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold mb-2">
                         {{ formData.type }}
                    </div>
                    <p class="text-slate-600">Déjanos tus datos para contactarte con la mejor oferta</p>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5 ml-1">Tu Nombre Completo</label>
                        <input 
                            v-model="formData.name"
                            type="text" 
                            placeholder="Ej. Juan Pérez"
                            class="w-full px-5 py-3.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5 ml-1">WhatsApp / Teléfono</label>
                        <input 
                            v-model="formData.phone"
                            type="tel" 
                            placeholder="Ej. 312 456 7890"
                            class="w-full px-5 py-3.5 rounded-xl border border-slate-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all"
                        />
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <button 
                        @click="step = 1"
                        class="flex-1 px-6 py-4 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 transition-all"
                    >
                        Atrás
                    </button>
                    <button 
                        @click="nextStep"
                        :disabled="!formData.name || !formData.phone"
                        class="flex-[2] px-6 py-4 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-xl shadow-blue-200"
                    >
                        Continuar
                    </button>
                </div>
            </div>

            <!-- Step 3: Success -->
            <div v-if="step === 3" class="text-center space-y-6 animate-fade-in">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce-subtle">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h4 class="text-2xl font-bold text-slate-900">¡Casi listo, {{ formData.name.split(' ')[0] }}!</h4>
                <p class="text-slate-600 leading-relaxed">
                    Hemos recibido tus datos para tu póliza de <strong>{{ formData.type }}</strong>. 
                     Para una respuesta inmediata, haz clic en el botón de abajo.
                </p>
                
                <a 
                    :href="whatsappUrl" 
                    target="_blank"
                    class="inline-flex items-center gap-3 px-8 py-4 bg-green-500 text-white font-bold rounded-2xl hover:bg-green-600 hover:-translate-y-1 transition-all shadow-xl shadow-green-200"
                >
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.485 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.886.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.894 4.44-9.899 9.891 0 2.15.546 3.707 1.554 5.437l-1.01 3.691 3.955-1.037zm12.633-6.432c-.327-.164-1.93-.953-2.229-1.063-.3-.109-.517-.164-.735.164-.216.328-.842 1.063-1.032 1.281-.19.219-.381.246-.708.082-.327-.164-1.38-.508-2.628-1.622-.971-.867-1.626-1.938-1.817-2.265-.19-.328-.02-.505.143-.668.148-.146.327-.382.49-.573.163-.19.218-.327.327-.546.109-.219.054-.41-.028-.573-.081-.164-.735-1.771-1.007-2.427-.265-.64-.537-.554-.735-.563-.19-.01-.408-.012-.627-.012s-.573.082-.871.41c-.299.327-1.144 1.119-1.144 2.73 0 1.611 1.171 3.166 1.334 3.385.163.218 2.304 3.518 5.581 4.938.779.336 1.388.538 1.861.689.782.248 1.494.213 2.056.129.626-.093 1.93-.789 2.199-1.551.274-.762.274-1.416.192-1.551-.082-.137-.294-.218-.621-.382z" />
                    </svg>
                    Hablar por WhatsApp
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes bounce-subtle {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
</style>
