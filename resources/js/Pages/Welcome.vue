<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    settings: Object,
    aseguradoras: Array,
});

// Cálculo dinámico de años basado en referencia 2021
const currentYear = new Date().getFullYear();
const yearsMarket = currentYear - 2006;  // 15 años en 2021 -> Inicio 2006
const yearsVilma = currentYear - 2011;   // 10 años en 2021 -> Inicio 2011

const scrolled = ref(false);
const mobileMenuOpen = ref(false);
const currentProductIndex = ref(0);
const autoplayInterval = ref(null);
const windowWidth = ref(0);

const visibleItems = computed(() => {
    if (windowWidth.value >= 1024) return 3;
    if (windowWidth.value >= 768) return 2;
    return 1;
});

const maxIndex = computed(() => {
    return Math.max(0, (productos.value?.length || 0) - visibleItems.value);
});

const nextProduct = () => {
    if (currentProductIndex.value >= maxIndex.value) {
        currentProductIndex.value = 0;
    } else {
        currentProductIndex.value++;
    }
};

const prevProduct = () => {
    if (currentProductIndex.value <= 0) {
        currentProductIndex.value = maxIndex.value;
    } else {
        currentProductIndex.value--;
    }
};

const startAutoplay = () => {
    stopAutoplay();
    autoplayInterval.value = setInterval(nextProduct, 5000);
};

const stopAutoplay = () => {
    if (autoplayInterval.value) clearInterval(autoplayInterval.value);
};

const updateWidth = () => {
    windowWidth.value = window.innerWidth;
};

onMounted(() => {
    updateWidth();
    window.addEventListener('resize', updateWidth);
    window.addEventListener('scroll', () => {
        scrolled.value = window.scrollY > 20;
    });
    startAutoplay();
});

onUnmounted(() => {
    window.removeEventListener('resize', updateWidth);
    stopAutoplay();
});

// Productos detallados con valor real
const productos = computed(() => [
    {
        id: 'auto',
        nombre: 'Automóviles',
        iconName: 'car',
        descripcion: 'Protección para tu vehículo frente a daños, hurto y daños a terceros (RCE).',
        beneficios: ['Responsabilidad Civil Extracontractual', 'Asistencia vial 24/7', 'Cobertura de daños propios', 'Asesoría en siniestros'],
        destaque: 'Protección Integral'
    },
    {
        id: 'hogar',
        nombre: 'Hogar / Multiriesgo',
        iconName: 'home',
        descripcion: 'Resguarda tu vivienda y contenidos contra incendio, terremoto o robo.',
        beneficios: ['Cobertura de estructura', 'Protección de contenidos', 'Resp. Civil familiar', 'Servicios de plomería/cerrajería'],
        destaque: 'Tu Refugio Seguro'
    },
    {
        id: 'incendio',
        nombre: 'Incendio y Aliados',
        iconName: 'flame',
        descripcion: 'Protección esencial para empresas y locales comerciales ante imprevistos.',
        beneficios: ['Cobertura multiriesgo empresarial', 'Daños por agua e inundación', 'Pérdidas de beneficios', 'Huelga y asonada'],
        destaque: 'Empresa Protegida'
    },
    {
        id: 'transporte',
        nombre: 'Transporte',
        iconName: 'truck',
        descripcion: 'Cubre tus mercancías durante traslados terrestres, marítimos o aéreos.',
        beneficios: ['Cobertura de carga nacional/intl', 'Seguro para transportadores', 'Amparos contra robo', 'Protección de logística'],
        destaque: 'Logística Segura'
    },
    {
        id: 'cumplimiento',
        nombre: 'Cumplimiento',
        iconName: 'file-text',
        descripcion: 'Garantiza el cumplimiento de contratos estatales o privados ante terceros.',
        beneficios: ['Garantía de seriedad de oferta', 'Buen manejo de anticipos', 'Calidad de bienes/servicios', 'Estabilidad de obra'],
        destaque: 'Respaldo Contractual'
    },
    {
        id: 'rce',
        nombre: 'Responsabilidad Civil (RCE)',
        iconName: 'shield',
        descripcion: 'Protege tu patrimonio ante indemnizaciones por daños accidentales a terceros.',
        beneficios: ['Defensa jurídica incluida', 'RCE para predios y labores', 'RCE profesional', 'Protección patrimonial'],
        destaque: 'Blindaje Legal'
    },
    {
        id: 'vida',
        nombre: 'Vida (Individual o Grupo)',
        iconName: 'heart',
        descripcion: 'Respaldo económico para tus beneficiarios ante fallecimiento o incapacidad.',
        beneficios: ['Indemnización por muerte', 'Renta diaria por hospitalización', 'Cobertura enfermedades graves', 'Asesoría sucesoral'],
        destaque: 'Legado de Amor'
    },
    {
        id: 'salud',
        nombre: 'Salud Premium',
        iconName: 'activity',
        descripcion: 'Medicina prepagada con acceso directo a especialistas y mejores clínicas.',
        beneficios: ['Acceso directo a especialistas', 'Habitaciones individuales', 'Urgencias internacionales', 'Sin periodos de carencia'],
        destaque: 'Bienestar Superior'
    },
    {
        id: 'accidentes',
        nombre: 'Accidentes Personales',
        iconName: 'bandage',
        descripcion: 'Cobertura inmediata ante gastos médicos o eventos derivados de accidentes.',
        beneficios: ['Renta por incapacidad', 'Gastos de curación', 'Muerte accidental', 'Desmembración'],
        destaque: 'Respuesta Rápida'
    },
    {
        id: 'educacion',
        nombre: 'Educación',
        iconName: 'graduation-cap',
        descripcion: 'Pólizas diseñadas para garantizar la educación superior de tus hijos.',
        beneficios: ['Ahorro educativo garantizado', 'Renta para manutención', 'Cobertura por incapacidad tutor', 'Flexibilidad de retiros'],
        destaque: 'Futuro Asegurado'
    },
    {
        id: 'exequial',
        nombre: 'Exequial',
        iconName: 'flower',
        descripcion: 'Cubre todos los gastos y trámites funerarios en momentos difíciles.',
        beneficios: ['Asistencia nacional', 'Repatriación incluida', 'Cremación u osario', 'Apoyo psicológico familiar'],
        destaque: 'Apoyo Humano'
    }
]);



