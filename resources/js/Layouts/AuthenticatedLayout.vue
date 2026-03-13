<script setup>
import { ref, onMounted, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const showingNavigationDropdown = ref(false);
const isSidebarCollapsed = ref(false);

onMounted(() => {
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState) {
        isSidebarCollapsed.value = savedState === 'true';
    }
});

watch(isSidebarCollapsed, (newVal) => {
    localStorage.setItem('sidebarCollapsed', newVal);
});

const navigation = [
    { name: 'Dashboard', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Clientes', route: 'clientes.index', icon: 'M17 20h5V4H2v16h5m8 0v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5m8 0H7', routeName: 'clientes' },
    { name: 'Aseguradoras', route: 'aseguradoras.index', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', routeName: 'aseguradoras' },
    { name: 'Ramos', route: 'ramos.index', icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', routeName: 'ramos' },
    { name: 'Riesgos', route: 'riesgos.index', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', routeName: 'riesgos' },
    { name: 'Pólizas', route: 'polizas.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', routeName: 'polizas' },
    { name: 'Cartera', route: '#', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', routeName: 'cartera' },
    { name: 'Landing Page', route: 'settings.landing.index', icon: 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM7 8h4v4H7V8z', routeName: 'settings.landing' },
];

const isActiveMenu = (itemRouteName) => {
    if (!itemRouteName) return false;
    return route().current(itemRouteName + '.*') || route().current(itemRouteName);
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex dark:bg-gray-900">
        <!-- Sidebar Navigation (Desktop) -->
        <aside :class="[
            isSidebarCollapsed ? 'w-20' : 'w-64',
            'hidden md:flex flex-col bg-slate-900 text-white shadow-xl flex-shrink-0 transition-all duration-300 h-screen sticky top-0 relative z-20'
        ]">
            <!-- Collapse Button -->
            <button @click="isSidebarCollapsed = !isSidebarCollapsed"
                class="absolute -right-3 top-6 bg-slate-800 border-2 border-slate-900 text-white rounded-full p-1 z-30 hover:bg-blue-600 transition-colors shadow-md">
                <svg v-if="!isSidebarCollapsed" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div
                class="h-16 flex items-center justify-center border-b border-slate-800 bg-slate-950 px-4 transition-all duration-300">
                <Link :href="route('dashboard')" class="flex items-center h-full w-full px-4"
                    :class="{ 'justify-center px-0': isSidebarCollapsed }">
                    <span v-show="!isSidebarCollapsed" class="font-black text-2xl tracking-tighter text-white truncate">Canal<span class="text-blue-500"> Asesores</span></span>
                    <span v-show="isSidebarCollapsed" class="font-black text-2xl text-blue-500">C<span class="text-white">A</span></span>
                </Link>
            </div>

            <div class="flex-1 overflow-y-auto py-4">
                <nav class="space-y-1 px-3">
                    <template v-for="item in navigation" :key="item.name">
                        <Link v-if="item.route !== '#'" :href="route(item.route)" :class="[
                            isActiveMenu(item.routeName) || route().current(item.route) ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
                            'group flex items-center py-2.5 font-medium rounded-md transition-all duration-200',
                            isSidebarCollapsed ? 'justify-center mx-1 px-0' : 'px-3 text-sm'
                        ]" :title="isSidebarCollapsed ? item.name : ''">
                            <svg :class="[
                                isActiveMenu(item.routeName) || route().current(item.route) ? 'text-white' : 'text-slate-400 group-hover:text-white',
                                isSidebarCollapsed ? 'mr-0' : 'mr-3',
                                'flex-shrink-0 h-6 w-6 transition-all duration-300'
                            ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            <span v-show="!isSidebarCollapsed" class="truncate transition-all duration-300">{{ item.name
                                }}</span>
                        </Link>
                        <div v-else :class="[
                            isActiveMenu(item.routeName) ? 'bg-blue-600 text-white shadow-md' : 'text-slate-300 hover:bg-slate-800 hover:text-white',
                            'group flex items-center py-2.5 font-medium rounded-md transition-all duration-200 cursor-pointer opacity-70',
                            isSidebarCollapsed ? 'justify-center mx-1 px-0' : 'px-3 text-sm'
                        ]" :title="isSidebarCollapsed ? item.name + ' (Próximamente)' : 'Próximamente'">
                            <svg :class="[
                                isActiveMenu(item.routeName) ? 'text-white' : 'text-slate-400 group-hover:text-white',
                                isSidebarCollapsed ? 'mr-0' : 'mr-3',
                                'flex-shrink-0 h-6 w-6 transition-all duration-300'
                            ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            <span v-show="!isSidebarCollapsed" class="truncate transition-all duration-300">{{ item.name
                                }}</span>
                        </div>
                    </template>
                </nav>
            </div>

            <div class="p-4 border-t border-slate-800 bg-slate-950 transition-all duration-300 overflow-hidden"
                :class="isSidebarCollapsed ? 'px-2 flex justify-center' : ''">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                        :title="isSidebarCollapsed ? $page.props.auth.user.name : ''">
                        {{ $page.props.auth.user.name.charAt(0) }}
                    </div>
                    <div class="flex-1 min-w-0" v-show="!isSidebarCollapsed">
                        <p class="text-sm font-medium text-white truncate">{{ $page.props.auth.user.name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $page.props.auth.user.email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen relative">
            <!-- Top Navigation -->
            <header
                class="bg-white shadow-sm border-b border-gray-200 z-10 p-4 h-16 flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
                <!-- Mobile Menu Button & Mobile Logo -->
                <div class="flex items-center md:hidden">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="text-xl font-black tracking-tighter text-slate-900 dark:text-white ml-3">Canal<span class="text-blue-600"> Asesores</span></span>
                </div>

                <!-- Header Title slot (desktop) -->
                <div class="hidden md:flex items-center">
                    <slot name="header" />
                </div>

                <!-- Top Right Actions -->
                <div class="ml-auto flex items-center gap-4">
                    <!-- Notifications (Placeholder) -->
                    <button class="text-gray-400 hover:text-gray-500 relative p-1">
                        <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>

                    <!-- User Dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out dark:text-gray-400 dark:hover:text-gray-300">
                                <div>{{ $page.props.auth.user.name }}</div>
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')">Perfil</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Cerrar Sesión</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Mobile Menu Dropdown -->
            <div v-show="showingNavigationDropdown"
                class="md:hidden border-b border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700">
                <div class="px-2 pt-2 pb-3 space-y-1 shadow-inner">
                    <template v-for="item in navigation" :key="item.name">
                        <Link v-if="item.route !== '#'" :href="route(item.route)" :class="[
                            route().current(item.route) ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800',
                            'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out dark:text-gray-300'
                        ]">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        :d="item.icon" />
                                </svg>
                                {{ item.name }}
                            </div>
                        </Link>
                        <div v-else :class="[
                            isActiveMenu(item.routeName) ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800',
                            'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out opacity-70 dark:text-gray-300'
                        ]">
                            <div class="flex items-center">
                                <svg class="mr-3 h-5 w-5 opacity-70" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        :d="item.icon" />
                                </svg>
                                {{ item.name }}
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Page Mobile Header Content (Visible only on mobile if slot exists) -->
            <div class="md:hidden bg-white shadow-sm px-4 py-3 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700"
                v-if="$slots.header">
                <slot name="header" />
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-4 sm:p-6 lg:p-8 dark:bg-gray-900">
                <slot />
            </main>
        </div>
    </div>
</template>
