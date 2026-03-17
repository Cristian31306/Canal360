<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: Array,
});

const form = useForm({
    settings: props.settings.map(setting => ({
        key: setting.key,
        value: setting.value,
        label: setting.label,
        type: setting.type,
        group: setting.group, // Faltaba este campo para que los filtros funcionen
    })),
});

const submit = () => {
    form.post(route('settings.landing.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Manejar éxito si es necesario
        },
    });
};

const handleImageUpload = (e, index) => {
    const file = e.target.files[0];
    if (!file) return;

    // 1. Validación de Formato
    if (!file.type.startsWith('image/')) {
        window.alert('¡Error! El archivo debe ser una imagen (JPG, PNG o WebP).');
        e.target.value = '';
        return;
    }

    // 2. Validación de Tamaño (Máximo 2MB)
    if (file.size > 2 * 1024 * 1024) {
        window.alert('¡Imagen muy pesada! El tamaño máximo permitido es de 2MB para mantener la velocidad del sitio.');
        e.target.value = '';
        return;
    }

    // 3. Procesamiento seguro con FileReader
    form.settings[index].value = file;
    try {
        const reader = new window.FileReader();
        reader.onload = (event) => {
            form.settings[index].preview = event.target.result;
        };
        reader.readAsDataURL(file);
    } catch (err) {
        console.error('Error al cargar la imagen:', err);
        window.alert('Hubo un error al procesar la previsualización, pero puedes intentar guardar el formulario.');
    }
};
</script>

<template>
    <Head title="Configuración de Landing Page" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Gestión de Contenidos - Landing Page
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-12">
                            
                            <!-- Hero Settings -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <h3 class="text-lg font-bold text-blue-600 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Sección Principal (Hero)
                                </h3>
                                <div class="grid gap-6">
                                    <template v-for="(setting, index) in form.settings" :key="setting.key">
                                        <div v-if="setting.group === 'landing_hero' || setting.key === 'landing_cta_text' || setting.key === 'landing_whatsapp_number'" class="space-y-2">
                                            <label :for="setting.key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ setting.label }}
                                            </label>
                                            
                                            <!-- Image Upload -->
                                            <div v-if="setting.type === 'image'" class="space-y-4">
                                                <div v-if="(typeof setting.value === 'string' && setting.value) || setting.preview" class="space-y-2">
                                                    <div class="w-48 h-28 rounded-lg overflow-hidden border border-gray-200 shadow-sm bg-gray-100 relative items-center justify-center flex">
                                                        <img v-if="setting.preview || (setting.value && setting.value.length > 10)" :src="setting.preview || setting.value" class="w-full h-full object-cover absolute inset-0">
                                                        <span v-else class="text-xs text-gray-400 font-medium">Sin imagen guardada</span>
                                                    </div>
                                                    <p class="text-[10px] text-blue-500 font-bold uppercase tracking-wider">
                                                        Recomendado: 1920x1080px (o relación 16:9) • Máx 2MB
                                                    </p>
                                                </div>
                                                <input
                                                    type="file"
                                                    :id="setting.key"
                                                    accept="image/*"
                                                    @change="(e) => handleImageUpload(e, index)"
                                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                                />
                                            </div>

                                            <textarea v-else-if="setting.type === 'textarea'"
                                                v-model="form.settings[index].value"
                                                :id="setting.key"
                                                rows="3"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 placeholder-gray-400"
                                            ></textarea>
                                            
                                            <input v-else-if="setting.type !== 'image'"
                                                v-model="form.settings[index].value"
                                                :id="setting.key"
                                                :type="setting.type"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700"
                                            />
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Services Titles & Items -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <h3 class="text-lg font-bold text-blue-600 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Nuestras Soluciones y Coberturas
                                </h3>
                                <div class="grid gap-8 md:grid-cols-2">
                                    <div v-for="i in 4" :key="i" class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 space-y-4">
                                        <template v-for="(setting, index) in form.settings" :key="setting.key">
                                            <div v-if="setting.key === 'landing_service_cat_' + i + '_title'" class="space-y-2">
                                                <label class="block text-xs font-bold uppercase tracking-wider text-blue-500">Título Grupo {{ i }}</label>
                                                <input v-model="form.settings[index].value" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700" />
                                            </div>
                                            <div v-if="setting.key === 'landing_service_cat_' + i + '_items'" class="space-y-2">
                                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500">Items (Sep. por comas)</label>
                                                <textarea v-model="form.settings[index].value" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700"></textarea>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Technology Settings -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <h3 class="text-lg font-bold text-blue-600 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Gestión y Tecnología
                                </h3>
                                <div class="grid gap-6">
                                    <template v-for="(setting, index) in form.settings" :key="setting.key">
                                        <div v-if="setting.key.includes('landing_tech')" class="space-y-2">
                                            <label :for="setting.key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ setting.label }}
                                            </label>
                                            <textarea v-if="setting.type === 'textarea'"
                                                v-model="form.settings[index].value"
                                                :id="setting.key"
                                                rows="3"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700"
                                            ></textarea>
                                            <input v-else
                                                v-model="form.settings[index].value"
                                                :id="setting.key"
                                                type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700"
                                            />
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- CONTACTS Settings -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <h3 class="text-lg font-bold text-blue-600 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Asesores y Contacto
                                </h3>
                                <div class="grid gap-6 md:grid-cols-2">
                                    <template v-for="(setting, index) in form.settings" :key="setting.key">
                                        <div v-if="setting.key.includes('contact')" class="space-y-2">
                                            <label :for="setting.key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ setting.label }}
                                            </label>
                                            <input
                                                v-model="form.settings[index].value"
                                                :id="setting.key"
                                                :type="setting.type"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700"
                                            />
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-4 border-t pt-6">
                                <Transition
                                    enter-active-class="transition ease-in-out"
                                    enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="form.recentlySuccessful" class="text-sm text-green-600">
                                        Guardado con éxito.
                                    </p>
                                </Transition>

                                <button
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                >
                                    Actualizar Landing Page
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
