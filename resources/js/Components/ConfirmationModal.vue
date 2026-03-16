<script setup>
import { computed } from 'vue';
import Modal from './Modal.vue';
import SecondaryButton from './SecondaryButton.vue';
import DangerButton from './DangerButton.vue';
import PrimaryButton from './PrimaryButton.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Confirmar Acción',
    },
    message: {
        type: String,
    },
    type: {
        type: String,
        default: 'danger', // danger, warning, success, info
    },
    confirmLabel: {
        type: String,
        default: 'Confirmar',
    },
    cancelLabel: {
        type: String,
        default: 'Cancelar',
    },
});

const emit = defineEmits(['close', 'confirm']);

const close = () => {
    emit('close');
};

const confirm = () => {
    emit('confirm');
};

const iconClass = computed(() => {
    return {
        danger: 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
        warning: 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400',
        success: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
        info: 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
    }[props.type];
});

const buttonComponent = computed(() => {
    if (props.type === 'danger') return DangerButton;
    if (props.type === 'success') return PrimaryButton; // Could use emerald but primary is good
    return PrimaryButton;
});
</script>

<template>
    <Modal :show="show" max-width="md" @close="close">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div :class="['p-3 rounded-2xl flex-shrink-0', iconClass]">
                    <svg v-if="type === 'danger'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                    <svg v-else-if="type === 'warning' || type === 'info'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <svg v-else-if="type === 'success'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">
                        {{ title }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
                        {{ message }}
                    </p>
                </div>
            </div>

            <div class="mt-8 flex flex-col sm:flex-row-reverse gap-3">
                <component 
                    :is="buttonComponent" 
                    @click="confirm" 
                    class="w-full sm:w-auto justify-center rounded-xl py-2.5 font-black uppercase text-[10px] tracking-widest shadow-lg transition-all active:scale-95"
                    :class="type === 'danger' ? 'shadow-red-500/20' : 'shadow-blue-500/20'"
                >
                    {{ confirmLabel }}
                </component>
                <SecondaryButton 
                    v-if="type === 'danger' || type === 'warning'" 
                    @click="close" 
                    class="w-full sm:w-auto justify-center rounded-xl py-2.5 font-black uppercase text-[10px] tracking-widest transition-all"
                >
                    {{ cancelLabel }}
                </SecondaryButton>
                <!-- Solo cerrar si es info/success y no tiene callback de confirm? O siempre mostrar cancelar? -->
                <SecondaryButton 
                    v-else 
                    @click="close" 
                    class="w-full sm:w-auto justify-center rounded-xl py-2.5 font-black uppercase text-[10px] tracking-widest transition-all"
                >
                    Cerrar
                </SecondaryButton>
            </div>
        </div>
    </Modal>
</template>
