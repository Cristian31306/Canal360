<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    cliente: Object,
});

const form = useForm({
    tipo_persona: props.cliente.tipo_persona,
    tipo_documento: props.cliente.tipo_documento,
    numero_documento: props.cliente.numero_documento,
    nombre_razon_social: props.cliente.nombre_razon_social,
    telefono: props.cliente.telefono || '',
    email: props.cliente.email || '',
    direccion: props.cliente.direccion || '',
    ciudad: props.cliente.ciudad || '',
    fecha_nacimiento: props.cliente.fecha_nacimiento ? props.cliente.fecha_nacimiento.split(' ')[0] : '', // Handle date formatting
    fecha_contacto: props.cliente.fecha_contacto ? props.cliente.fecha_contacto.split(' ')[0] : '',
    observaciones: props.cliente.observaciones || '',
    rep_legal_nombre: props.cliente.rep_legal_nombre || '',
    rep_legal_documento: props.cliente.rep_legal_documento || '',
    rep_legal_telefono: props.cliente.rep_legal_telefono || '',
    rep_legal_email: props.cliente.rep_legal_email || '',
    anna_credentials: props.cliente.anna_credentials || [],
    payment_credentials: props.cliente.payment_credentials || []
});

const addAnnaCredential = () => {
    form.anna_credentials.push({ usuario: '', password: '', observaciones: '' });
};

const removeAnnaCredential = (index) => {
    form.anna_credentials.splice(index, 1);
};

const addPaymentCredential = () => {
    form.payment_credentials.push({ aseguradora_id: '', usuario: '', password: '', observaciones: '' });
};

const removePaymentCredential = (index) => {
    form.payment_credentials.splice(index, 1);
};

const goBack = () => window.history.back();

