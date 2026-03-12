<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    tipo_persona: 'natural',
    tipo_documento: 'CC',
    numero_documento: '',
    nombre_razon_social: '',
    telefono: '',
    email: '',
    direccion: '',
    ciudad: '',
    fecha_nacimiento: '',
    fecha_contacto: new Date().toISOString().split('T')[0],
    observaciones: '',
    rep_legal_nombre: '',
    rep_legal_documento: '',
    rep_legal_telefono: '',
    rep_legal_email: ''
});

const submit = () => {
    form.post(route('clientes.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Nuevo Cliente" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 w-full">
                <Link :href="route('clientes.index')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-blue-600 pl-3 dark:text-gray-100">
                    Nuevo Cliente
                </h2>
            </div>
        </template>

        <div class="max-w-4xl mx-auto py-6">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-hidden">
                <div class="px-4 py-6 sm:p-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        
                        <!-- Tipo de Persona -->
                        <div>
                            <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Información Principal</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Tipo de Persona</label>
                                    <div class="mt-2 flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-600 flex-1 transition-colors" :class="form.tipo_persona === 'natural' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200'">
                                            <input type="radio" class="text-blue-600 focus:ring-blue-600 h-4 w-4" name="tipo_persona" value="natural" v-model="form.tipo_persona">
                                            <span class="text-sm text-gray-900 dark:text-gray-200">Persona Natural</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-600 flex-1 transition-colors" :class="form.tipo_persona === 'juridica' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200'">
                                            <input type="radio" class="text-blue-600 focus:ring-blue-600 h-4 w-4" name="tipo_persona" value="juridica" v-model="form.tipo_persona">
                                            <span class="text-sm text-gray-900 dark:text-gray-200">Persona Jurídica</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Tipo de Documento</label>
                                    <select v-model="form.tipo_documento" class="mt-2 block w-full rounded-md border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                        <option value="CC" v-if="form.tipo_persona === 'natural'">Cédula de Ciudadanía (CC)</option>
                                        <option value="CE" v-if="form.tipo_persona === 'natural'">Cédula de Extranjería (CE)</option>
                                        <option value="NIT" v-if="form.tipo_persona === 'juridica'">NIT</option>
                                        <option value="PASAPORTE">Pasaporte</option>
                                    </select>
                                </div>

                                <div class="col-span-1">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Número de Documento / NIT</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.numero_documento" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>

                                <div class="col-span-1">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Nombre Completo / Razón Social</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.nombre_razon_social" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Datos de Contacto -->
                        <div>
                            <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Datos de Contacto</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Correo Electrónico</label>
                                    <div class="mt-2 relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M3 4a2 2 0 00-2 2v8a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2H3zm14 2.207l-6.5 3.25a1 1 0 01-.894 0L3 6.207V6h14v.207z M3 8.25l6.053 3.026a3 3 0 002.684 0L17 8.25V14H3V8.25z" />
                                            </svg>
                                        </div>
                                        <input type="email" v-model="form.email" required class="block w-full rounded-md border-0 py-2 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Teléfono / Celular</label>
                                    <div class="mt-2 relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                              <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 013.5 2h1.148a1.5 1.5 0 011.465 1.175l.716 3.223a1.5 1.5 0 01-1.052 1.767l-.933.267c-.41.117-.643.555-.48.95a11.542 11.542 0 006.254 6.254c.395.163.833-.07.95-.48l.267-.933a1.5 1.5 0 011.767-1.052l3.223.716A1.5 1.5 0 0118 15.352V16.5a1.5 1.5 0 01-1.5 1.5H15c-1.149 0-2.263-.15-3.326-.43A13.022 13.022 0 012.43 8.326 13.019 13.019 0 012 5V3.5z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <input type="text" v-model="form.telefono" required class="block w-full rounded-md border-0 py-2 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Dirección</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.direccion" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Ciudad</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.ciudad" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Fecha de Nacimiento / Constitución</label>
                                    <div class="mt-2">
                                        <input type="date" v-model="form.fecha_nacimiento" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600" :pattern="'\\d{4}-\\d{2}-\\d{2}'">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Útil para envío de alertas de cumpleaños.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Representante Legal (Solo Jurídica) -->
                        <div v-show="form.tipo_persona === 'juridica'" class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Datos Representante Legal</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Nombre del Representante Legal</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.rep_legal_nombre" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Documento del Representante</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.rep_legal_documento" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CRM Observaciones -->
                        <div>
                            <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Información Adicional (CRM)</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Fecha de Primer Contacto</label>
                                    <div class="mt-2">
                                        <input type="date" v-model="form.fecha_contacto" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Observaciones</label>
                                    <div class="mt-2">
                                        <textarea v-model="form.observaciones" rows="3" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600"></textarea>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Notas internas sobre el cliente (Ej: Viene recomendado por Juan Pérez).</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-x-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <Link :href="route('clientes.index')" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:underline">Cancelar</Link>
                            <button type="submit" :disabled="form.processing" class="rounded-md bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">
                                <span v-if="form.processing">Guardando...</span>
                                <span v-else>Guardar Cliente</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
