<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const showingNavigationDropdown = ref(false);
const isSidebarCollapsed = ref(false);

// Estado para los grupos de menús abiertos
const openGroups = ref({
    configuracion: false,
    operaciones: false
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const hasPermission = (moduleName) => {
    if (user.value.is_admin) return true;
    return user.value.permisos?.includes(moduleName);
};

onMounted(() => {
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState) {
        isSidebarCollapsed.value = savedState === 'true';
    }

    // Auto-abrir grupos si una ruta hija está activa
    if (isActiveGroup(['aseguradoras', 'ramos', 'riesgos', 'portales'])) openGroups.value.configuracion = true;
    if (isActiveGroup(['polizas', 'cartera'])) openGroups.value.operaciones = true;
});

watch(isSidebarCollapsed, (newVal) => {
    localStorage.setItem('sidebarCollapsed', newVal);
});

const navigation = computed(() => {
    const items = [
        { type: 'header', name: 'Gestión de Seguros' },
        {
            name: 'Clientes',
            route: 'clientes.index',
            icon: 'M17 20h5V4H2v16h5m8 0v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5m8 0H7',
            routeName: 'clientes',
            module: 'clientes'
        },
        {
            name: 'CRM (ANNA)',
            route: 'titulos-360.index',
            icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            routeName: 'titulos-360',
            module: 'titulos-360'
        },
        {
            name: 'Directorio de Pólizas',
            route: 'polizas.index',
            icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            routeName: 'polizas',
            module: 'polizas'
        },
        {
            name: 'Renovaciones',
            route: 'polizas.renewals',
            icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            routeName: 'polizas.renewals',
            module: 'renovaciones'
        },
        {
            name: 'Cartera y Pagos',
            route: 'cartera.index',
            icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            routeName: 'cartera',
            module: 'cartera'
        },

        { type: 'header', name: 'Herramientas' },
        {
            name: 'Precio Minerales',
            route: 'minerales.index',
            icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            routeName: 'minerales',
            module: 'minerales'
        },
        {
            name: 'Portales Externos',
            route: 'portales.index',
            icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
            routeName: 'portales',
            module: 'portales'
        },

        {
            name: 'Configuración',
            icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
            id: 'configuracion',
            children: [
                { name: 'Aseguradoras', route: 'aseguradoras.index', routeName: 'aseguradoras', module: 'aseguradoras' },
                { name: 'Ramos y Riesgos', route: 'ramos.index', routeName: 'ramos', module: 'ramos' },
                { name: 'Landing Page', route: 'settings.landing.index', routeName: 'settings.landing', adminOnly: true },
            ]
        },
    ];


    return items.filter(item => {
        if (item.type === 'header') return true;
        if (item.adminOnly) return user.value.is_admin;
        if (item.children) {
            item.children = item.children.filter(child => {
                if (child.adminOnly) return user.value.is_admin;
                return hasPermission(child.module);
            });
            return item.children.length > 0;
        }
        return hasPermission(item.module);
    });
});

const isActiveMenu = (item) => {
    if (!item) return false;
    
    if (item.children) {
        return item.children.some(child => child.routeName && (route().current(child.routeName + '.*') || route().current(child.routeName)));
    }
    
    if (!item.routeName) return false;
    
    // Especial para pólizas: Si estamos en renovaciones, NO marcar el directorio de pólizas
    if (item.routeName === 'polizas' && route().current('polizas.renewals')) {
        return false;
    }
    
    return route().current(item.routeName + '.*') || route().current(item.routeName);
};

const isActiveGroup = (routeNames) => {
    return routeNames.some(name => name && (route().current(name + '.*') || route().current(name)));
};

const toggleGroup = (groupId) => {
    if (isSidebarCollapsed.value) {
        isSidebarCollapsed.value = false;
        openGroups.value[groupId] = true;
    } else {
        openGroups.value[groupId] = !openGroups.value[groupId];
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex dark:bg-gray-900 font-sans">
        <!-- Sidebar Navigation (Desktop) -->
        <aside :class="[
            isSidebarCollapsed ? 'w-20' : 'w-72',
            'hidden md:flex flex-col bg-slate-900 text-white shadow-2xl flex-shrink-0 transition-all duration-300 h-screen sticky top-0 relative z-20'
        ]">
            <!-- Collapse Button -->
            <button @click="isSidebarCollapsed = !isSidebarCollapsed"
                class="absolute -right-3 top-6 bg-blue-600 border-2 border-slate-900 text-white rounded-full p-1 z-30 hover:bg-blue-500 transition-colors shadow-lg">
                <svg v-if="!isSidebarCollapsed" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div
                class="h-20 flex items-center justify-center border-b border-slate-800 bg-slate-950 px-6 transition-all duration-300">
                <Link :href="route('dashboard')" class="flex items-center h-full w-full"
                    :class="{ 'justify-center px-0': isSidebarCollapsed }">
                    <span v-show="!isSidebarCollapsed"
                        class="font-black text-2xl tracking-tighter text-white truncate italic">Canal<span
                            class="text-blue-500">360</span></span>
                    <span v-show="isSidebarCollapsed" class="font-black text-2xl text-blue-500">C<span
                            class="text-white">3</span></span>
                </Link>
            </div>

            <div class="flex-1 overflow-y-auto py-6 custom-scrollbar">
                <nav :class="[isSidebarCollapsed ? 'px-2' : 'px-4', 'space-y-1']">
                    <template v-for="(item, index) in navigation" :key="index">
                        <!-- Header de Categoría -->
                        <div v-if="item.type === 'header'" v-show="!isSidebarCollapsed"
                            class="pt-4 pb-2 px-4 text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                            {{ item.name }}
                        </div>

                        <!-- Item de Menú Simple -->
                        <Link v-else-if="!item.children" :href="route(item.route)" :class="[
                            isActiveMenu(item) ? 'bg-blue-600 text-white shadow-[0_8px_20px_rgba(37,99,235,0.25)] scale-[1.02]' : 'text-slate-400 hover:bg-slate-800 hover:text-white',
                            'group flex items-center py-3 font-bold rounded-xl transition-all duration-200',
                            isSidebarCollapsed ? 'justify-center w-full px-0' : 'px-4 text-[13px] tracking-tight'
                        ]" :title="isSidebarCollapsed ? item.name : ''">
                            <svg :class="[
                                isActiveMenu(item) ? 'text-white' : 'text-slate-500 group-hover:text-white',
                                isSidebarCollapsed ? 'mr-0' : 'mr-3.5',
                                'flex-shrink-0 h-6 w-6 transition-all duration-300'
                            ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            <span v-show="!isSidebarCollapsed" class="truncate font-sans">{{ item.name }}</span>

                            <!-- Indicador de Activo (Puntito) -->
                            <div v-if="isActiveMenu(item) && !isSidebarCollapsed"
                                class="ml-auto w-1.5 h-1.5 rounded-full bg-white opacity-50"></div>
                        </Link>

                        <!-- Grupo Colapsable (Configuración) -->
                        <div v-else class="space-y-1">
                            <button @click="toggleGroup(item.id)" :class="[
                                isActiveMenu(item) ? 'text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white',
                                'w-full group flex items-center py-3 font-bold rounded-xl transition-all duration-200 focus:outline-none',
                                isSidebarCollapsed ? 'justify-center px-0' : 'px-4 text-[13px] tracking-tight'
                            ]">
                                <svg :class="[
                                    isActiveMenu(item) ? 'text-blue-400' : 'text-slate-500 group-hover:text-white',
                                    isSidebarCollapsed ? 'mr-0' : 'mr-3.5',
                                    'flex-shrink-0 h-6 w-6 transition-all duration-300'
                                ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                </svg>
                                <span v-show="!isSidebarCollapsed" class="truncate flex-1 text-left font-sans">{{ item.name }}</span>
                                <svg v-show="!isSidebarCollapsed" :class="[openGroups[item.id] ? 'rotate-180' : '', 'h-4 w-4 transition-transform duration-200 text-slate-600']" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <!-- Sub-items -->
                            <div v-show="openGroups[item.id] && !isSidebarCollapsed" class="pl-12 space-y-1 pr-4">
                                <Link v-for="child in item.children" :key="child.name" :href="route(child.route)" :class="[
                                    isActiveGroup([child.routeName]) ? 'text-white bg-slate-800 shadow-sm' : 'text-slate-500 hover:text-white hover:bg-slate-800/50',
                                    'block py-2 px-3 text-xs font-semibold rounded-lg transition-colors'
                                ]">
                                    {{ child.name }}
                                </Link>
                            </div>
                        </div>
                    </template>
                </nav>
            </div>

            <div class="p-4 border-t border-slate-800 bg-slate-950 transition-all duration-300 overflow-hidden"
                :class="isSidebarCollapsed ? 'px-2 flex justify-center' : ''">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-lg"
                        :title="isSidebarCollapsed ? $page.props.auth.user.name : ''">
                        {{ $page.props.auth.user.name.charAt(0) }}
                    </div>
                    <div class="flex-1 min-w-0" v-show="!isSidebarCollapsed">
                        <p class="text-sm font-bold text-white truncate">{{ $page.props.auth.user.name }}</p>
                        <p class="text-[10px] uppercase font-black tracking-widest text-slate-500 truncate">{{
                            $page.props.auth.user.email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto relative custom-scrollbar">
            <!-- Top Navigation -->
            <header
                class="bg-white/95 backdrop-blur-md shadow-[0_1px_3px_rgba(0,0,0,0.05)] border-b border-gray-100 sticky top-0 z-40 p-4 h-16 flex items-center justify-between dark:bg-gray-800/95 dark:border-gray-700">
                <!-- Mobile Menu Button & Mobile Logo -->
                <div class="flex items-center md:hidden">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 dark:text-gray-400 dark:hover:text-gray-300 mr-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="text-xl font-black tracking-tighter text-slate-900 dark:text-white">Canal<span
                            class="text-blue-600">360</span></span>
                </div>

                <!-- Header Title slot (desktop) -->
                <div class="hidden md:flex items-center font-sans">
                    <slot name="header" />
                </div>

                <!-- Top Right Actions -->
                <div class="ml-auto flex items-center gap-4">
                    <!-- User Dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                class="flex items-center gap-2 text-sm font-black uppercase tracking-widest text-gray-500 hover:text-gray-900 focus:outline-none transition duration-150 ease-in-out dark:text-gray-400 dark:hover:text-gray-300">
                                <div>{{ $page.props.auth.user.name }}</div>
                                <svg class="h-4 w-4 opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')"
                                class="font-bold text-xs uppercase tracking-tight">Perfil de Usuario</DropdownLink>
                            <DropdownLink v-if="$page.props.auth.user.is_admin" :href="route('admin.users.index')"
                                class="font-bold text-xs uppercase tracking-tight">Administración de Usuarios
                            </DropdownLink>
                            <div class="border-t border-gray-100 dark:border-gray-700"></div>
                            <DropdownLink :href="route('logout')" method="post" as="button"
                                class="font-black text-xs uppercase tracking-widest text-red-600">Cerrar Sesión
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Mobile Menu Dropdown -->
            <div v-show="showingNavigationDropdown"
                class="md:hidden border-b border-gray-200 bg-white/95 backdrop-blur-md dark:bg-gray-800/95 dark:border-gray-700 sticky top-16 z-30 shadow-2xl overflow-y-auto max-h-[calc(100vh-64px)]">
                <div class="px-3 pt-4 pb-6 space-y-2 font-sans">
                    <template v-for="(item, idx) in navigation" :key="idx">
                        <!-- Header Móvil -->
                        <div v-if="item.type === 'header'"
                            class="pt-4 pb-2 px-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                            {{ item.name }}
                        </div>

                        <!-- Item Móvil Simple -->
                        <Link v-else-if="!item.children" :href="route(item.route)" :class="[
                            isActiveMenu(item) ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300',
                            'flex items-center px-4 py-3 rounded-xl text-[13px] font-bold uppercase tracking-tight transition-all'
                        ]">
                            <svg class="mr-3 h-5 w-5 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            {{ item.name }}
                        </Link>

                        <!-- Grupo Móvil (Configuración) -->
                        <div v-else class="pt-2">
                            <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1 italic opacity-70">{{ item.name }}</p>
                            <div class="space-y-1">
                                <Link v-for="child in item.children" :key="child.name" :href="route(child.route)" :class="[
                                    isActiveGroup([child.routeName]) ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400',
                                    'flex items-center px-8 py-2.5 rounded-lg text-[13px] font-medium transition-all'
                                ]">
                                    <div class="w-1.5 h-1.5 rounded-full mr-3" :class="isActiveGroup([child.routeName]) ? 'bg-blue-600' : 'bg-gray-300'"></div>
                                    {{ child.name }}
                                </Link>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Page Mobile Header Content (Visible only on mobile if slot exists) -->
            <div class="md:hidden bg-white/95 backdrop-blur-md shadow-sm px-4 py-3 dark:bg-gray-800/95 border-b border-gray-100 dark:border-gray-700 sticky top-16 z-20"
                v-if="$slots.header">
                <slot name="header" />
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 bg-gray-50 p-4 sm:p-6 lg:p-8 dark:bg-gray-900 custom-scrollbar">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(100, 116, 139, 0.2);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(100, 116, 139, 0.4);
}
</style>
