<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    settings: Object,
    aseguradoras: Array,
});

const scrolled = ref(false);

onMounted(() => {
    window.addEventListener('scroll', () => {
        scrolled.value = window.scrollY > 20;
    });
});

const serviceCategories = computed(() => [
    {
        key: 'cat_1',
        icon: 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z',
        items: (props.settings.landing_service_cat_1_items || '').split(',')
    },
    {
        key: 'cat_2',
        icon: 'M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z',
        items: (props.settings.landing_service_cat_2_items || '').split(',')
    },
    {
        key: 'cat_3',
        icon: 'M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42.99L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99z',
        items: (props.settings.landing_service_cat_3_items || '').split(',')
    },
    {
        key: 'cat_4',
        icon: 'M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm10 12h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V9h2v2zm0-4h-2V5h2v2zm4 12h-2v-2h2v2zm0-4h-2v-2h2v2z',
        items: (props.settings.landing_service_cat_4_items || '').split(',')
    }
]);
</script>

<template>

    <Head>
        <title>Canal Asesores - Seguros & Protección Inteligente en Colombia</title>
        <meta name="description" content="Protege lo que más quieres con Canal Asesores. Expertos en seguros de autos, vida, hogar, salud y empresariales en Colombia. Consultoría personalizada y respaldo garantizado." />
        <meta name="keywords" content="seguros colombia, agencia de seguros, seguros de autos, seguros de vida, soat, seguros de hogar, protección financiera, canal asesores" />
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website" />
        <meta property="og:title" content="Canal Asesores - Seguros & Protección Inteligente" />
        <meta property="og:description" content="Soluciones integrales de protección para ti, tu familia y tu empresa. Compara y asegura con los mejores." />
        <meta property="og:image" :content="settings.landing_hero_image" />

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:title" content="Canal Asesores - Seguros & Protección Inteligente" />
        <meta property="twitter:description" content="Soluciones integrales de protección para ti, tu familia y tu empresa." />
        <meta property="twitter:image" :content="settings.landing_hero_image" />
        
        <link rel="canonical" href="https://canalasesores.com" />
    </Head>

    <div
        class="min-h-screen bg-white text-slate-900 font-sans selection:bg-blue-100 selection:text-blue-900 overflow-x-hidden">

        <!-- Navbar Estilo Glassmorphism -->
        <nav :class="[scrolled ? 'bg-white/80 backdrop-blur-md border-b border-slate-200' : 'bg-transparent']"
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo Section (Solo Texto) -->
                    <div class="flex items-center">
                        <span class="text-3xl font-extrabold tracking-tighter text-slate-900">Canal<span
                                class="text-blue-600"> Asesores</span></span>
                    </div>

                    <!-- Desktop Nav -->
                    <div class="hidden md:flex items-center gap-8">
                        <a href="#inicio" class="text-sm font-medium hover:text-blue-600 transition-colors">Inicio</a>
                        <a href="#servicios"
                            class="text-sm font-medium hover:text-blue-600 transition-colors">Servicios</a>
                        <a href="#nosotros"
                            class="text-sm font-medium hover:text-blue-600 transition-colors">Nosotros</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section - Sophistication 2026 -->
        <section id="inicio" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
            <!-- Background Elements -->
            <div
                class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-[800px] h-[800px] bg-blue-50 rounded-full blur-3xl opacity-60">
            </div>
            <div
                class="absolute bottom-0 left-0 translate-y-1/4 -translate-x-1/4 w-[600px] h-[600px] bg-indigo-50 rounded-full blur-3xl opacity-60">
            </div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="text-left animate-fade-in-up">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-bold tracking-wider uppercase mb-6 border border-blue-100">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                            </span>
                            Protección y Confianza
                        </div>
                        <h1 class="text-5xl lg:text-7xl font-extrabold text-slate-900 leading-[1.1] mb-8">
                            {{ settings.landing_hero_title }}
                        </h1>
                        <p class="text-xl text-slate-600 mb-10 leading-relaxed max-w-xl">
                            {{ settings.landing_hero_description }}
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a :href="'https://wa.me/' + settings.landing_whatsapp_number" target="_blank"
                                class="px-8 py-4 bg-blue-600 text-white text-lg font-bold rounded-2xl shadow-xl shadow-blue-200 hover:bg-blue-500 hover:-translate-y-1 transition-all text-center">
                                {{ settings.landing_cta_text }}
                            </a>
                            <a href="#servicios"
                                class="px-8 py-4 bg-white text-slate-900 text-lg font-bold rounded-2xl border border-slate-200 hover:bg-slate-50 transition-all text-center">
                                Explorar Servicios
                            </a>
                        </div>

                        <div class="mt-12 overflow-hidden relative group">
                            <div class="flex items-center gap-12 animate-scroll hover:pause">
                                <!-- Render logos multiple times for infinite effect -->
                                <div v-for="n in 3" :key="n" class="flex items-center gap-12 flex-shrink-0">
                                    <div v-for="aseguradora in aseguradoras" :key="aseguradora.id + '-' + n"
                                        class="flex items-center justify-center w-24 h-24 grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all duration-500 transform hover:scale-110">
                                        <img :src="'/storage/' + aseguradora.logo" :alt="'Aseguradora aliada ' + aseguradora.nombre + ' en Canal Asesores'"
                                            class="max-w-full max-h-full object-contain p-2" />
                                    </div>
                                </div>
                            </div>
                            <div
                                class="mt-4 text-xs text-slate-400 font-bold uppercase tracking-widest text-center md:text-left">
                                Aliados Estratégicos que respaldan tu seguridad
                            </div>
                        </div>
                    </div>

                    <!-- Hero Image / Visual -->
                    <div class="relative animate-fade-in group">
                        <div
                            class="absolute -inset-4 bg-gradient-to-tr from-blue-100 to-indigo-100 rounded-[2.5rem] blur-2xl opacity-40 group-hover:opacity-60 transition duration-1000">
                        </div>
                        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl border border-white/50">
                            <img :src="settings.landing_hero_image" alt="Asesoría integral en seguros y protección - Canal Asesores"
                                class="w-full h-auto object-cover transform transition duration-1000 group-hover:scale-105" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>

                            <!-- Floating Card -->
                            <div
                                class="absolute bottom-8 left-8 right-8 bg-white/10 backdrop-blur-xl border border-white/20 p-6 rounded-2xl">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-white font-bold">Respaldo Garantizado</div>
                                        <div class="text-white/80 text-xs">Monitoreo de vigencia en tiempo real</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="servicios" class="py-24 bg-slate-50">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-20">
                    <h2 class="text-blue-600 font-bold tracking-widest uppercase text-sm mb-4">Nuestras Coberturas Integrales</h2>
                    <p class="text-4xl font-bold text-slate-900 mb-6">Protección especializada para cada área de tu vida</p>
                    <div class="h-1.5 w-20 bg-blue-500 mx-auto rounded-full"></div>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="(category, index) in serviceCategories" :key="category.key"
                        class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 group">
                        <div
                            class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path :d="category.icon" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">{{ settings['landing_service_cat_' + (index +
