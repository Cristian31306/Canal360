<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Hero Section
            [
                'key' => 'landing_hero_title',
                'value' => 'Seguridad Integral para tu Tranquilidad.',
                'label' => 'Título del Hero',
                'type' => 'text',
                'group' => 'landing_hero',
            ],
            [
                'key' => 'landing_hero_description',
                'value' => 'Gestionamos tus seguros con la sofisticación y el respaldo que mereces. Canal Asesores: Aliados en tu protección personal y empresarial.',
                'label' => 'Descripción del Hero',
                'type' => 'textarea',
                'group' => 'landing_hero',
            ],
            [
                'key' => 'landing_hero_image',
                'value' => '/hero-landing.png',
                'label' => 'Imagen del Hero (Subir archivo)',
                'type' => 'image',
                'group' => 'landing_hero',
            ],
            [
                'key' => 'landing_cta_text',
                'value' => 'Proteger mis Activos',
                'label' => 'Texto del Botón Principal',
                'type' => 'text',
                'group' => 'landing_hero',
            ],
            [
                'key' => 'landing_whatsapp_number',
                'value' => '573176462367',
                'label' => 'Número de WhatsApp (Sin +)',
                'type' => 'text',
                'group' => 'landing_contact',
            ],
            // Technology / Value Proposition Section
            [
                'key' => 'landing_tech_title',
                'value' => 'Gestión de Pólizas, Simplificada.',
                'label' => 'Título Sección Tecnología',
                'type' => 'text',
                'group' => 'landing_tech',
            ],
            [
                'key' => 'landing_tech_description',
                'value' => 'Olvídate de los papeles y las fechas olvidadas. Canal Asesores es la plataforma centralizada donde toda tu seguridad está a un clic de distancia.',
                'label' => 'Descripción Sección Tecnología',
                'type' => 'textarea',
                'group' => 'landing_tech',
            ],
            [
                'key' => 'landing_tech_features',
                'value' => 'Notificaciones de renovación,Soporte especializado,Dashboard de coberturas,Historial de siniestros',
                'label' => 'Características (Separadas por comas)',
                'type' => 'textarea',
                'group' => 'landing_tech',
            ],
            // Contact Section
            [
                'key' => 'contact_email',
                'value' => 'canalasesores1@gmail.com',
                'label' => 'Correo de Contacto',
                'type' => 'email',
                'group' => 'landing_contact',
            ],
            [
                'key' => 'contact_person_1_name',
                'value' => 'Vilma Delgado',
                'label' => 'Nombre del Asesor 1',
                'type' => 'text',
                'group' => 'landing_contact',
            ],
            [
                'key' => 'contact_person_1_phone',
                'value' => '3176462367',
                'label' => 'Teléfono del Asesor 1',
                'type' => 'text',
                'group' => 'landing_contact',
            ],
            [
                'key' => 'contact_person_2_name',
                'value' => 'Juan David Canal',
                'label' => 'Nombre del Asesor 2',
                'type' => 'text',
                'group' => 'landing_contact',
            ],
            [
                'key' => 'contact_person_2_phone',
                'value' => '3134848500',
                'label' => 'Teléfono del Asesor 2',
                'type' => 'text',
                'group' => 'landing_contact',
            ],
            // Service Categories Titles
            [
                'key' => 'landing_service_cat_1_title',
                'value' => 'Vida & Salud',
                'label' => 'Título Categoría 1',
                'type' => 'text',
                'group' => 'landing_services',
            ],
            [
                'key' => 'landing_service_cat_1_items',
                'value' => 'Salud,Vida,Accidentes Personales,Exequiales,Vida Deudor',
                'label' => 'Items Categoría 1 (Sep. por comas)',
                'type' => 'textarea',
                'group' => 'landing_services',
            ],
            [
                'key' => 'landing_service_cat_2_title',
                'value' => 'Hogar & Patrimonio',
                'label' => 'Título Categoría 2',
                'type' => 'text',
                'group' => 'landing_services',
            ],
            [
                'key' => 'landing_service_cat_2_items',
                'value' => 'Hogar,Arrendamiento,Copropiedades,Todo Riesgo',
                'label' => 'Items Categoría 2 (Sep. por comas)',
                'type' => 'textarea',
                'group' => 'landing_services',
            ],
            [
                'key' => 'landing_service_cat_3_title',
                'value' => 'Movilidad & Equipo',
                'label' => 'Título Categoría 3',
                'type' => 'text',
                'group' => 'landing_services',
            ],
            [
                'key' => 'landing_service_cat_3_items',
                'value' => 'Autos,Autos Pesados,Transporte,Maquinaria',
                'label' => 'Items Categoría 3 (Sep. por comas)',
                'type' => 'textarea',
                'group' => 'landing_services',
            ],
            [
                'key' => 'landing_service_cat_4_title',
                'value' => 'Corporativo & Técnico',
                'label' => 'Título Categoría 4',
                'type' => 'text',
                'group' => 'landing_services',
            ],
            [
                'key' => 'landing_service_cat_4_items',
                'value' => 'PYME,Cumplimiento,Manejo Global Comercial,RCE,Minera,Hidrocarburos,Emisiones',
                'label' => 'Items Categoría 4 (Sep. por comas)',
                'type' => 'textarea',
                'group' => 'landing_services',
            ],
        ];

        // Limpiar llaves antiguas que ya no se usan
        \App\Models\Setting::whereIn('key', ['contact_phone_vilma', 'contact_phone_juan'])->delete();

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
