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
                            
                            <!-- SEO Settings -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <h3 class="text-lg font-bold text-blue-600 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Configuración SEO (Motores de búsqueda)
                                </h3>
                                <div class="grid gap-6">
                                    <template v-for="(setting, index) in form.settings" :key="setting.key">
                                        <div v-if="setting.key === 'landing_meta_title' || setting.key === 'landing_meta_description'" class="space-y-2">
                                            <label :for="setting.key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ setting.label }}
                                            </label>
                                            <textarea v-if="setting.key === 'landing_meta_description'"
                                                v-model="form.settings[index].value"
                                                :id="setting.key"
                                                rows="2"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700 placeholder-gray-400"
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

                            <!-- WhatsApp Settings -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <h3 class="text-lg font-bold text-blue-600 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Canal de Venta (WhatsApp)
                                </h3>
                                <div class="grid gap-6">
                                    <template v-for="(setting, index) in form.settings" :key="setting.key">
                                        <div v-if="setting.key === 'landing_whatsapp_number'" class="space-y-2">
                                            <label :for="setting.key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ setting.label }}
                                            </label>
                                            <input
                                                v-model="form.settings[index].value"
                                                :id="setting.key"
                                                type="text"
                                                placeholder="Ej: 573123456789"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700"
                                            />
                                            <p class="text-xs text-gray-500">Este número es el que se usará para los botones de "Cotizar Ahora" y el botón flotante.</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Footer Settings -->
                            <div class="bg-gray-50 dark:bg-gray-900/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-800">
                                <h3 class="text-lg font-bold text-blue-600 mb-6 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    Pie de Página (Footer)
                                </h3>
                                <div class="grid gap-6">
                                    <template v-for="(setting, index) in form.settings" :key="setting.key">
                                        <div v-if="setting.key === 'landing_footer_description'" class="space-y-2">
                                            <label :for="setting.key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ setting.label }}
                                            </label>
                                            <textarea
                                                v-model="form.settings[index].value"
                                                :id="setting.key"
                                                rows="3"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-700"
                                            ></textarea>
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
                                        <div v-if="setting.key === 'contact_email' || setting.key.includes('_phone')" class="space-y-2">
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