const procesos = [
    {
        numero: '1',
        titulo: 'Asesoría',
        descripcion: 'Experto dedicado a tus necesidades',
        iconName: 'user'
    },
    {
        numero: '2',
        titulo: 'Cotización',
        descripcion: 'Información clara y precisa en minutos',
        iconName: 'clipboard'
    },
    {
        numero: '3',
        titulo: 'Contratación',
        descripcion: 'Proceso simple y seguro',
        iconName: 'check-circle'
    },
    {
        numero: '4',
        titulo: 'Soporte',
        descripcion: 'Acompañamiento durante todo tu viaje',
        iconName: 'users'
    }
];


const testimonios = [
    {
        nombre: 'María González',
        rol: 'Propietaria',
        texto: 'Excelente servicio. El proceso fue rápido y sin complicaciones. Muy recomendado.',
        calificacion: 5
    },
    {
        nombre: 'Carlos Ruiz',
        rol: 'Empresario',
        texto: 'La mejor opción en seguros. Profesionales que realmente se preocupan.',
        calificacion: 5
    },
    {
        nombre: 'Ana Martínez',
        rol: 'Médica',
        texto: 'Servicio de atención excepcional. Resolvieron mi caso muy rápido.',
        calificacion: 5
    }
];

const valores = [
    {
        titulo: 'Transparencia',
        descripcion: 'Sin letras pequeñas. Sabes exactamente qué está cubierto.',
        iconName: 'eye'
    },
    {
        titulo: 'Rapidez',
        descripcion: 'Respuestas en minutos, no en días.',
        iconName: 'zap'
    },
    {
        titulo: 'Experiencia',
        descripcion: `${yearsMarket}+ años en el mercado asegurador colombiano.`,
        iconName: 'award'
    },
    {
        titulo: 'Disponibilidad',
        descripcion: 'Atención humana cuando la necesitas.',
        iconName: 'smartphone'
    }
];


const equipo = [
    {
        nombre: 'Luciano Canal',
        rol: 'Socio y Fundador',
        bio: 'Ingeniero Civil y especialista en gestión ambiental con más de 20 años de experiencia en el sector minero. Su conocimiento técnico es el puente vital entre las necesidades del gremio y las exigencias de las aseguradoras.',
        especialidad: 'Pólizas Mineroambientales',
        image: '/images/landing/luciano.webp'
    },
    {
        nombre: 'Vilma Delgado',
        rol: 'Socia Fundadora y Gerente General',
        bio: `Ingeniero Civil con más de ${yearsVilma} años de trayectoria en el sector asegurador. Experta en pólizas de disposiciones legales y gestión estratégica de requerimientos ante aseguradoras.`,
        especialidad: 'Disposiciones Legales',
        image: '/images/landing/vilma.webp'
    },
    {
        nombre: 'Juan David Canal',
        rol: 'Gerente Comercial',
        bio: 'Ingeniero Industrial y Magister en Finanzas. Enfocado en optimizar el valor para el cliente mediante negociaciones de alto nivel y beneficios exclusivos en procesos y pagos.',
        especialidad: 'Estrategia Financiera',
        image: '/images/landing/juan_david.webp'
    }
];

