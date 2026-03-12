<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    options: {
        type: Array,
        required: true
    },
    labelKey: {
        type: String,
        default: 'name'
    },
    valueKey: {
        type: String,
        default: 'id'
    },
    placeholder: {
        type: String,
        default: 'Buscar o seleccionar...'
    }
});

const emit = defineEmits(['update:modelValue', 'change']);

const isOpen = ref(false);
const search = ref('');
const selectRef = ref(null);
const searchInput = ref(null);

const filteredOptions = computed(() => {
    if (!search.value) return props.options;
    return props.options.filter(o => 
        String(o[props.labelKey]).toLowerCase().includes(search.value.toLowerCase())
    );
});

const selectedOptionLabel = computed(() => {
    const selected = props.options.find(o => o[props.valueKey] === props.modelValue);
    return selected ? selected[props.labelKey] : '';
});

const toggleOpen = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        search.value = '';
        setTimeout(() => {
            if (searchInput.value) {
                searchInput.value.focus();
            }
        }, 50);
    }
};

const selectOption = (option) => {
    emit('update:modelValue', option[props.valueKey]);
    emit('change', option[props.valueKey]);
    isOpen.value = false;
};

// Close on outside click
const handleClickOutside = (e) => {
    if (selectRef.value && !selectRef.value.contains(e.target)) {
        isOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
    <div class="relative w-full" ref="selectRef">
        <!-- Input Principal / Trigger -->
        <div 
            @click="toggleOpen"
            class="block w-full rounded-xl border-0 py-3.5 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:ring-gray-600 cursor-pointer flex justify-between items-center transition-all bg-white"
            :class="{'ring-2 ring-emerald-600 border-emerald-600': isOpen}"
        >
            <span class="truncate text-sm font-medium" :class="{'text-gray-400': !selectedOptionLabel}">
                {{ selectedOptionLabel || placeholder }}
            </span>
            <svg class="h-4 w-4 text-gray-400 transition-transform" :class="{'rotate-180 text-emerald-600': isOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>

        <!-- Dropdown Menu -->
        <div v-if="isOpen" class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-2 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <input 
                        ref="searchInput"
                        v-model="search" 
                        type="text" 
                        class="block w-full rounded-lg border-0 py-2.5 pl-9 pr-3 text-sm bg-white focus:ring-2 focus:ring-emerald-500 sm:text-sm dark:bg-gray-800 dark:text-white ring-1 ring-inset ring-gray-200 dark:ring-gray-700" 
                        placeholder="Escribe para buscar..."
                        @click.stop
                    >
                </div>
            </div>
            <ul class="max-h-60 overflow-y-auto py-1 text-sm text-gray-700 dark:text-gray-200">
                <li v-if="filteredOptions.length === 0" class="px-4 py-3 text-gray-500 text-center text-xs">No se encontraron resultados para "{{ search }}"</li>
                <li 
                    v-for="option in filteredOptions" 
                    :key="option[valueKey]" 
                    @click="selectOption(option)"
                    class="px-4 py-3 cursor-pointer hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors flex items-center justify-between group"
                    :class="{'bg-emerald-50 text-emerald-700 font-bold dark:bg-emerald-900/50': modelValue === option[valueKey]}"
                >
                    <span class="truncate block group-hover:translate-x-1 transition-transform">{{ option[labelKey] }}</span>
                    <svg v-if="modelValue === option[valueKey]" class="h-4 w-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </li>
            </ul>
        </div>
    </div>
</template>
