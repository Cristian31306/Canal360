<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    minerales: Array,
});

const isEditing = ref(null);

const form = useForm({
    nombre: '',
    activo: true,
});

const editform = useForm({
    nombre: '',
    activo: true,
});

const submit = () => {
    form.post(route('cat-minerales.store'), {
        onSuccess: () => {
            form.reset();
        }
    });
};

const update = (id) => {
    editform.put(route('cat-minerales.update', id), {
        onSuccess: () => {
            isEditing.value = null;
        }
    });
};

const startEdit = (mineral) => {
    isEditing.value = mineral.id;
    editform.nombre = mineral.nombre;
    editform.activo = !!mineral.activo;
};

const confirmation = ref({
    show: false,
    title: '',
    message: '',
    callback: null
});

const deleteMineral = (mineral) => {
    confirmation.value = {
        show: true,
        title: 'Eliminar Mineral',
        message: `¿Estás seguro de que deseas eliminar ${mineral.nombre}? Esto solo será posible si no tiene precios registrados.`,
        callback: () => router.delete(route('cat-minerales.destroy', mineral.id))
    };
};
</script>

<template>
    <Head title="Configuración de Minerales" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('minerales.index')" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <h2 class="text-2xl font-bold leading-tight text-gray-900 border-l-4 border-amber-600 pl-3 dark:text-gray-100 uppercase">
                    Configuración <span class="text-amber-600">de</span> Minerales
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Agregar Nuevo -->
                <div class="bg-white dark:bg-gray-800 shadow-xl shadow-amber-500/5 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="p-8">
                        <h3 class="text-xs font-black text-amber-600 uppercase tracking-widest mb-6">Agregar Nuevo Mineral</h3>
                        <form @submit.prevent="submit" class="flex items-end gap-6">
                            <div class="flex-1">
                                <InputLabel for="nombre" value="Nombre del Mineral" class="uppercase text-[10px] font-black tracking-widest text-gray-400 mb-2" />
                                <TextInput id="nombre" v-model="form.nombre" type="text" class="block w-full rounded-xl border-gray-200 focus:ring-amber-500" placeholder="Ej: Cobre, Paladio..." required />
                                <div v-if="form.errors.nombre" class="mt-2 text-xs font-bold text-red-600 uppercase">{{ form.errors.nombre }}</div>
                            </div>
                            <PrimaryButton :disabled="form.processing" class="rounded-xl px-8 bg-amber-600 hover:bg-amber-500 shadow-lg shadow-amber-500/20 uppercase tracking-widest text-xs font-bold h-[42px]">
                                Agregar
                            </PrimaryButton>
                        </form>
                    </div>
                </div>

                <!-- Listado de Minerales -->
                <div class="bg-white dark:bg-gray-800 shadow-xl shadow-amber-500/5 rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                    <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Nombre</th>
                                    <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Slug (URL)</th>
                                    <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Estado</th>
                                    <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="mineral in minerales" :key="mineral.id" class="hover:bg-amber-50/20 dark:hover:bg-amber-900/10 transition-colors">
                                    <td class="px-6 py-6">
                                        <div v-if="isEditing === mineral.id" class="flex items-center gap-4">
                                            <TextInput v-model="editform.nombre" type="text" class="text-sm py-1 rounded-lg w-full" />
                                        </div>
                                        <span v-else class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ mineral.nombre }}</span>
                                    </td>
                                    <td class="px-6 py-6">
                                        <span class="text-xs text-gray-400 font-mono">{{ mineral.slug }}</span>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div v-if="isEditing === mineral.id">
                                            <select v-model="editform.activo" class="text-xs py-1 rounded-lg border-gray-200 dark:bg-gray-900 dark:text-gray-300">
                                                <option :value="true">Activo</option>
                                                <option :value="false">Inactivo</option>
                                            </select>
                                        </div>
                                        <span v-else :class="[mineral.activo ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50', 'text-[10px] font-black px-2 py-1 rounded uppercase tracking-widest']">
                                            {{ mineral.activo ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-6 text-right">
                                        <div v-if="isEditing === mineral.id" class="flex justify-end gap-2">
                                            <button @click="update(mineral.id)" class="text-[10px] font-black uppercase text-emerald-600 hover:text-emerald-700 underline tracking-widest">Guardar</button>
                                            <button @click="isEditing = null" class="text-[10px] font-black uppercase text-gray-400 hover:text-gray-600 underline tracking-widest">Cancelar</button>
                                        </div>
                                        <div v-else class="flex justify-end gap-2">
                                            <button @click="startEdit(mineral)" class="p-2 text-gray-400 hover:text-amber-600 transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M18.364 5.636l-3.536 3.536m0 0l3.536 3.536m-3.536-3.536L15 4" />
                                                </svg>
                                            </button>
                                            <button v-if="!['oro', 'plata', 'platino'].includes(mineral.slug)" @click="deleteMineral(mineral)" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmationModal
            :show="confirmation.show"
            :title="confirmation.title"
            :message="confirmation.message"
            type="danger"
            confirm-label="Eliminar"
            @close="confirmation.show = false"
            @confirm="() => {
                confirmation.show = false;
                if (confirmation.callback) confirmation.callback();
            }"
        />
    </AuthenticatedLayout>
</template>