1) + '_title'] }}</h3>
                        <p class="text-slate-500 text-xs mb-4 leading-relaxed line-clamp-2">Protección personalizada basada en las mejores coberturas del mercado nacional.</p>
                        <ul class="space-y-3">
                            <li v-for="item in category.items" :key="item"
                                class="flex items-center gap-2 text-slate-500 text-sm font-medium">
                                <div class="w-1 h-1 bg-blue-400 rounded-full"></div>
                                {{ item }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Technology Section -->
        <section id="nosotros" class="py-24 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="bg-slate-900 rounded-[3rem] p-8 lg:p-20 relative overflow-hidden">
                    <!-- Decor -->
                    <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/20 rounded-full blur-[100px]"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-[80px]"></div>

                    <div class="grid lg:grid-cols-2 gap-16 items-center relative z-10">
                        <div>
                             <h2 class="text-3xl lg:text-5xl font-bold text-white mb-8 leading-tight tracking-tight">
                                {{ settings.landing_tech_title }}
                            </h2>
                            <p class="text-slate-400 text-lg mb-10 leading-relaxed">
                                {{ settings.landing_tech_description }}
                            </p>

                            <ul class="space-y-6">
                                <li v-for="item in (settings.landing_tech_features || '').split(',')" :key="item"
                                    class="flex items-center gap-4 text-white font-medium">
                                    <div
                                        class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-500/20 border border-blue-500/30 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    {{ item }}
                                </li>
                            </ul>
                        </div>
                        <div class="relative">
                            <div
                                class="bg-gradient-to-b from-slate-800 to-slate-900 rounded-3xl p-4 shadow-2xl border border-slate-700">
                                <div
                                    class="bg-slate-950 rounded-2xl h-80 flex items-center justify-center border border-slate-800">
                                    <!-- Representación de UI -->
                                    <div class="w-full max-w-[80%] space-y-4">
                                        <div class="h-4 w-1/3 bg-slate-800 rounded-full"></div>
                                        <div class="h-10 w-full bg-slate-900 rounded-xl flex items-center px-4 gap-3">
                                            <div class="h-4 w-4 rounded-full bg-blue-500"></div>
                                            <div class="h-2 w-1/2 bg-slate-800 rounded-full"></div>
                                        </div>
                                        <div class="h-10 w-full bg-slate-900 rounded-xl flex items-center px-4 gap-3">
                                            <div class="h-4 w-4 rounded-full bg-cyan-500"></div>
                                            <div class="h-2 w-1/3 bg-slate-800 rounded-full"></div>
                                        </div>
                                        <div
                                            class="h-10 w-full bg-slate-900 rounded-xl flex items-center px-4 gap-3 opacity-50">
                                            <div class="h-4 w-4 rounded-full bg-indigo-500"></div>
                                            <div class="h-2 w-1/4 bg-slate-800 rounded-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Floating Badge -->
                            <div
                                class="absolute -top-6 -right-6 bg-blue-600 text-white p-6 rounded-3xl shadow-xl shadow-blue-500/30 font-bold rotate-12">
                                100% Digital
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-50 pt-20 pb-10 border-t border-slate-200">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                    <div class="lg:col-span-1">
                        <div class="flex items-center mb-8">
                            <span class="text-2xl font-extrabold tracking-tighter text-slate-900">Canal<span
                                    class="text-blue-600"> Asesores</span></span>
                        </div>
                        <p class="text-slate-500 text-sm leading-relaxed">
                            Líderes en gestión tecnológica de riesgos y seguros en la región. Seguridad real para
                            tiempos modernos.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 mb-6 uppercase text-xs tracking-widest">Navegación</h4>
                        <ul class="space-y-4 text-sm text-slate-500">
                            <li><a href="#inicio" class="hover:text-blue-600 transition-colors">Inicio</a></li>
                            <li><a href="#servicios" class="hover:text-blue-600 transition-colors">Servicios</a></li>
                            <li><a href="#nosotros" class="hover:text-blue-600 transition-colors">Nosotros</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 mb-6 uppercase text-xs tracking-widest">Contacto</h4>
                        <div class="text-sm text-slate-500 leading-relaxed space-y-3">
                            <a :href="'https://wa.me/57' + settings.contact_person_1_phone?.replace(/\s+/g, '')"
                                target="_blank" class="flex items-center gap-2 hover:text-green-600 transition-colors">
                                <span class="p-1 bg-green-100 rounded-full"><svg class="w-3 h-3 text-green-600"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.485 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.886.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.894 4.44-9.899 9.891 0 2.15.546 3.707 1.554 5.437l-1.01 3.691 3.955-1.037zm12.633-6.432c-.327-.164-1.93-.953-2.229-1.063-.3-.109-.517-.164-.735.164-.216.328-.842 1.063-1.032 1.281-.19.219-.381.246-.708.082-.327-.164-1.38-.508-2.628-1.622-.971-.867-1.626-1.938-1.817-2.265-.19-.328-.02-.505.143-.668.148-.146.327-.382.49-.573.163-.19.218-.327.327-.546.109-.219.054-.41-.028-.573-.081-.164-.735-1.771-1.007-2.427-.265-.64-.537-.554-.735-.563-.19-.01-.408-.012-.627-.012s-.573.082-.871.41c-.299.327-1.144 1.119-1.144 2.73 0 1.611 1.171 3.166 1.334 3.385.163.218 2.304 3.518 5.581 4.938.779.336 1.388.538 1.861.689.782.248 1.494.213 2.056.129.626-.093 1.93-.789 2.199-1.551.274-.762.274-1.416.192-1.551-.082-.137-.294-.218-.621-.382z" />
                                    </svg></span>
                                <strong>{{ settings.contact_person_1_name }}:</strong> {{
                                settings.contact_person_1_phone }}
                            </a>
                            <a :href="'https://wa.me/57' + settings.contact_person_2_phone?.replace(/\s+/g, '')"
                                target="_blank" class="flex items-center gap-2 hover:text-green-600 transition-colors">
                                <span class="p-1 bg-green-100 rounded-full"><svg class="w-3 h-3 text-green-600"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.485 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.886.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.894 4.44-9.899 9.891 0 2.15.546 3.707 1.554 5.437l-1.01 3.691 3.955-1.037zm12.633-6.432c-.327-.164-1.93-.953-2.229-1.063-.3-.109-.517-.164-.735.164-.216.328-.842 1.063-1.032 1.281-.19.219-.381.246-.708.082-.327-.164-1.38-.508-2.628-1.622-.971-.867-1.626-1.938-1.817-2.265-.19-.328-.02-.505.143-.668.148-.146.327-.382.49-.573.163-.19.218-.327.327-.546.109-.219.054-.41-.028-.573-.081-.164-.735-1.771-1.007-2.427-.265-.64-.537-.554-.735-.563-.19-.01-.408-.012-.627-.012s-.573.082-.871.41c-.299.327-1.144 1.119-1.144 2.73 0 1.611 1.171 3.166 1.334 3.385.163.218 2.304 3.518 5.581 4.938.779.336 1.388.538 1.861.689.782.248 1.494.213 2.056.129.626-.093 1.93-.789 2.199-1.551.274-.762.274-1.416.192-1.551-.082-.137-.294-.218-.621-.382z" />
                                    </svg></span>
                                <strong>{{ settings.contact_person_2_name }}:</strong> {{
                                settings.contact_person_2_phone }}
                            </a>
                            <span class="block pt-2">{{ settings.contact_email }}</span>
                        </div>
                    </div>
                </div>
                <!-- Sección Discreta para Operativos -->
                <div
                    class="pt-8 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="text-slate-400 text-xs">
                        &copy; {{ new Date().getFullYear() }} Canal Asesores. Todos los derechos reservados.
                    </div>
                    <div class="flex items-center gap-4">
                        <Link :href="route('login')"
                            class="text-blue-600/60 hover:text-blue-600 text-[11px] font-bold uppercase tracking-widest border border-blue-100 px-4 py-2 rounded-full hover:bg-blue-50 transition-all">
                            Portales Operativos
                        </Link>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Floating WhatsApp Button -->
        <a :href="'https://wa.me/' + settings.landing_whatsapp_number?.replace(/\s+/g, '')" target="_blank"
            class="fixed bottom-8 right-8 z-50 bg-green-500 text-white p-4 rounded-full shadow-2xl hover:bg-green-600 transition-all hover:scale-110 group animate-bounce-subtle">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.485 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.886.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.894 4.44-9.899 9.891 0 2.15.546 3.707 1.554 5.437l-1.01 3.691 3.955-1.037zm12.633-6.432c-.327-.164-1.93-.953-2.229-1.063-.3-.109-.517-.164-.735.164-.216.328-.842 1.063-1.032 1.281-.19.219-.381.246-.708.082-.327-.164-1.38-.508-2.628-1.622-.971-.867-1.626-1.938-1.817-2.265-.19-.328-.02-.505.143-.668.148-.146.327-.382.49-.573.163-.19.218-.327.327-.546.109-.219.054-.41-.028-.573-.081-.164-.735-1.771-1.007-2.427-.265-.64-.537-.554-.735-.563-.19-.01-.408-.012-.627-.012s-.573.082-.871.41c-.299.327-1.144 1.119-1.144 2.73 0 1.611 1.171 3.166 1.334 3.385.163.218 2.304 3.518 5.581 4.938.779.336 1.388.538 1.861.689.782.248 1.494.213 2.056.129.626-.093 1.93-.789 2.199-1.551.274-.762.274-1.416.192-1.551-.082-.137-.294-.218-.621-.382z" />
            </svg>
            <span
                class="absolute right-full mr-4 bg-white text-slate-900 px-4 py-2 rounded-xl text-xs font-bold shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none border border-slate-100">
                ¿En qué podemos ayudarte?
            </span>
        </a>
    </div>
</template>

<style scoped>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
}

.animate-fade-in {
    animation: fade-in 1.2s ease-out forwards;
}

@keyframes scroll {
    0% {
        transform: translateX(0);
    }

    100% {
        transform: translateX(calc(-100% / 3));
    }
}

.animate-scroll {
    display: flex;
    width: max-content;
    animation: scroll 12s linear infinite;
}

.pause {
    animation-play-state: paused;
}

@keyframes bounce-subtle {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-5px);
    }
}

.animate-bounce-subtle {
    animation: bounce-subtle 2s infinite ease-in-out;
}

html {
    scroll-behavior: smooth;
}
</style>
