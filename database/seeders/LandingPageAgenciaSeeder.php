<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class LandingPageAgenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'landing_meta_title' => ['label' => 'Título SEO (Meta Title)', 'value' => 'Canal Asesores | Agencia de Seguros y Protección Integral', 'type' => 'text'],
            'landing_meta_description' => ['label' => 'Descripción SEO (Meta Description)', 'value' => 'Expertos en seguros de autos, vida, hogar y salud en Colombia. Protegemos lo que más quieres con asesoría personalizada y respaldo garantizado.', 'type' => 'textarea'],
            'landing_hero_title' => ['label' => 'Título del Hero', 'value' => 'Seguridad Integral para lo que más Valoras', 'type' => 'text'],
            'landing_hero_description' => ['label' => 'Descripción del Hero', 'value' => 'Protegemos tu familia, tu empresa y tu futuro con el respaldo de las mejores aseguradoras de Colombia. Asesoría experta y acompañamiento real en cada paso.', 'type' => 'textarea'],
            'landing_cta_text' => ['label' => 'Texto del Botón Principal', 'value' => 'Cotizar Ahora', 'type' => 'text'],
            'landing_allies_text' => ['label' => 'Título de Aliados', 'value' => 'Aliados Estratégicos que respaldan tu seguridad', 'type' => 'text'],
            'landing_trust_badge' => ['label' => 'Etiqueta de Confianza', 'value' => 'Expertos en riesgos a tu servicio', 'type' => 'text'],
            'landing_tech_title' => ['label' => 'Título Sección Tecnología', 'value' => 'Asociamos Experiencia Humana con Eficiencia Digital', 'type' => 'text'],
            'landing_tech_description' => ['label' => 'Descripción Sección Tecnología', 'value' => 'En Canal Asesores combinamos décadas de experiencia en seguros con herramientas modernas para que gestionar tus pólizas sea tan sencillo como un clic.', 'type' => 'textarea'],
            'landing_tech_features' => ['label' => 'Características Tecnológicas (separadas por coma)', 'value' => 'Asesoría personalizada 24/7,Respaldo en reclamaciones,Cotizaciones multiaseguradora,Claridad total en coberturas', 'type' => 'text'],
            'landing_footer_description' => ['label' => 'Descripción del Footer', 'value' => 'Expertos en asesoría de riesgos y protección de patrimonio. Seguridad real para tiempos modernos.', 'type' => 'textarea'],
            
            // Descripciones de las 4 categorías de servicios
            'landing_service_cat_1_title' => ['label' => 'Título Servicio 1', 'value' => 'Seguros para Personas', 'type' => 'text'],
            'landing_service_cat_1_description' => ['label' => 'Descripción Servicio 1', 'value' => 'Protección integral para tu familia, salud y patrimonio con el respaldo de las mejores aseguradoras.', 'type' => 'textarea'],
            'landing_service_cat_2_title' => ['label' => 'Título Servicio 2', 'value' => 'Seguros para el Hogar', 'type' => 'text'],
            'landing_service_cat_2_description' => ['label' => 'Descripción Servicio 2', 'value' => 'Asegura lo que más valoras: tu hogar y tus bienes, con coberturas contra todo riesgo.', 'type' => 'textarea'],
            'landing_service_cat_3_title' => ['label' => 'Título Servicio 3', 'value' => 'Seguros de Movilidad', 'type' => 'text'],
            'landing_service_cat_3_description' => ['label' => 'Descripción Servicio 3', 'value' => 'Soluciones de movilidad inteligente para tu vehículo con asistencia inmediata en vía 24/7.', 'type' => 'textarea'],
            'landing_service_cat_4_title' => ['label' => 'Título Servicio 4', 'value' => 'Seguros Empresariales', 'type' => 'text'],
            'landing_service_cat_4_description' => ['label' => 'Descripción Servicio 4', 'value' => 'Brindamos solidez y confianza para proteger la continuidad de tu empresa y tus empleados.', 'type' => 'textarea'],
        ];

        foreach ($settings as $key => $data) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'],
                    'label' => $data['label'],
                    'type' => $data['type'],
                    'group' => 'landing_page'
                ]
            );
        }
    }
}