const submit = () => {
    form.put(route('clientes.update', props.cliente.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="'Editar Cliente: ' + cliente.nombre_razon_social" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4 w-full">
                <button @click="goBack" type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </button>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-amber-500 pl-3 dark:text-gray-100">
                    Editar Cliente <span class="text-gray-500 text-lg ml-2 font-normal">#{{ cliente.numero_documento }}</span>
                </h2>
            </div>
        </template>

        <div class="max-w-4xl mx-auto py-4">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800 dark:ring-gray-700 overflow-hidden">
                <div class="px-4 py-4 sm:p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        
                        <!-- Tipo de Persona -->
                        <div>
                            <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Información Principal</h3>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Tipo de Persona</label>
                                    <div class="mt-2 flex gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-600 flex-1 transition-colors" :class="form.tipo_persona === 'natural' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200'">
                                            <input type="radio" class="text-amber-600 focus:ring-amber-600 h-4 w-4" name="tipo_persona" value="natural" v-model="form.tipo_persona">
                                            <span class="text-sm text-gray-900 dark:text-gray-200">Persona Natural</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer p-3 border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-600 flex-1 transition-colors" :class="form.tipo_persona === 'juridica' ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-gray-200'">
                                            <input type="radio" class="text-amber-600 focus:ring-amber-600 h-4 w-4" name="tipo_persona" value="juridica" v-model="form.tipo_persona">
                                            <span class="text-sm text-gray-900 dark:text-gray-200">Persona Jurídica</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Tipo de Documento</label>
                                    <select v-model="form.tipo_documento" class="mt-2 block w-full rounded-md border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                        <option value="CC" v-if="form.tipo_persona === 'natural'">Cédula de Ciudadanía (CC)</option>
                                        <option value="CE" v-if="form.tipo_persona === 'natural'">Cédula de Extranjería (CE)</option>
                                        <option value="NIT" v-if="form.tipo_persona === 'juridica'">NIT</option>
                                        <option value="PASAPORTE">Pasaporte</option>
                                    </select>
                                </div>

                                <div class="col-span-1">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Número de Documento / NIT</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.numero_documento" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>

                                <div class="col-span-1">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Nombre Completo / Razón Social</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.nombre_razon_social" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
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
                                        <input type="email" v-model="form.email" required class="block w-full rounded-md border-0 py-2 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
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
                                        <input type="text" v-model="form.telefono" required class="block w-full rounded-md border-0 py-2 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Dirección</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.direccion" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Ciudad</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.ciudad" required class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Fecha de Nacimiento / Constitución</label>
                                    <div class="mt-2">
                                        <input type="date" v-model="form.fecha_nacimiento" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
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
                                        <input type="text" v-model="form.rep_legal_nombre" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-300">Documento del Representante</label>
                                    <div class="mt-2">
                                        <input type="text" v-model="form.rep_legal_documento" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Credenciales ANNA (Especial para Minero/Otros específicos) -->
                        <div class="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-between border-b border-blue-200 dark:border-blue-800 pb-2 mb-4">
                                <h3 class="text-base font-semibold leading-7 text-blue-900 dark:text-blue-100 flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                    </svg>
                                    Usuarios Plataforma ANNA
                                </h3>
                                <button type="button" @click="addAnnaCredential" class="text-xs font-bold text-blue-600 hover:text-blue-500 flex items-center gap-1 uppercase tracking-wider">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Agregar Usuario
                                </button>
                            </div>
                            
                            <div v-if="form.anna_credentials.length === 0" class="text-center py-4">
                                <p class="text-sm text-blue-500 italic">No hay usuarios de ANNA registrados para este cliente.</p>
                            </div>
                            
                            <div v-else class="space-y-4">
                                <div v-for="(cred, index) in form.anna_credentials" :key="index" class="grid grid-cols-1 md:grid-cols-3 gap-4 relative bg-white dark:bg-gray-800 p-3 rounded shadow-sm border border-blue-100 dark:border-gray-700">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase">Usuario</label>
                                        <input type="text" v-model="cred.usuario" class="mt-1 block w-full rounded-md border-gray-200 py-1 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase">Contraseña</label>
                                        <input type="text" v-model="cred.password" class="mt-1 block w-full rounded-md border-gray-200 py-1 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase">Notas</label>
                                            <input type="text" v-model="cred.observaciones" placeholder="Opcional" class="mt-1 block w-full rounded-md border-gray-200 py-1 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                        </div>
                                        <button type="button" @click="removeAnnaCredential(index)" class="mt-5 text-red-500 hover:text-red-700">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Credenciales de Portales de Pago -->
                        <div class="bg-emerald-50 dark:bg-emerald-900/10 p-4 rounded-lg border border-emerald-200 dark:border-emerald-800">
                            <div class="flex items-center justify-between border-b border-emerald-200 dark:border-emerald-800 pb-2 mb-4">
                                <h3 class="text-base font-semibold leading-7 text-emerald-900 dark:text-emerald-100 flex items-center gap-2">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                    </svg>
                                    Portales de Pago Aseguradoras
                                </h3>
                                <button type="button" @click="addPaymentCredential" class="text-xs font-bold text-emerald-600 hover:text-emerald-500 flex items-center gap-1 uppercase tracking-wider">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    Agregar Portal
                                </button>
                            </div>

                            <div v-if="form.payment_credentials.length === 0" class="text-center py-4">
                                <p class="text-sm text-emerald-500 italic">No hay credenciales de portales de pago para este cliente.</p>
                            </div>

                            <div v-else class="space-y-4">
                                <div v-for="(cred, index) in form.payment_credentials" :key="index" class="grid grid-cols-1 md:grid-cols-4 gap-4 relative bg-white dark:bg-gray-800 p-3 rounded shadow-sm border border-emerald-100 dark:border-gray-700">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase">Aseguradora</label>
                                        <select v-model="cred.aseguradora_id" class="mt-1 block w-full rounded-md border-gray-200 py-1 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                            <option value="">Seleccione...</option>
                                            <option v-for="aseg in $page.props.aseguradoras" :key="aseg.id" :value="aseg.id">{{ aseg.nombre }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase">Usuario</label>
                                        <input type="text" v-model="cred.usuario" class="mt-1 block w-full rounded-md border-gray-200 py-1 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase">Contraseña</label>
                                        <input type="text" v-model="cred.password" class="mt-1 block w-full rounded-md border-gray-200 py-1 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase">Notas</label>
                                            <input type="text" v-model="cred.observaciones" placeholder="Opcional" class="mt-1 block w-full rounded-md border-gray-200 py-1 text-sm dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                        </div>
                                        <button type="button" @click="removePaymentCredential(index)" class="mt-5 text-red-500 hover:text-red-700">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
                            
                            <Link :href="route('clientes.destroy', cliente.id)" method="delete" as="button" class="text-sm font-semibold leading-6 text-red-600 hover:text-red-500 transition-colors">
                                Eliminar Cliente
                            </Link>
                            
                            <div class="flex items-center gap-x-4">
                                <button type="button" @click="goBack" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-300 hover:underline">Cancelar</button>
                                <button type="submit" :disabled="form.processing" class="rounded-md bg-amber-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600 transition-colors disabled:opacity-75 disabled:cursor-not-allowed">
                                    <span v-if="form.processing">Actualizando...</span>
                                    <span v-else>Actualizar Cliente</span>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
