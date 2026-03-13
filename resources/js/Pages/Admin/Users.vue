<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    users: Array,
});

const userToEdit = ref(null);
const creatingUser = ref(false);
const editingUser = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    is_admin: false,
    is_active: true,
});

const openCreateModal = () => {
    creatingUser.value = true;
    form.reset();
    form.is_active = true;
};

const closeCreateModal = () => {
    creatingUser.value = false;
    form.reset();
    form.clearErrors();
};

const openEditModal = (user) => {
    userToEdit.value = user;
    editingUser.value = true;
    form.name = user.name;
    form.email = user.email;
    form.is_admin = !!user.is_admin;
    form.is_active = !!user.is_active;
    form.password = '';
    form.password_confirmation = '';
};

const closeEditModal = () => {
    editingUser.value = false;
    userToEdit.value = null;
    form.reset();
    form.clearErrors();
};

const submitCreate = () => {
    form.post(route('admin.users.store'), {
        onSuccess: () => closeCreateModal(),
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const submitUpdate = () => {
    form.put(route('admin.users.update', userToEdit.value.id), {
        onSuccess: () => closeEditModal(),
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const sendPasswordReset = (user) => {
    if (confirm(`¿Enviar enlace de recuperación a ${user.email}?`)) {
        form.post(route('admin.users.password.reset', user.id));
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};
</script>

<template>
    <Head title="Administración de Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-2">
                <div class="p-2 bg-blue-600 rounded-lg shadow-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-black uppercase tracking-tight text-gray-800 dark:text-gray-200">
                    Control de Accesos
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Action Header -->
                <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            Usuarios Registrados
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] rounded-full font-black uppercase tracking-widest">{{ users.length }}</span>
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Gestiona roles y estados de cuenta para el personal del sistema.</p>
                    </div>
                    <PrimaryButton @click="openCreateModal" class="rounded-xl px-6 py-3 font-black tracking-widest bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-100 active:scale-95 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nuevo Acceso
                    </PrimaryButton>
                </div>

                <!-- Users Table -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                    <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 dark:border-gray-700">Identidad</th>
                                    <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 dark:border-gray-700">Privilegios</th>
                                    <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 dark:border-gray-700">Estado de Acceso</th>
                                    <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 dark:border-gray-700 text-right">Gestión</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="user in users" :key="user.id" class="hover:bg-blue-50/30 dark:hover:bg-gray-900/30 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <div class="relative">
                                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600 flex items-center justify-center font-black text-lg uppercase shadow-sm group-hover:from-blue-600 group-hover:to-blue-700 group-hover:text-white transition-all duration-300">
                                                    {{ user.name.charAt(0) }}
                                                </div>
                                                <div v-if="user.is_active" class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full" title="Cuenta Activa"></div>
                                                <div v-else class="absolute -bottom-1 -right-1 w-4 h-4 bg-red-500 border-2 border-white rounded-full animate-pulse" title="Cuenta Suspendida"></div>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">{{ user.name }}</div>
                                                <div class="text-xs text-gray-400 font-medium">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div v-if="user.is_admin" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black bg-purple-100 text-purple-700 uppercase tracking-widest border border-purple-200">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M2.166 4.9L10 1.55l7.834 3.35a1 1 0 01.666.92v6.57a10 10 0 01-8.5 9.87 10 10 0 01-8.5-9.87V5.82a1 1 0 01.666-.92zM10 12.24V4.42L3.5 7.2v4.8c0 4.41 3.22 8.1 6.5 9.38 3.28-1.28 6.5-4.97 6.5-9.38V7.2L10 4.42v7.82z" clip-rule="evenodd" />
                                            </svg>
                                            Administrador
                                        </div>
                                        <div v-else class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-bold bg-gray-100 text-gray-600 uppercase tracking-widest border border-gray-200">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                            Operador
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div v-if="user.is_active" class="flex items-center gap-2 text-green-600 font-black text-[10px] uppercase tracking-widest">
                                            <div class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
                                            Habilitado
                                        </div>
                                        <div v-else class="flex items-center gap-2 text-red-600 font-black text-[10px] uppercase tracking-widest">
                                            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse shadow-[0_0_8px_rgba(239,68,68,0.5)]"></div>
                                            Suspendido
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <button 
                                                @click="sendPasswordReset(user)"
                                                class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                                title="Enviar Link de Recuperación"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                </svg>
                                            </button>
                                            <button 
                                                @click="openEditModal(user)"
                                                class="inline-flex items-center gap-2 px-4 py-2 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all duration-200 font-bold text-xs uppercase tracking-widest border border-blue-100 hover:border-blue-600 shadow-sm whitespace-nowrap"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                Configurar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-6 p-4 bg-amber-50 rounded-2xl border border-amber-100 flex gap-4 items-start">
                    <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-amber-800 mb-1">Nota de Seguridad</p>
                        <p class="text-[11px] text-amber-700 leading-normal">Por integridad de la base de datos, los usuarios no se eliminan permanentemente. Si deseas revocar un acceso, utiliza la opción <strong>"Cuenta Activa"</strong> en la configuración para suspender al usuario.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create User Modal -->
        <Modal :show="creatingUser" @close="closeCreateModal" maxWidth="md">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-blue-50 rounded-2xl text-blue-600 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Nuevo Acceso</h2>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Crear credenciales para el sistema</p>
                    </div>
                </div>

                <form @submit.prevent="submitCreate" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Nombre Completo" class="font-black text-[10px] uppercase tracking-widest text-gray-400 mb-1" />
                        <TextInput id="name" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-blue-600 focus:border-blue-600" v-model="form.name" required autofocus placeholder="Ej: Juan Pérez" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Correo Corporativo" class="font-black text-[10px] uppercase tracking-widest text-gray-400 mb-1" />
                        <TextInput id="email" type="email" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-blue-600 focus:border-blue-600" v-model="form.email" required placeholder="juan@canalasesores.com" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="password" value="Establecer Clave" class="font-black text-[10px] uppercase tracking-widest text-gray-400 mb-1" />
                            <TextInput id="password" type="password" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-blue-600 focus:border-blue-600" v-model="form.password" required autocomplete="new-password" placeholder="••••••••" />
                        </div>
                        <div>
                            <InputLabel for="password_confirmation" value="Confirmar" class="font-black text-[10px] uppercase tracking-widest text-gray-400 mb-1" />
                            <TextInput id="password_confirmation" type="password" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-blue-600 focus:border-blue-600" v-model="form.password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                        </div>
                        <InputError class="col-span-2 mt-1" :message="form.errors.password" />
                    </div>

                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <label class="flex items-center cursor-pointer group">
                            <Checkbox v-model:checked="form.is_admin" class="w-6 h-6 rounded-lg border-gray-300 text-blue-600 focus:ring-blue-600" />
                            <div class="ms-4">
                                <span class="text-sm font-black uppercase tracking-tight text-gray-800">Privilegios de Administrador</span>
                                <p class="text-[10px] text-gray-500 font-medium leading-tight mt-1">Permite gestionar otros usuarios, roles y ver auditoría.</p>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <SecondaryButton @click="closeCreateModal" class="rounded-xl font-black uppercase text-[10px] tracking-widest border-gray-200 px-6">Cancelar</SecondaryButton>
                        <PrimaryButton class="rounded-xl px-8 bg-blue-600 hover:bg-blue-700 shadow-xl shadow-blue-100 active:scale-95 transition-all font-black uppercase text-[10px] tracking-widest" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Crear Usuario
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Edit User Modal -->
        <Modal :show="editingUser" @close="closeEditModal" maxWidth="md">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-blue-600 rounded-2xl text-white shadow-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">Configurar Acceso</h2>
                        <p class="text-xs text-blue-600 font-black uppercase tracking-[0.2em]">{{ userToEdit?.name }}</p>
                    </div>
                </div>

                <form @submit.prevent="submitUpdate" class="space-y-6">
                    <div>
                        <InputLabel for="edit_name" value="Nombre del Perfil" class="font-black text-[10px] uppercase tracking-widest text-gray-400 mb-1" />
                        <TextInput id="edit_name" type="text" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-blue-600" v-model="form.name" required />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="edit_email" value="Correo Electrónico" class="font-black text-[10px] uppercase tracking-widest text-gray-400 mb-1" />
                        <TextInput id="edit_email" type="email" class="mt-1 block w-full rounded-xl border-gray-200 focus:ring-blue-600" v-model="form.email" required />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100/50 space-y-3">
                        <div class="flex items-center justify-between">
                            <InputLabel value="Cambiar Contraseña" class="font-black text-[10px] uppercase tracking-widest text-blue-700" />
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[8px] font-black rounded-full uppercase tracking-widest">Opcional</span>
                        </div>
                        <p class="text-[10px] text-blue-600/70 font-medium leading-tight">Solo llena estos campos si deseas establecer una nueva clave. Déjalos vacíos para mantener la actual.</p>
                        <div class="grid grid-cols-2 gap-4 pt-1">
                            <TextInput id="edit_password" type="password" class="mt-1 block w-full rounded-xl bg-white border-blue-100 focus:ring-blue-600" v-model="form.password" autocomplete="new-password" placeholder="Nueva clave" />
                            <TextInput id="edit_password_confirmation" type="password" class="mt-1 block w-full rounded-xl bg-white border-blue-100 focus:ring-blue-600" v-model="form.password_confirmation" autocomplete="new-password" placeholder="Confirmar" />
                        </div>
                        <InputError class="mt-1" :message="form.errors.password" />
                    </div>

                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 space-y-4">
                        <label class="flex items-center" :class="{ 'opacity-50 cursor-not-allowed': userToEdit?.id === $page.props.auth.user.id }">
                            <Checkbox v-model:checked="form.is_admin" :disabled="userToEdit?.id === $page.props.auth.user.id" class="w-6 h-6 rounded-lg border-gray-300 text-blue-600 focus:ring-blue-600" />
                            <div class="ms-4">
                                <span class="text-sm font-black uppercase tracking-tight text-gray-800">Administrador de Sistema</span>
                            </div>
                        </label>
                        
                        <label class="flex items-center" :class="{ 'opacity-50 cursor-not-allowed': userToEdit?.id === $page.props.auth.user.id }">
                            <Checkbox v-model:checked="form.is_active" :disabled="userToEdit?.id === $page.props.auth.user.id" class="w-6 h-6 rounded-lg border-gray-300 text-green-600 focus:ring-green-600" />
                            <div class="ms-4">
                                <span class="text-sm font-black uppercase tracking-tight text-gray-800">Cuenta Activa</span>
                                <p v-if="!form.is_active" class="text-[10px] text-red-600 font-black uppercase tracking-widest mt-0.5">Acceso Revocado</p>
                            </div>
                        </label>
                        
                        <div v-if="userToEdit?.id === $page.props.auth.user.id" class="flex gap-2 items-center bg-white p-2 rounded-lg border border-gray-100">
                           <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                           </svg>
                           <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">No puedes modificar tus propios permisos de acceso.</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <SecondaryButton @click="closeEditModal" class="rounded-xl font-black uppercase text-[10px] tracking-widest border-gray-200 px-6">Cancelar</SecondaryButton>
                        <PrimaryButton class="rounded-xl px-10 bg-blue-600 hover:bg-blue-700 shadow-xl shadow-blue-100 active:scale-95 transition-all font-black uppercase text-[10px] tracking-widest" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Guardar Cambios
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
.group:hover .w-12 {
    transform: scale(1.05);
}
</style>