const faqs = [
    {
        pregunta: '¿En cuánto tiempo recibo mi cotización?',
        respuesta: 'La agilidad es uno de nuestros pilares fundamentales. Procesamos cada solicitud de manera prioritaria para entregarte una comparativa técnica en el menor tiempo posible, asegurando que cuentes con la información necesaria para decidir sin esperas innecesarias.'
    },
    {
        pregunta: '¿Qué tipo de seguros manejan?',
        respuesta: 'Contamos con un portafolio diseñado para cubrir cada etapa de tu vida y negocio. Desde seguros personales de alta gama (Salud Premium, Vida, Hogar) hasta soluciones industriales complejas (Responsabilidad Civil, Pyme y cumplimiento). Además, somos especialistas líderes en pólizas de disposiciones legales y sector mineroambiental.'
    },
    {
        pregunta: '¿Con qué aseguradoras trabajan?',
        respuesta: 'Mantenemos convenios directos con las compañías más sólidas y prestigiosas de Colombia, incluyendo a Allianz, Berkley, Previsora, Seguros Bolívar, Seguros del Estado, Seguros Mundial, Global Seguros, Solidaria, Sura y HDI. Esta amplia red de aliados nos permite negociar condiciones exclusivas, primas competitivas y una agilidad superior en la expedición de tus pólizas.'
    },
    {
        pregunta: '¿Cómo es el proceso de reclamación?',
        respuesta: 'Aunque el trámite de reclamación se realiza formalmente de manera directa ante la compañía aseguradora, en Canal Asesores te brindamos un respaldo integral. Como tus intermediarios de confianza, te asesoramos en la gestión de tu caso y te orientamos paso a paso para que el proceso sea lo más fluido, transparente y efectivo posible.'
    }
];

const aliados = [
    { name: 'Allianz', logo: '/images/aliados/allianz.webp' },
    { name: 'Berkley', logo: '/images/aliados/berkley.webp' },
    { name: 'Previsora', logo: '/images/aliados/previsora.webp' },
    { name: 'Seguros Bolívar', logo: '/images/aliados/bolivar.webp' },
    { name: 'Seguros del Estado', logo: '/images/aliados/estado.webp' },
    { name: 'Seguros Mundial', logo: '/images/aliados/mundial.webp' },
    { name: 'Global Seguros', logo: '/images/aliados/global.webp' },
    { name: 'Solidaria', logo: '/images/aliados/solidaria.webp' },
    { name: 'Sura', logo: '/images/aliados/sura.webp' },
    { name: 'HDI', logo: '/images/aliados/hdi.webp' },
];

const faqOpen = ref(null);
const toggleFaq = (index) => {
    faqOpen.value = faqOpen.value === index ? null : index;
};

</script>

