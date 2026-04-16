<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const scrolled = ref(false);

onMounted(() => {
    window.addEventListener('scroll', () => {
        scrolled.value = window.scrollY > 20;
    });
});
</script>

<template>

    <Head>
        <title>Política de Tratamiento de Datos | Canal Asesores</title>
        <link
            href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Inter:wght@400;500;600;700&display=swap"
            rel="stylesheet" />
    </Head>

    <div class="min-h-screen bg-white text-slate-900 font-sans selection:bg-blue-100 selection:text-blue-900">
        <!-- navbar -->
        <nav :class="[scrolled ? 'bg-white border-b border-slate-100 shadow-sm' : 'bg-transparent']"
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <Link href="/" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-900 rounded-lg flex items-center justify-center">
                            <span class="text-white font-black text-lg">C</span>
                        </div>
                        <span class="text-xl font-black tracking-tight">Canal<span
                                class="text-blue-600">Asesores</span></span>
                    </Link>
                    <Link href="/"
                        class="text-xs font-bold text-slate-400 hover:text-blue-600 transition uppercase tracking-widest">
                        Volver al Inicio
                    </Link>
                </div>
            </div>
        </nav>

        <main class="pt-40 pb-32 px-6 lg:px-8">
            <article class="max-w-4xl mx-auto space-y-12">
                <header class="border-b border-slate-100 pb-12">
                    <h1 class="text-2xl font-black uppercase tracking-[0.3em] text-blue-600 mb-4">Documento Legal</h1>
                    <h2 class="text-4xl lg:text-6xl font-black leading-tight tracking-tight"
                        style="font-family: 'Playfair Display', serif;">
                        POLÍTICA DE TRATAMIENTO DE DATOS PERSONALES
                    </h2>
                </header>

                <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed font-medium space-y-8">
                    <p class="whitespace-pre-line font-bold text-slate-900">
                        Canal Asesores Limitada Reconoce que la información personal es valiosa y adoptamos todas las
                        medidas razonablemente necesarias conforme a las exigencias legales, con el fin de proteger
                        dicha información y evitar su pérdida, adulteración, uso o acceso no autorizados mientras se
                        encuentra en nuestro cuidado. Sin perjuicio de ello, el visitante debe ser consciente de que las
                        medias de seguridad en Internet, no es infalible.
                    </p>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">1. GENERALIDADES</h3>
                        <p>El presente documento establece las Políticas de Tratamiento de Datos Personales de la
                            sociedad Canal Asesores Limitada, (en adelante, la Sociedad), en cumplimiento de las
                            disposiciones de la Ley 1581 de 2012 y el Decreto 1377 de 2013, y en él se describen los
                            mecanismos por medio de los cuales Canal Asesores Limitada garantiza un manejo adecuado de
                            los datos personales recolectados en sus bases de datos, con el fin de permitir a los
                            titulares el ejercicio del derecho de Habeas Data.</p>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">2. RESPONSABLE</h3>
                        <p>Canal Asesores Limitada es una persona jurídica de derecho privado, domiciliada en Cúcuta,
                            con Nit. 900.798.866- 1, cuyos datos de contacto son los siguientes:</p>
                        <p class="p-6 bg-slate-50 rounded-xl border-l-4 border-blue-600">
                            <strong>Dirección:</strong> Via Antigua Bocono, Urb El Viñedo CS 22, Villa Del Rosario<br>
                            <strong>Teléfono:</strong> 3176462367<br>
                            <strong>Correo electrónico:</strong> canalasesores1@gmail.com
                        </p>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">DEFINICIONES</h3>
                        <p class="whitespace-pre-line">
                            <strong>Autorización:</strong> Consentimiento previo, expreso e informado del Titular para
                            llevar a cabo el Tratamiento de datos personales;<br>
                            <strong>Base de Datos:</strong> Conjunto organizado de datos personales que sea objeto de
                            Tratamiento;<br>
                            <strong>Dato personal:</strong> Cualquier información vinculada o que pueda asociarse a una
                            o varias personas naturales determinadas o determinables;<br>
                            <strong>Encargado:</strong> Persona natural o jurídica, pública o privada, que por sí misma
                            o en asocio con otros, realice el Tratamiento de datos personales por cuenta del responsable
                            del Tratamiento;<br>
                            <strong>Responsable:</strong> Persona natural o jurídica, pública o privada, que por sí
                            misma o en asocio con otros, decida sobre la base de datos y/o el Tratamiento de los
                            datos;<br>
                            <strong>Titular:</strong> Persona natural cuyos datos personales sean objeto de
                            Tratamiento;<br>
                            <strong>Tratamiento:</strong> Cualquier operación o conjunto de operaciones sobre datos
                            personales, tales como la recolección, almacenamiento, uso, circulación o supresión.
                        </p>
                        <p class="whitespace-pre-line">
                            <strong>Principio de legalidad:</strong> El Tratamiento de datos personales es una actividad
                            reglada que debe sujetarse a lo establecido en la ley y en las demás disposiciones que la
                            desarrollen;<br>
                            <strong>Principio de finalidad:</strong> El Tratamiento debe obedecer a una finalidad
                            legítima de acuerdo con la Constitución y la Ley, la cual debe ser informada al Titular;<br>
                            <strong>Principio de libertad:</strong> El Tratamiento sólo puede ejercerse con el
                            consentimiento, previo, expreso e informado del Titular. Los datos personales no podrán ser
                            obtenidos o divulgados sin previa autorización, o en ausencia de mandato legal o judicial
                            que releve el consentimiento;<br>
                            <strong>Principio de veracidad o calidad:</strong> La información sujeta a Tratamiento debe
                            ser veraz, completa, exacta, actualizada, comprobable y comprensible. Se prohíbe el
                            Tratamiento de datos parciales, incompletos, fraccionados o que induzcan a error;<br>
                            <strong>Principio de transparencia:</strong> En el Tratamiento debe garantizarse el derecho
                            del Titular a obtener del responsable del Tratamiento o del Encargado del Tratamiento, en
                            cualquier momento y sin restricciones, información acerca de la existencia de datos que le
                            conciernan;<br>
                            <strong>Principio de acceso y circulación restringida:</strong> El Tratamiento se sujeta a
                            los límites que se derivan de la naturaleza de los datos personales, de las disposiciones de
                            la ley y la Constitución. En este sentido, el Tratamiento sólo podrá hacerse por personas
                            autorizadas por el Titular y/o por las personas previstas en la ley.<br>
                            – Los datos personales, salvo la información pública, no podrán estar disponibles en
                            Internet u otros medios de divulgación o comunicación masiva, salvo que el acceso sea
                            técnicamente controlable para brindar un conocimiento restringido sólo a los Titulares o
                            terceros autorizados conforme a la ley;<br>
                            <strong>Principio de seguridad:</strong> La información sujeta a Tratamiento por el
                            Responsable del Tratamiento o Encargado del Tratamiento se deberá manejar con las medidas
                            técnicas, humanas y administrativas que sean necesarias para otorgar seguridad a los
                            registros evitando su adulteración, pérdida, consulta, uso o acceso no autorizado o
                            fraudulento;<br>
                            <strong>Principio de confidencialidad:</strong> Todas las personas que intervengan en el
                            Tratamiento de datos personales que no tengan la naturaleza de públicos están obligadas a
                            garantizar la reserva de la información, inclusive después de finalizada su relación con
                            alguna de las labores que comprende el Tratamiento, pudiendo sólo realizar suministro o
                            comunicación de datos personales cuando ello corresponda al desarrollo de las actividades
                            autorizadas en la ley y en los términos de la misma.
                        </p>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">4. INFORMACIÓN
                            RECOLECTADA</h3>
                        <p>Para poder solicitar que nuestro equipo lo contacte, requerimos que nos proporcione cierta
                            información que incluye algunos datos personales como: nombre y apellido, correo
                            electrónico, numero de celular y un comentario sobre el motivo de su consulta.
                            Adicionalmente puede facilitarnos sus datos mediante opiniones, comentarios y sugerencias
                            participando voluntariamente en encuestas.</p>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">5. SEGURIDAD DE LOS
                            DATOS PERSONALES</h3>
                        <p>Canal Asesores Limitada incorpora varios procedimientos, sistemas y estándares físicos,
                            electrónicos y administrativos para salvaguardar la confidencialidad de sus datos personales
                            y proteger la información personal de pérdida, alteraciones o destrucción. El acceso a sus
                            datos personales es restringido. Sólo a los empleados que necesitan acceder a sus datos
                            personales para realizar un trabajo específico se les permite el acceso a los mismos.
                            Adicionalmente, todos nuestros empleados entran conminados a respetar políticas de
                            confidencialidad y seguridad de la información. Por último, los servidores donde Canal
                            Asesores Limitada almacena sus datos personales se mantienen en un ambiente seguro y cumplen
                            con los estándares de seguridad</p>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">6. TRATAMIENTO</h3>
                        <p>La información que consta en las bases de datos de la empresa es sometida a distintas formas
                            de tratamiento, como recolección, intercambio, actualización, procesamiento, reproducción,
                            compilación, almacenamiento, uso, sistematización y organización, todos ellos de forma
                            parcial o total en cumplimiento de las finalidades aquí establecidas. En cumplimiento de
                            deberes legales, la sociedad podrá suministrar la información personal a entidades
                            judiciales o administrativas.</p>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">7. MENORES DE EDAD</h3>
                        <p>Canal Asesores Limitada entiende la importancia de proteger a los menores de edad en especial
                            en ambientes virtuales como Internet. Nuestro sitio no está dirigido a menores de edad,
                            quienes no podrán registrarse y crear cuentas electrónicas, y sobre quienes no se podrá
                            recoger o recibir información en ningún caso, garantizando así el cumplimiento de las
                            exigencias legales.</p>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">8. FINALIDAD</h3>
                        <p>La información recolectada por Canal Asesores Limitada tiene como propósito:</p>
                        <ul class="list-decimal pl-6 space-y-4">
                            <li>Permitir el adecuado desarrollo de su objeto como empresa dedicada al a la
                                intermediación de seguros, efectuando todas las gestiones administrativas, financieras y
                                de mercadeo relacionadas directamente con el giro ordinario de negocio.</li>
                            <li>Mantener informado a sus clientes, a través de cualquier medio, acerca de productos o
                                servicios que Canal Asesores Limitada ofrezca o llegue a ofrecer en el mercado, o los
                                que sean ofrecidos por sus subsidiarias o aliados estratégicos.</li>
                            <li>Informar al cliente, a través de cualquier medio, sobre las actividades publicitarias
                                y/o promocionales de Canal Asesores Limitada.</li>
                            <li>Utilizar mi información para que Canal Asesores Limitada la transmita o transfiera,
                                cuando sea requerido, por alguna entidad estatal o tercero debidamente autorizado.</li>
                            <li>Utilizar mi información para que Canal Asesores Limitada pueda realizar informes
                                estadísticos.</li>
                        </ul>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">9. SUS DERECHOS Y
                            ACCIONES PARA RETIRARSE / CORRECCIONES</h3>
                        <p>Es nuestra obligación proporcionarle los mecanismos adecuados con la finalidad de que usted
                            pueda revisar su información personal y solicitar alguna de las siguientes acciones, si
                            usted lo solicita. De conformidad con lo previsto en el artículo 8 de la ley 1581 de 2012,
                            los titulares podrán:</p>
                        <ul class="list-disc pl-6 space-y-4">
                            <li>Conocer, actualizar y rectificar sus datos personales frente a la sociedad o a los
                                Encargados. Este derecho se podrá ejercer, entre otros frente a datos parciales,
                                inexactos, incompletos, fraccionados, que induzcan a error, o aquellos cuyo Tratamiento
                                esté expresamente prohibido o no haya sido autorizado.</li>
                            <li>Solicitar prueba de la autorización otorgada a la Sociedad, salvo cuando expresamente se
                                exceptúe como requisito para el Tratamiento, de conformidad con lo previsto en el
                                artículo 10 de ley 1582 de 2012.</li>
                            <li>Ser informado por la Sociedad o el Encargado, previa solicitud, respecto del uso que le
                                ha dado a sus datos personales.</li>
                            <li>Presentar ante la Superintendencia de Industria y Comercio quejas por infracciones a lo
                                dispuesto en la ley 1581 de 2012 y las demás normas que la modifiquen, adicionen o
                                complementen.</li>
                            <li>Revocar la autorización y/o solicitar la supresión del dato cuando en el Tratamiento no
                                se respeten los principios, derechos y garantías constitucionales y legales. La
                                revocatoria y/o supresión procederá cuando la Superintendencia de Industria y Comercio
                                haya determinado que la Sociedad o el Encargado han incurrido en conductas contrarias a
                                la ley 1581 de 2012y a la Constitución.</li>
                            <li>Acceder en forma gratuita a sus datos personales que hayan sido objeto de Tratamiento.
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">10. OBLIGACIONES DE
                            CANAL ASESORES LIMITADA, DEBERÁ:</h3>
                        <ul class="list-disc pl-6 space-y-4">
                            <li>Garantizar al Titular, en todo tiempo, el pleno y efectivo ejercicio del derecho de
                                hábeas data.</li>
                            <li>Solicitar y conservar, en las condiciones previstas en la ley 1581 de 2012, copia de la
                                respectiva autorización otorgada por el Titular.</li>
                            <li>Informar debidamente al Titular sobre la finalidad de la recolección y los derechos que
                                le asisten por virtud de la autorización otorgada.</li>
                            <li>Conservar la información bajo las condiciones de seguridad necesarias para impedir su
                                adulteración, pérdida, consulta, uso o acceso no autorizado o fraudulento.</li>
                            <li>Garantizar que la información que se suministre al Encargado sea veraz, completa,
                                exacta, actualizada, comprobable y comprensible.</li>
                            <li>Actualizar la información, comunicando de forma oportuna al Encargado del Tratamiento,
                                todas las novedades respecto de los datos que previamente le haya suministrado y adoptar
                                las demás medidas necesarias para que la información suministrada a este se mantenga
                                actualizada.</li>
                            <li>Rectificar la información cuando sea incorrecta y comunicar lo pertinente al Encargado.
                            </li>
                            <li>Suministrar al Encargado, según el caso, únicamente datos cuyo Tratamiento esté
                                previamente autorizado de conformidad con lo previsto en la ley 1581 de 2012.</li>
                            <li>Exigir al Encargado en todo momento, el respeto a las condiciones de seguridad y
                                privacidad de la información del Titular.</li>
                            <li>Tramitar las consultas y reclamos formulados en los términos señalados en la ley 1581 de
                                2012.</li>
                            <li>Adoptar un manual interno de políticas y procedimientos para garantizar el adecuado
                                cumplimiento ley 1581 de 2012 y en especial, para la atención de consultas y reclamos.
                            </li>
                            <li>Informar al Encargado cuando determinada información se encuentra en discusión por parte
                                del Titular, una vez se haya presentado la reclamación y no haya finalizado el trámite
                                respectivo.</li>
                            <li>Informar a solicitud del Titular sobre el uso dado a sus datos.</li>
                            <li>Informar a la autoridad de protección de datos cuando se presenten violaciones a los
                                códigos de seguridad y existan riesgos en la administración de la información de los
                                Titulares.</li>
                            <li>Cumplir las instrucciones y requerimientos que imparta la Superintendencia de Industria
                                y Comercio.</li>
                        </ul>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">11. PERSONA O ÁREA
                            RESPONSABLE</h3>
                        <p>Toda petición, queja o reclamo relacionado con el manejo de datos personales, en aplicación
                            de lo previsto en la Ley 1581 de 2012 y el Decreto 1377 de 2013, deberá enviarse a:</p>
                        <p class="p-6 bg-slate-50 rounded-xl border-l-4 border-slate-900">
                            <strong>Sociedad:</strong> Canal Asesores Limitada<br>
                            <strong>Departamento:</strong> Norte de Santander<br>
                            <strong>Dirección:</strong> Via Antigua Bocono, Urb El Viñedo CS 22, Villa Del Rosario<br>
                            <strong>Correo electrónico:</strong> canalasesores1@gmail.com<br>
                            <strong>Teléfono:</strong> 317 6462367
                        </p>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">12. PROCEDIMIENTOS DE
                            PRESENTACIÓN Y RESPUESTA DE CONSULTAS QUEJAS Y RECLAMOS</h3>
                        <p>Los reclamos deberán ser formulados por correo electrónico, de acuerdo a la información
                            contenida en este documento, y deberán contener, al menos, la siguiente información:
                            Identificación del Titular, Descripción de los hechos que dan lugar al reclamo, Dirección de
                            correo electrónico del titular y Documentación que se quiera presentar como prueba.</p>
                        <p>Si el reclamo resulta incompleto, se requerirá al interesado dentro de los cinco (5) días
                            siguientes a la recepción del reclamo para que subsane las fallas. Transcurridos dos (2)
                            meses desde la fecha del requerimiento, sin que el solicitante presente la información
                            requerida, se entenderá que ha desistido del reclamo.</p>
                    </div>

                    <div class="space-y-6">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">13. VIGENCIA DE LA BASE
                            DE DATOS</h3>
                        <p>Las Políticas de Tratamiento de Información Personal de Canal Asesores Limitada estarán
                            vigentes a partir del día (1) de julio de 2021. Canal Asesores Limitada se reserva el
                            derecho a modificarlas, en los términos y con las limitaciones previstas en la ley. Las
                            bases de datos administradas por la Sociedad se mantendrán indefinidamente, mientras
                            desarrolle su objeto, y mientras sea necesario para asegurar el cumplimiento de obligaciones
                            de carácter legal.</p>
                    </div>

                    <div class="space-y-6 border-t border-slate-100 pt-12">
                        <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">14. MANEJO Y
                            AUTORIZACIÓN COOKIES</h3>
                        <p>Las Cookies son tipos específicos de información que un sitio web transmite al disco duro del
                            computador del Usuario con el fin de mantener los registros. Las cookies pueden servir para
                            facilitar el uso de un sitio Web, al guardar contraseñas y preferencias mientras el usuario
                            navega en internet. El uso de cookies puede ser desactivado o habilitado en cualquier
                            momento por el Consumidor.</p>
                    </div>

                    <div class="space-y-12 bg-blue-50 p-10 rounded-[2rem] border border-blue-100 mt-20">
                        <h2 class="text-3xl font-black text-blue-900" style="font-family: 'Playfair Display', serif;">
                            AUTORIZACIÓN DE TRATAMIENTO DE DATOS PERSONALES</h2>
                        <p>Con la aceptación del presente documento AUTORIZO de manera previa, expresa, consciente,
                            voluntaria e informada a Canal Asesores Limitada con Nit. 900.798.866- 1, para realizar, de
                            conformidad a la Ley 1581 de 2012, el Decreto 1377 de 2013 y a la política de tratamiento de
                            datos de Canal Asesores Limitada, el tratamiento de mis datos personales, conservarlos bajo
                            su operación, control o supervisión de acuerdo a los lineamientos estipulados en el presente
                            documento. Así mismo, certifico que soy mayor de edad, que cuento con la capacidad para
                            entregar mis datos personales y que toda la información que entrego es cierta y veraz</p>

                        <div class="space-y-6">
                            <h4 class="font-black text-slate-900">1. TRATAMIENTO DE LA INFORMACIÓN:</h4>
                            <p>Autorizo a Canal Asesores Limitada, quien actúa directamente o a través de terceros, como
                                el Responsable del Tratamiento de mis datos personales para que, en el desarrollo de su
                                objeto social y sus actividades comerciales, recolecte, utilice, almacene, custodie,
                                suprima, transfiera, transmita y en general procese mis datos personales.</p>
                        </div>

                        <div class="space-y-6">
                            <h4 class="font-black text-slate-900">2. FINALIDADES DEL TRATAMIENTO:</h4>
                            <ul class="list-none space-y-2">
                                <li><strong>2.1)</strong> Mantenerme informado a través de cualquier medio acerca de
                                    productos o servicios que Canal Asesores Limitada ofrezca.</li>
                                <li><strong>2.2)</strong> Informarme y hacerme partícipe a través de cualquier medio
                                    acerca de actividades publicitarias y/o promocionales.</li>
                                <li><strong>2.3)</strong> Utilizar mi información para que Canal Asesores Limitada
                                    realice todas las gestiones administrativas, financieras y de mercadeo.</li>
                                <li><strong>2.4)</strong> Utilizar mi información para que Canal Asesores Limitada la
                                    transmita o transfiera cuando sea requerido.</li>
                                <li><strong>2.5)</strong> Utilizar mi información para que Canal Asesores Limitada pueda
                                    realizar y/o publicar informes estadísticos.</li>
                                <li><strong>2.6)</strong> Contactarme mediante cualquier medio para que Canal Asesores
                                    Limitada pueda cumplir con su objeto social.</li>
                            </ul>
                        </div>

                        <div class="space-y-6">
                            <h4 class="font-black text-slate-900">3. DERECHOS DEL TITULAR:</h4>
                            <p>Declaro que Canal Asesores Limitada me informó acerca de los derechos que me asisten como
                                titular de los datos personales (conocer, rectificar, actualizar, solicitar prueba de
                                autorización, ser informado del uso, presentar quejas ante la SIC y revocar
                                autorización).</p>
                        </div>
                    </div>
                </div>
            </article>
        </main>

        <footer class="bg-slate-900 text-white py-16 px-6 lg:px-8 text-center">
            <div class="max-w-6xl mx-auto">
                <div class="flex items-center justify-center gap-2 mb-8">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center font-black">C</div>
                    <span class="font-bold">CanalAsesores</span>
                </div>
                <div class="text-sm text-slate-400">
                    &copy; {{ new Date().getFullYear() }} CanalAsesores Limitada. Todos los derechos reservados.
                </div>
                <div class="mt-4">
                    <Link href="/"
                        class="text-blue-500 hover:text-white transition font-bold uppercase tracking-[0.2em] text-[10px]">
                        Volver al inicio</Link>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
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

article {
    animation: fadeInUp 1s ease-out;
}
</style>
