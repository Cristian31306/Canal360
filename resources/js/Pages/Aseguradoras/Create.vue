<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nombre: '',
    nit: '',
    logo: null,
    contactos: [
        { rol: 'Ejecutivo Comercial', nombre: '', telefono: '', email: '' }
    ]
});

const previewLogo = ref(null);

const handleLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        previewLogo.value = URL.createObjectURL(file);
    } else {
        form.logo = null;
        previewLogo.value = null;
    }
};

const addContacto = () => {
    form.contactos.push({ rol: '', nombre: '', telefono: '', email: '' });
};

const removeContacto = (index) => {
    form.contactos.splice(index, 1);
};

const submit = () => {
    form.post(route('aseguradoras.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Nueva Aseguradora" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 w-full">
                <Link :href="route('aseguradoras.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-indigo-600 pl-3 dark:text-gray-100">
                    Añadir Aseguradora
                </h2>
            </div>
        </template>

        <div class="max-w-3xl mx-auto py-6">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-hidden">
                <div class="px-4 py-6 sm:p-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <!-- Datos Principales -->
                        <div>
                            <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Información de la Compañía</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Nombre de la Aseguradora <span class="text-red-500">*</span></label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.nombre" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                    <p class="mt-1 text-xs text-red-500" v-if="form.errors.nombre">{{ form.errors.nombre }}</p>
                                </div>

                                <div class="col-span-1">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">NIT <span class="text-red-500">*</span></label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.nit" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                    <p class="mt-1 text-xs text-red-500" v-if="form.errors.nit">{{ form.errors.nit }}</p>
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Logotipo Institucional (Opcional)</label>
                                    <div class="mt-2 flex items-center gap-x-3">
                                        <div v-if="previewLogo" class="h-16 w-16 overflow-hidden rounded-md bg-white border border-gray-200 dark:border-gray-700 flex items-center justify-center p-1">
                                            <img :src="previewLogo" alt="Preview" class="h-full w-full object-contain" />
                                        </div>
                                        <div v-else class="h-16 w-16 rounded-md bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                            <svg class="h-8 w-8 text-gray-300 dark:text-gray-600" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <input type="file" @change="handleLogoChange" accept="image/jpeg, image/png, image/webp" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/40 dark:file:text-indigo-400" />
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-red-500" v-if="form.errors.logo">{{ form.errors.logo }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contactos Especializados -->
                        <div>
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white">Contactos Especializados</h3>
                                <button type="button" @click="addContacto" class="inline-flex items-center gap-x-1 text-sm font-semibold leading-6 text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                    </svg>
                                    Añadir Contacto
                                </button>
                            </div>
                            
                            <div class="mt-4 space-y-6">
                                <div v-for="(contacto, index) in form.contactos" :key="index" class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700 relative">
                                    
                                    <button v-if="form.contactos.length > 1" type="button" @click="removeContacto(index)" class="absolute top-4 right-4 text-red-500 hover:text-red-700 transition-colors" title="Eliminar contacto">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                        <div>
                                            <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Rol / Área <span class="text-red-500">*</span></label>
                                            <div class="mt-1">
                                                <input type="text" v-model="contacto.rol" required placeholder="Ej: Vida, Autos, Siniestros..." class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                            </div>
                                            <p class="mt-1 text-xs text-red-500" v-if="form.errors[`contactos.${index}.rol`]">{{ form.errors[`contactos.${index}.rol`] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Nombre del Contacto <span class="text-red-500">*</span></label>
                                            <div class="mt-1">
                                                <input type="text" v-model="contacto.nombre" required placeholder="Nombre de la persona" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                            </div>
                                            <p class="mt-1 text-xs text-red-500" v-if="form.errors[`contactos.${index}.nombre`]">{{ form.errors[`contactos.${index}.nombre`] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Correo Electrónico</label>
                                            <div class="mt-1">
                                                <input type="email" v-model="contacto.email" placeholder="correo@ejemplo.com" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                            </div>
                                            <p class="mt-1 text-xs text-red-500" v-if="form.errors[`contactos.${index}.email`]">{{ form.errors[`contactos.${index}.email`] }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Teléfono</label>
                                            <div class="mt-1">
                                                <input type="text" v-model="contacto.telefono" placeholder="Número de contacto" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                            </div>
                                            <p class="mt-1 text-xs text-red-500" v-if="form.errors[`contactos.${index}.telefono`]">{{ form.errors[`contactos.${index}.telefono`] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-x-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <Link :href="route('aseguradoras.index')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:underline">Cancelar</Link>
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Guardar Aseguradora</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