<template>

    <Head>
        <title>{{ settings.landing_meta_title || 'Canal Asesores | Seguros Confiables en Colombia' }}</title>
        <meta name="description"
            :content="settings.landing_meta_description || 'Seguros de vida, auto, hogar y salud con asesoría profesional en Colombia. Protección que puedes confiar.'" />
        <meta name="keywords" content="seguros colombia, seguros de vida, seguros auto, seguros hogar" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600;700&display=swap"
            rel="preload" as="style" />
        <link
            href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600;700&display=swap"
            rel="stylesheet" media="print" onload="this.media='all'" />
    </Head>

    <div class="min-h-screen bg-white text-slate-900 font-sans overflow-x-hidden relative">
        <!-- Global Background Texture (Grain/Noise) -->
        <div class="fixed inset-0 pointer-events-none z-[1] opacity-[0.03]"
            style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIj48ZmlsdGVyIGlkPSJuIj48ZmVUdXJidWxlbmNlIHR5cGU9ImZyYWN0YWxOb2lzZSIgYmFzZUZyZXF1ZW5jeT0iMC42NSIgbnVtT2N0YXZlcz0iMyIgc3RpdGNoVGlsZXM9InN0aXRjaCIvPjwvZmlsdGVyPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbHRlcj0idXJsKCNuKSIvPjwvc3ZnPg==');">
        </div>

        <!-- Navbar elegante -->
        <nav :class="[scrolled ? 'bg-white border-b border-slate-100 shadow-sm' : 'bg-transparent']"
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center gap-3">
                        <span class="text-xl font-black tracking-tight text-slate-900 line-height-1">Canal<span
                                class="text-blue-700">
                                Asesores</span></span>
                    </div>

                    <div class="hidden md:flex items-center gap-12">
                        <a href="#inicio"
                            class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">Inicio</a>
                        <a href="#productos"
                            class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">Productos</a>
                        <a href="#nosotros"
                            class="text-sm font-medium text-slate-600 hover:text-slate-900 transition">Nosotros</a>
                    </div>

                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2"
                        aria-label="Abrir menú de navegación" :aria-expanded="mobileMenuOpen">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden bg-white border-b border-slate-100">
                <div class="px-6 py-4 space-y-4">
                    <a href="#inicio" class="block text-slate-600 hover:text-slate-900">Inicio</a>
                    <a href="#productos" class="block text-slate-600 hover:text-slate-900">Productos</a>
                    <a href="#nosotros" class="block text-slate-600 hover:text-slate-900">Nosotros</a>
                </div>
            </div>
        </nav>

        <!-- Hero Section - Composición Equilibrada -->
        <section id="inicio" class="relative pt-40 pb-32 px-6 lg:px-8 overflow-hidden">
            <!-- Background Enhancements -->
            <div class="absolute inset-0 z-0">
                <div
                    class="absolute top-0 right-0 w-1/2 h-full bg-blue-50/30 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/4">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-1/2 h-1/2 bg-slate-100/50 rounded-full blur-[120px] translate-y-1/4 -translate-x-1/4">
                </div>
            </div>

            <div class="max-w-7xl mx-auto relative z-10">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <!-- Columna de Texto -->
                    <div class="space-y-10 text-center lg:text-left">
                        <div class="space-y-6">
                            <h1 class="text-5xl lg:text-7xl font-black leading-[0.9] tracking-tighter"
                                style="font-family: 'Playfair Display', serif;">
                                ASEGURA TU HOGAR,<br><span class="text-blue-700">FAMILIA Y FUTURO.</span>
                            </h1>
                            <p class="text-lg text-slate-600 max-w-lg leading-relaxed font-medium mx-auto lg:mx-0">
                                Protección integral en riesgos mineros, corporativos y familiares. Ofrecemos el respaldo
                                técnico y legal que tu tranquilidad exige.
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-6 pt-4 justify-center lg:justify-start items-center">
                            <a :href="'https://wa.me/' + settings.landing_whatsapp_number" target="_blank"
                                class="inline-flex items-center justify-center px-12 py-5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all hover:scale-105 shadow-xl shadow-blue-200 group">
                                Cotizar Ahora
                                <svg class="w-6 h-6 ml-2 group-hover:translate-x-1 transition" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <a href="#productos"
                                class="text-slate-900 font-black text-sm uppercase tracking-widest border-b-2 border-slate-900 pb-1 hover:text-blue-600 hover:border-blue-600 transition-all">
                                Nuestros Servicios
                            </a>
                        </div>
                    </div>

                    <!-- Columna Visual (Composición Potente) -->
                    <div class="relative hidden lg:block">
                        <div
                            class="relative z-10 rounded-[3rem] overflow-hidden shadow-2xl shadow-blue-900/10 border-8 border-white">
                            <img src="/images/landing/hero_collage.webp" alt="Canal360 Protección Integral"
                                class="w-full object-cover" width="600" height="400" fetchpriority="high"
                                decoding="async">
                        </div>


                        <div
                            class="absolute -bottom-8 -left-8 bg-blue-600 p-6 rounded-2xl shadow-xl text-white z-20 hover:scale-105 transition-transform duration-500">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-bold uppercase tracking-tighter leading-none mb-1">Respaldo
                                        Real</div>
                                    <div class="text-[10px] opacity-80">Asesoría Constante</div>
                                </div>
                            </div>
                        </div>

                        <!-- Decorative Blobs Behind Image -->
                        <div
                            class="absolute -z-10 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-blue-600/5 rounded-full blur-[100px]">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Nueva Sección: Marquee de Aliados (Logos) -->
        <section class="py-12 bg-white/50 border-y border-slate-100 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 mb-8">
                <div class="flex items-center gap-4">
                    <div class="h-[1px] flex-grow bg-slate-200"></div>
                    <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.3em] whitespace-nowrap">
                        Nuestros
                        Aliados Estratégicos</h2>
                    <div class="h-[1px] flex-grow bg-slate-200"></div>
                </div>
            </div>

            <div class="relative flex overflow-x-hidden group">
                <div class="flex animate-marquee-continuous hover:pause-marquee flex-nowrap items-center min-w-max">
                    <!-- First set of logos -->
                    <div class="flex gap-16 lg:gap-28 items-center px-8 lg:px-14">
                        <div v-for="aliado in aliados" :key="aliado.name" class="flex-shrink-0 group/logo">
                            <img :src="aliado.logo" :alt="aliado.name" :title="aliado.name"
                                class="h-16 lg:h-24 w-auto object-contain transition-all duration-500 hover:scale-110"
                                loading="lazy" decoding="async" width="224" height="112"
                                @error="$event.target.src = 'https://via.placeholder.com/200x80?text=' + aliado.name">
                        </div>
                    </div>
                    <!-- Second set of logos (identical) -->
                    <div class="flex gap-16 lg:gap-28 items-center px-8 lg:px-14">
                        <div v-for="aliado in aliados" :key="aliado.name + '_clone'" class="flex-shrink-0 group/logo">
                            <img :src="aliado.logo" :alt="aliado.name" :title="aliado.name"
                                class="h-16 lg:h-24 w-auto object-contain transition-all duration-500 hover:scale-110"
                                loading="lazy" decoding="async" width="224" height="112"
                                @error="$event.target.src = 'https://via.placeholder.com/200x80?text=' + aliado.name">
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Valores fundamentales -->
        <section class="py-24 px-6 lg:px-8 relative overflow-hidden">
            <!-- Subtle Mesh Gradient Background -->
            <div class="absolute inset-0 bg-slate-50 z-0">
                <div class="absolute top-0 -left-1/4 w-1/2 h-full bg-blue-50/50 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-0 -right-1/4 w-1/2 h-full bg-slate-100/50 rounded-full blur-[120px]"></div>
            </div>

            <div class="max-w-6xl mx-auto relative z-10">
                <h2 class="sr-only">Nuestros Valores Fundamentales</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="valor in valores" :key="valor.titulo"
                        class="space-y-4 p-6 rounded-2xl transition-all hover:bg-white/50 hover:shadow-sm">
                        <div
                            class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                            <svg v-if="valor.iconName === 'eye'" class="w-6 h-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-if="valor.iconName === 'zap'" class="w-6 h-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <svg v-if="valor.iconName === 'award'" class="w-6 h-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                            <svg v-if="valor.iconName === 'smartphone'" class="w-6 h-6" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold">{{ valor.titulo }}</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">{{ valor.descripcion }}</p>
                    </div>
                </div>
            </div>
        </section>


        <!-- Productos destacados -->
        <section id="productos" class="py-32 px-6 lg:px-8 relative overflow-hidden">
            <!-- Background Mesh Gradient -->
            <div class="absolute inset-0 z-0">
                <div class="absolute top-1/4 -right-1/4 w-1/2 h-full bg-blue-50/40 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-1/4 -left-1/4 w-1/2 h-full bg-slate-100/40 rounded-full blur-[120px]"></div>
            </div>

            <div class="max-w-6xl mx-auto relative z-10">
                <div class="text-center mb-16 space-y-4">
                    <h2 class="text-5xl lg:text-6xl font-black tracking-tight"
                        style="font-family: 'Playfair Display', serif;">
                        Productos que protegen
                    </h2>
                    <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                        Cada uno diseñado con flexibilidad y transparencia para tus necesidades específicas.
                    </p>
                </div>

                <div class="relative group/carousel">
                    <!-- Controles de Navegación -->
                    <div class="absolute -left-4 lg:-left-12 top-1/2 -translate-y-1/2 z-10">
                        <button @click="prevProduct" aria-label="Ver producto anterior"
                            class="w-10 h-10 lg:w-12 lg:h-12 bg-white border border-slate-200 rounded-full flex items-center justify-center shadow-lg hover:bg-blue-600 hover:text-white transition-all hover:scale-110 active:scale-95">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                    </div>

                    <div class="absolute -right-4 lg:-right-12 top-1/2 -translate-y-1/2 z-10">
                        <button @click="nextProduct" aria-label="Ver producto siguiente"
                            class="w-10 h-10 lg:w-12 lg:h-12 bg-white border border-slate-200 rounded-full flex items-center justify-center shadow-lg hover:bg-blue-600 hover:text-white transition-all hover:scale-110 active:scale-95">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Vista del Carrusel -->
                    <div class="overflow-hidden" @mouseenter="stopAutoplay" @mouseleave="startAutoplay">
                        <div class="flex transition-transform duration-700 ease-in-out"
                            :style="{ transform: `translateX(-${currentProductIndex * (100 / visibleItems)}%)` }">
                            <div v-for="producto in productos" :key="producto.id" class="px-3 flex-shrink-0"
                                :style="{ width: `${100 / visibleItems}%` }">
                                <div
                                    class="h-full group border border-white rounded-2xl p-8 hover:border-blue-300 hover:shadow-2xl transition-all duration-500 bg-white/70 backdrop-blur-xl flex flex-col shadow-xl shadow-slate-200/50">
                                    <div class="flex items-start justify-between mb-8">
                                        <div
                                            class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-sm">
                                            <svg v-if="producto.iconName === 'heart'" class="w-8 h-8" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            <svg v-if="producto.iconName === 'car'" class="w-8 h-8" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                            </svg>
                                            <svg v-if="producto.iconName === 'home'" class="w-8 h-8" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                            <svg v-if="producto.iconName === 'activity'" class="w-8 h-8" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 9l3 3-3 3" />
                                            </svg>
                                            <svg v-if="producto.iconName === 'flame'" class="w-8 h-8" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.99 7.99 0 0120 13a7.99 7.99 0 01-2.343 5.657z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.879 16.121A3 3 0 1012.015 11L11 14l2.828 2.828" />
                                            </svg>
                                            <svg v-if="producto.iconName === 'truck'" class="w-8 h-8" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                            </svg>
                                            <svg v-if="producto.iconName === 'file-text'" class="w-8 h-8" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <svg v-if="producto.iconName === 'shield'" class="w-8 h-8" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            <svg v-if="producto.iconName === 'bandage'" class="w-8 h-8" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <svg v-if="producto.iconName === 'graduation-cap'" class="w-8 h-8"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 14l9-5-9-5-9 5 9 5z" />
                                                <path
                                                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                            </svg>
                                            <svg v-if="producto.iconName === 'flower'" class="w-8 h-8" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                        </div>
                                        <div
                                            class="text-xs font-black text-blue-700 uppercase tracking-widest bg-blue-50 px-4 py-1.5 rounded-full shadow-sm">
                                            {{ producto.destaque }}
                                        </div>
                                    </div>



                                    <h3 class="text-xl font-bold mb-2">{{ producto.nombre }}</h3>
                                    <p class="text-slate-600 mb-6 text-sm leading-relaxed">{{ producto.descripcion }}
                                    </p>

                                    <div class="space-y-2 mb-8">
                                        <div v-for="beneficio in producto.beneficios" :key="beneficio"
                                            class="flex items-start gap-3">
                                            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs text-slate-700">{{ beneficio }}</span>
                                        </div>
                                    </div>

                                    <a :href="'https://wa.me/' + settings.landing_whatsapp_number" target="_blank"
                                        class="mt-auto inline-block px-6 py-3 bg-slate-900 text-white text-xs font-bold rounded-xl hover:bg-blue-600 transition w-full text-center uppercase tracking-widest">
                                        Asesoría
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Indicadores (Dots) -->
                    <div class="flex justify-center gap-2 mt-12">
                        <button v-for="i in (maxIndex + 1)" :key="i" @click="currentProductIndex = i - 1"
                            :aria-label="'Ir al grupo de productos ' + i"
                            class="relative w-3 h-3 rounded-full transition-transform duration-300 group"
                            :class="[currentProductIndex === i - 1 ? 'bg-blue-600 scale-x-[2.5]' : 'bg-slate-300 hover:bg-slate-400']">
                            <!-- Click area expansion -->
                            <span class="absolute inset-[-12px] rounded-full"></span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Proceso sencillo -->
        <section class="py-32 px-6 lg:px-8 bg-slate-900 text-white relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute inset-0 z-0">
                <div
                    class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-blue-600/10 rounded-full blur-[120px]">
                </div>
                <div class="absolute inset-0 opacity-[0.05]"
                    style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 80px 80px;">
                </div>
            </div>

            <div class="max-w-6xl mx-auto relative z-10">
                <div class="text-center mb-16 space-y-4">
                    <h2 class="text-5xl lg:text-6xl font-black tracking-tight"
                        style="font-family: 'Playfair Display', serif;">
                        Proceso sencillo
                    </h2>
                    <p class="text-xl text-slate-300 max-w-2xl mx-auto">
                        Desde cotización hasta cobertura en pasos simples y claros.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div v-for="(paso, idx) in procesos" :key="paso.numero" class="relative group">
                        <div class="flex flex-col items-center text-center space-y-6">
                            <div
                                class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center font-black text-xl shadow-lg shadow-blue-900/40 group-hover:scale-110 transition-transform">
                                <span v-if="!paso.iconName">{{ paso.numero }}</span>
                                <svg v-if="paso.iconName === 'clipboard'" class="w-8 h-8" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <svg v-if="paso.iconName === 'user'" class="w-8 h-8" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <svg v-if="paso.iconName === 'check-circle'" class="w-8 h-8" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg v-if="paso.iconName === 'users'" class="w-8 h-8" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold">{{ paso.titulo }}</h3>
                            <p class="text-slate-300 text-sm">{{ paso.descripcion }}</p>
                        </div>


                        <!-- Línea conectora -->
                        <div v-if="idx < procesos.length - 1"
                            class="hidden lg:block absolute top-8 left-[calc(50%+3.5rem)] w-[calc(100%-7rem)] h-0.5 bg-slate-700/50">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonios Premium -->
        <section class="py-32 px-6 lg:px-8 relative overflow-hidden">
            <!-- Background Accent -->
            <div class="absolute inset-0 bg-white z-0">
                <div class="absolute top-0 right-0 w-1/3 h-2/3 bg-blue-50/30 rounded-full blur-[120px]"></div>
            </div>

            <div class="max-w-6xl mx-auto relative z-10">
                <div class="text-center mb-20 space-y-4">
                    <h2 class="text-5xl lg:text-6xl font-black tracking-tight"
                        style="font-family: 'Playfair Display', serif;">
                        Clientes satisfechos
                    </h2>
                    <p class="text-xl text-slate-600">Confianza construida a través de resultados reales.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div v-for="testimonio in testimonios" :key="testimonio.nombre"
                        class="bg-white/80 backdrop-blur-md p-10 rounded-[2rem] border border-white flex flex-col justify-between hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 group shadow-xl shadow-slate-100">
                        <div class="space-y-6">
                            <div class="flex gap-1 text-blue-600">
                                <svg v-for="i in 5" :key="i" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <p class="text-lg font-medium text-slate-700 leading-relaxed italic">"{{ testimonio.texto
                                }}"</p>
                        </div>
                        <div class="mt-8 pt-8 border-t border-slate-200">
                            <div class="font-black text-slate-900">{{ testimonio.nombre }}</div>
                            <div class="text-xs font-bold text-slate-600 uppercase tracking-widest">{{ testimonio.rol }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Nuestro Equipo Directivo -->
        <section id="nosotros" class="py-32 px-6 lg:px-8 bg-slate-50">
            <div class="max-w-6xl mx-auto">
                <div class="grid lg:grid-cols-12 gap-16 items-start">
                    <div class="lg:col-span-5 space-y-8">
                        <h2 class="text-5xl lg:text-6xl font-black leading-[0.9] tracking-tighter"
                            style="font-family: 'Playfair Display', serif;">
                            Experiencia que<br><span class="text-blue-700">respalda.</span>
                        </h2>
                        <p class="text-xl text-slate-600 leading-relaxed font-medium">
                            Nuestra trayectoria nace en el corazón del sector minero-industrial colombiano,
                            evolucionando hacia un ecosistema asegurador integral para todas las industrias.
                        </p>
                    </div>

                    <div class="lg:col-span-7 grid gap-6">
                        <div v-for="miembro in equipo" :key="miembro.nombre"
                            class="bg-white/80 backdrop-blur-md p-8 rounded-2xl border border-white shadow-xl hover:shadow-2xl transition-all group overflow-hidden relative">
                            <!-- Background Accent -->
                            <div
                                class="absolute -right-12 -top-12 w-32 h-32 bg-blue-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity blur-2xl">
                            </div>

                            <div class="flex flex-col md:flex-row gap-6 relative z-10">
                                <div
                                    class="w-24 h-24 rounded-2xl bg-slate-900 flex-shrink-0 overflow-hidden shadow-lg group-hover:ring-4 group-hover:ring-blue-100 transition-all">
                                    <img :src="miembro.image" :alt="miembro.nombre" class="w-full h-full object-cover"
                                        width="96" height="96" loading="lazy" decoding="async">
                                </div>
                                <div class="space-y-4">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <h3 class="text-2xl font-black tracking-tight"
                                            style="font-family: 'Playfair Display', serif;">
                                            {{ miembro.nombre }}
                                        </h3>
                                        <div
                                            class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-full">
                                            {{ miembro.especialidad }}
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-blue-700 uppercase tracking-widest leading-none">
                                        {{
                                            miembro.rol }}</div>
                                    <p class="text-sm text-slate-500 leading-relaxed">{{ miembro.bio }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Preguntas Frecuentes (FAQ) -->
        <section class="py-32 px-6 lg:px-8 relative overflow-hidden bg-white">
            <!-- Background Decoration -->
            <div class="absolute inset-0 z-0">
                <div class="absolute top-1/2 left-0 w-1/3 h-full bg-blue-50/20 rounded-full blur-[120px]"></div>
                <div class="absolute bottom-0 right-0 w-1/4 h-1/2 bg-slate-50 rounded-full blur-[80px]"></div>
            </div>

            <div class="max-w-4xl mx-auto relative z-10">
                <div class="text-center mb-16 space-y-4">
                    <h2 class="text-5xl lg:text-6xl font-black tracking-tight"
                        style="font-family: 'Playfair Display', serif;">
                        Preguntas frecuentes
                    </h2>
                    <p class="text-xl text-slate-600">Resolvemos tus dudas con claridad meridiana.</p>
                </div>

                <div class="space-y-4">
                    <div v-for="(faq, idx) in faqs" :key="idx"
                        class="border border-slate-100 rounded-2xl overflow-hidden transition-all"
                        :class="[faqOpen === idx ? 'bg-slate-50 border-blue-100 ring-2 ring-blue-50' : 'bg-white']">
                        <button @click="toggleFaq(idx)" :aria-expanded="faqOpen === idx"
                            class="w-full px-8 py-6 flex items-center justify-between text-left group">
                            <span
                                class="text-lg font-bold text-slate-800 group-hover:text-blue-700 transition-colors">{{
                                    faq.pregunta }}</span>
                            <span class="text-2xl font-black transition-transform duration-300 transform"
                                :class="[faqOpen === idx ? 'rotate-45 text-blue-700' : 'text-slate-300']">
                                +
                            </span>
                        </button>
                        <transition enter-active-class="transition duration-300 ease-out"
                            enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-200 ease-in"
                            leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                            <div v-if="faqOpen === idx" class="px-8 pb-6 text-slate-600 leading-relaxed font-medium">
                                {{ faq.respuesta }}
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </section>


        <!-- CTA Final -->
        <section class="py-40 px-6 lg:px-8 bg-slate-900 relative overflow-hidden text-center">
            <!-- Dynamic Gradient Background -->
            <div class="absolute inset-0 z-0">
                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-blue-900/60 to-slate-900"></div>
                <!-- Animated-like glows -->
                <div class="absolute -top-1/4 -right-1/4 w-1/2 h-full bg-blue-600/20 rounded-full blur-[140px]"></div>
                <div class="absolute -bottom-1/4 -left-1/4 w-1/2 h-full bg-blue-400/10 rounded-full blur-[140px]"></div>
            </div>

            <div class="max-w-3xl mx-auto relative z-10 space-y-10">
                <div class="space-y-4">
                    <h2 class="text-5xl lg:text-7xl font-black leading-tight text-white"
                        style="font-family: 'Playfair Display', serif;">
                        ¿Listo para proteger lo que importa?
                    </h2>
                    <p class="text-xl text-slate-300 max-w-xl mx-auto leading-relaxed">
                        Nuestros expertos están disponibles para resolver todas tus preguntas y brindarte el respaldo
                        que mereces.
                    </p>
                </div>
                <div class="pt-4">
                    <a :href="'https://wa.me/' + settings.landing_whatsapp_number" target="_blank"
                        class="inline-flex items-center justify-center px-12 py-5 bg-white text-blue-600 font-black rounded-xl hover:bg-slate-100 transition-all hover:scale-105 shadow-2xl shadow-white/10 group">
                        Contáctanos por WhatsApp
                        <svg class="w-6 h-6 ml-3 group-hover:translate-x-1 transition" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer minimalista -->
        <footer class="bg-slate-900 text-white py-16 px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="font-bold">Canal Asesores</span>
                        </div>
                        <p class="text-slate-400 text-sm leading-relaxed"> {{ settings.landing_footer_description ||
                            'Seguros confiables con asesoría profesional en Colombia.' }}</p>
                    </div>

                    <div>
                        <h3 class="font-bold mb-4">Links</h3>
                        <ul class="space-y-2 text-slate-400 text-sm">
                            <li><a href="#inicio" class="hover:text-white transition">Inicio</a></li>
                            <li><a href="#productos" class="hover:text-white transition">Productos</a></li>
                            <li><a href="#nosotros" class="hover:text-white transition">Nosotros</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-bold mb-6 text-white">Contacto</h3>
                        <div class="space-y-4 text-sm text-slate-400">
                            <!-- Email -->
                            <div class="flex items-center gap-3 hover:text-white transition-colors duration-300 group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L17 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span>{{ settings.contact_email }}</span>
                            </div>

                            <!-- Phone 1 -->
                            <div class="flex items-center gap-3 hover:text-white transition-colors duration-300 group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <span>{{ settings.contact_person_1_phone }}</span>
                            </div>

                            <!-- Phone 2 -->
                            <div v-if="settings.contact_person_2_phone"
                                class="flex items-center gap-3 hover:text-white transition-colors duration-300 group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <span>{{ settings.contact_person_2_phone }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-6 text-sm text-slate-400">
                    <div>&copy; {{ new Date().getFullYear() }} Canal Asesores Limitada. Todos los derechos reservados.
                    </div>
                    <div class="flex gap-8 font-bold uppercase tracking-widest text-[10px]">
                        <Link :href="route('privacy.policy')" class="hover:text-white transition">Habeas Data</Link>
                        <!-- Enlace dinámico según estado de sesión -->
                        <Link v-if="$page.props.auth?.user" :href="route('dashboard')"
                            class="hover:text-white transition">Ir al Portal</Link>
                        <Link v-else :href="route('login')" class="hover:text-white transition">Acceso Portal</Link>
                    </div>
                </div>

            </div>
        </footer>

        <!-- WhatsApp Floating Button -->
        <a :href="'https://wa.me/' + settings.landing_whatsapp_number?.replace(/\s+/g, '')" target="_blank"
            aria-label="Contactar por WhatsApp"
            class="fixed bottom-10 right-10 z-50 group flex flex-col items-end gap-3">

            <div class="relative">
                <!-- Subtle Glow Effect -->
                <span class="absolute inset-0 rounded-full bg-[#25D366] animate-pulse opacity-10"></span>

                <!-- Main Button -->
                <div
                    class="relative w-16 h-16 bg-[#25D366] text-white rounded-full shadow-lg shadow-[#25D366]/10 flex items-center justify-center hover:bg-[#20ba5c] transition-all hover:scale-110 active:scale-95 group-hover:shadow-[#25D366]/20">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.889 1.034 3.842 1.531 5.767 1.531h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                    </svg>

                    <!-- Online Status Badge -->
                    <span
                        class="absolute bottom-1 right-1 w-3.5 h-3.5 bg-emerald-400 border-2 border-white rounded-full"></span>
                </div>
            </div>
        </a>
    </div>
</template>

<style scoped>
@keyframes marquee-continuous {
    0% {
        transform: translate3d(0, 0, 0);
    }

    100% {
        transform: translate3d(-50%, 0, 0);
    }
}

.animate-marquee-continuous {
    animation: marquee-continuous 20s linear infinite;
}

.pause-marquee {
    animation-play-state: paused;
}
</style>

<style scoped>
/* Animaciones suaves */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

section {
    animation: fadeInUp 0.8s ease-out;
}

/* Scroll suave */
html {
    scroll-behavior: smooth;
}

/* Tipografía elegante */
h1,
h2 {
    letter-spacing: -0.02em;
}
</style>