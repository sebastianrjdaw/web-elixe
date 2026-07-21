<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Models\Faq;
use App\Models\LegalPage;
use App\Models\ResponseTemplate;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->blocks() as $block) {
            ContentBlock::firstOrCreate(['key' => $block['key']], $block);
        }

        foreach ($this->faqs() as $faq) {
            Faq::firstOrCreate([
                'category' => $faq['category'],
                'question_es' => $faq['question_es'],
            ], $faq);
        }

        foreach ($this->legalPages() as $page) {
            LegalPage::firstOrCreate(['slug' => $page['slug']], $page);
        }

        foreach ($this->settings() as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        foreach ($this->responseTemplates() as $template) {
            ResponseTemplate::firstOrCreate(['key' => $template['key']], $template);
        }
    }

    private function blocks(): array
    {
        return [
            [
                'key' => 'hero',
                'title_es' => 'Publicidad local en pantallas reales.',
                'title_gl' => 'Publicidade local en pantallas reais.',
                'subtitle_es' => 'Elixe instala y gestiona pantallas digitales en locales para mostrar contenido, promociones y publicidad de forma sencilla.',
                'subtitle_gl' => 'Elixe instala e xestiona pantallas dixitais en locais para mostrar contido, promocións e publicidade dun xeito sinxelo.',
                'content_es' => null,
                'content_gl' => null,
                'active' => true,
                'sort_order' => 10,
            ],
            [
                'key' => 'venues',
                'title_es' => 'Para locales',
                'title_gl' => 'Para locais',
                'content_es' => 'Tu pantalla también puede mostrar contenido propio del local: promociones, menús, avisos, eventos o mensajes para tus clientes. Elixe se encarga de la configuración, gestión de contenidos y mantenimiento.',
                'content_gl' => 'A túa pantalla tamén pode mostrar contido propio do local: promocións, menús, avisos, eventos ou mensaxes para a clientela. Elixe encárgase da configuración, xestión de contidos e mantemento.',
                'active' => true,
                'sort_order' => 20,
            ],
            [
                'key' => 'advertisers',
                'title_es' => 'Para anunciantes',
                'title_gl' => 'Para anunciantes',
                'content_es' => 'Llega a clientes locales y amplía la visibilidad de tu negocio en pantallas reales, con zonas y tipos de locales seleccionados bajo asesoramiento.',
                'content_gl' => 'Chega a clientes locais e amplía a visibilidade do teu negocio en pantallas reais, con zonas e tipos de locais seleccionados con asesoramento.',
                'active' => true,
                'sort_order' => 30,
            ],
            [
                'key' => 'how_it_works',
                'title_es' => 'Cómo funciona',
                'title_gl' => 'Como funciona',
                'content_es' => 'Recogemos tu solicitud, revisamos el encaje y preparamos una propuesta personalizada. Sin pagos online ni subida de creatividades en el MVP.',
                'content_gl' => 'Recibimos a túa solicitude, revisamos o encaixe e preparamos unha proposta personalizada. Sen pagamentos online nin subida de creatividades no MVP.',
                'active' => true,
                'sort_order' => 40,
            ],
            ['key' => 'feature_screens', 'title_es' => 'Pantallas reales', 'title_gl' => 'Pantallas reais', 'content_es' => 'Instaladas en establecimientos de la red y gestionadas por Elixe.', 'content_gl' => 'Instaladas en establecementos da rede e xestionadas por Elixe.', 'active' => true, 'sort_order' => 50],
            ['key' => 'feature_proximity', 'title_es' => 'Impacto de proximidad', 'title_gl' => 'Impacto de proximidade', 'content_es' => 'Campañas por zona y tipo de local para llegar a una audiencia relevante.', 'content_gl' => 'Campañas por zona e tipo de local para chegar a unha audiencia relevante.', 'active' => true, 'sort_order' => 60],
            ['key' => 'feature_management', 'title_es' => 'Gestión sencilla', 'title_gl' => 'Xestión sinxela', 'content_es' => 'Te acompañamos desde la idea hasta la publicación y el mantenimiento.', 'content_gl' => 'Acompañámoste desde a idea ata a publicación e o mantemento.', 'active' => true, 'sort_order' => 70],
            ['key' => 'process_needs', 'title_es' => 'Cuéntanos qué necesitas', 'title_gl' => 'Cóntanos que necesitas', 'content_es' => 'Local, campaña u otra consulta: un único formulario adaptado a ti.', 'content_gl' => 'Local, campaña ou outra consulta: un único formulario adaptado a ti.', 'active' => true, 'sort_order' => 80],
            ['key' => 'process_proposal', 'title_es' => 'Diseñamos la propuesta', 'title_gl' => 'Deseñamos a proposta', 'content_es' => 'Revisamos zonas, pantallas, objetivos y disponibilidad contigo.', 'content_gl' => 'Revisamos zonas, pantallas, obxectivos e dispoñibilidade contigo.', 'active' => true, 'sort_order' => 90],
            ['key' => 'process_launch', 'title_es' => 'Lo ponemos en marcha', 'title_gl' => 'Poñémolo en marcha', 'content_es' => 'Elixe gestiona la configuración, la publicación y el seguimiento.', 'content_gl' => 'Elixe xestiona a configuración, a publicación e o seguimento.', 'active' => true, 'sort_order' => 100],
        ];
    }

    private function faqs(): array
    {
        return [
            [
                'category' => 'locales',
                'question_es' => '¿Necesito tener una pantalla?',
                'question_gl' => 'Necesito ter unha pantalla?',
                'answer_es' => 'No necesariamente. Si ya tienes una pantalla, Elixe puede ayudarte a activarla; si no la tienes, podemos estudiar una solución.',
                'answer_gl' => 'Non necesariamente. Se xa tes unha pantalla, Elixe pode axudarche a activala; se non a tes, podemos estudar unha solución.',
                'sort_order' => 10,
            ],
            [
                'category' => 'locales',
                'question_es' => '¿Puedo mostrar contenido de mi local?',
                'question_gl' => 'Podo mostrar contido do meu local?',
                'answer_es' => 'Sí. Puedes mostrar promociones, menús, avisos, eventos o mensajes para tus clientes.',
                'answer_gl' => 'Si. Podes mostrar promocións, menús, avisos, eventos ou mensaxes para a túa clientela.',
                'sort_order' => 20,
            ],
            [
                'category' => 'anunciantes',
                'question_es' => '¿Puedo anunciarme solo en una zona?',
                'question_gl' => 'Podo anunciarme só nunha zona?',
                'answer_es' => 'Sí. La propuesta puede orientarse por zonas, tipos de locales o pantallas concretas disponibles.',
                'answer_gl' => 'Si. A proposta pode orientarse por zonas, tipos de locais ou pantallas concretas dispoñibles.',
                'sort_order' => 30,
            ],
            [
                'category' => 'anunciantes',
                'question_es' => '¿Cuánto cuesta anunciarse?',
                'question_gl' => 'Canto custa anunciarse?',
                'answer_es' => 'No mostramos precios cerrados en la web. El equipo prepara una propuesta personalizada según zona, duración y disponibilidad.',
                'answer_gl' => 'Non mostramos prezos pechados na web. O equipo prepara unha proposta personalizada segundo zona, duración e dispoñibilidade.',
                'sort_order' => 40,
            ],
        ];
    }

    private function legalPages(): array
    {
        return [
            [
                'slug' => 'privacidad',
                'title_es' => 'Política de privacidad',
                'title_gl' => 'Política de privacidade',
                'content_es' => 'Texto legal inicial para desarrollo. Debe revisarse antes de producción con los datos definitivos del responsable y tratamientos.',
                'content_gl' => 'Texto legal inicial para desenvolvemento. Debe revisarse antes de produción cos datos definitivos do responsable e tratamentos.',
            ],
            [
                'slug' => 'cookies',
                'title_es' => 'Política de cookies',
                'title_gl' => 'Política de cookies',
                'content_es' => 'Texto inicial de cookies para desarrollo. Sustituir por la versión legal definitiva antes de producción.',
                'content_gl' => 'Texto inicial de cookies para desenvolvemento. Substituír pola versión legal definitiva antes de produción.',
            ],
            [
                'slug' => 'aviso-legal',
                'title_es' => 'Aviso legal',
                'title_gl' => 'Aviso legal',
                'content_es' => 'Aviso legal inicial para desarrollo. Completar con datos fiscales, titularidad y condiciones aplicables.',
                'content_gl' => 'Aviso legal inicial para desenvolvemento. Completar con datos fiscais, titularidade e condicións aplicables.',
            ],
        ];
    }

    private function settings(): array
    {
        return [
            ['key' => 'contact_email', 'value' => 'info@elixe.es', 'label' => 'Email visible', 'type' => 'email', 'is_public' => true],
            ['key' => 'contact_phone', 'value' => '', 'label' => 'Telefono visible', 'type' => 'text', 'is_public' => true],
            ['key' => 'business_hours', 'value' => 'Lunes a viernes, 9:00 - 18:00', 'label' => 'Horario de atencion', 'type' => 'text', 'is_public' => true],
            ['key' => 'leads_email', 'value' => env('ELIXE_LEADS_EMAIL', 'info@elixe.es'), 'label' => 'Email receptor de leads', 'type' => 'email', 'is_public' => false],
        ];
    }

    private function responseTemplates(): array
    {
        $templates = [];

        foreach (['venue', 'advertiser', 'other'] as $type) {
            $templates[] = [
                'key' => "automatic_{$type}_es",
                'name' => "Confirmación automática ({$type}, ES)",
                'lead_type' => $type,
                'locale' => 'es',
                'subject' => 'Hemos recibido tu solicitud, {{contact_name}}',
                'body' => "Hola {{contact_name}},\n\nHemos recibido tu solicitud sobre {{business_name}}. El equipo de Elixe revisará la información y se pondrá en contacto contigo por el medio indicado.\n\nGracias por confiar en Elixe.",
                'is_active' => true,
            ];
            $templates[] = [
                'key' => "automatic_{$type}_gl",
                'name' => "Confirmación automática ({$type}, GL)",
                'lead_type' => $type,
                'locale' => 'gl',
                'subject' => 'Recibimos a túa solicitude, {{contact_name}}',
                'body' => "Ola {{contact_name}},\n\nRecibimos a túa solicitude sobre {{business_name}}. O equipo de Elixe revisará a información e porase en contacto contigo polo medio indicado.\n\nGrazas por confiar en Elixe.",
                'is_active' => true,
            ];
        }

        $templates[] = [
            'key' => 'commercial_followup_es',
            'name' => 'Seguimiento comercial (ES)',
            'lead_type' => null,
            'locale' => 'es',
            'subject' => 'Siguiente paso para {{business_name}}',
            'body' => "Hola {{contact_name}},\n\nHemos revisado tu solicitud y nos gustaría comentar contigo el siguiente paso. Puedes responder a este correo o indicarnos cuándo prefieres que te llamemos.\n\nUn saludo,\nEquipo Elixe",
            'is_active' => true,
        ];

        $templates[] = [
            'key' => 'commercial_followup_gl',
            'name' => 'Seguimento comercial (GL)',
            'lead_type' => null,
            'locale' => 'gl',
            'subject' => 'Seguinte paso para {{business_name}}',
            'body' => "Ola {{contact_name}},\n\nRevisamos a túa solicitude e gustaríanos comentar contigo o seguinte paso. Podes responder a este correo ou indicarnos cando prefires que te chamemos.\n\nUn saúdo,\nEquipo Elixe",
            'is_active' => true,
        ];

        $manualTemplates = [
            'request_information' => [
                'es' => ['Solicitud de información', 'Necesitamos un dato más sobre {{business_name}}', "Hola {{contact_name}},\n\nGracias por tu solicitud. Para poder revisarla necesitamos que nos facilites un poco más de información respondiendo a este correo.\n\nUn saludo,\nEquipo Elixe"],
                'gl' => ['Solicitude de información', 'Necesitamos un dato máis sobre {{business_name}}', "Ola {{contact_name}},\n\nGrazas pola túa solicitude. Para poder revisala necesitamos que nos facilites un pouco máis de información respondendo a este correo.\n\nUn saúdo,\nEquipo Elixe"],
            ],
            'call_proposal' => [
                'es' => ['Propuesta de llamada', '¿Hablamos sobre {{business_name}}?', "Hola {{contact_name}},\n\nNos gustaría comentar contigo las opciones disponibles. Responde a este correo con el día y la franja horaria que prefieras para una llamada.\n\nUn saludo,\nEquipo Elixe"],
                'gl' => ['Proposta de chamada', 'Falamos sobre {{business_name}}?', "Ola {{contact_name}},\n\nGustaríanos comentar contigo as opcións dispoñibles. Responde a este correo co día e a franxa horaria que prefiras para unha chamada.\n\nUn saúdo,\nEquipo Elixe"],
            ],
            'appointment_confirmation' => [
                'es' => ['Confirmación de cita', 'Confirmación de nuestra cita', "Hola {{contact_name}},\n\nTu cita con el equipo de Elixe queda confirmada. Si necesitas cambiarla, responde a este correo y buscaremos otra fecha.\n\nUn saludo,\nEquipo Elixe"],
                'gl' => ['Confirmación de cita', 'Confirmación da nosa cita', "Ola {{contact_name}},\n\nA túa cita co equipo de Elixe queda confirmada. Se necesitas cambiala, responde a este correo e buscaremos outra data.\n\nUn saúdo,\nEquipo Elixe"],
            ],
            'not_viable' => [
                'es' => ['Solicitud no viable', 'Actualización sobre tu solicitud', "Hola {{contact_name}},\n\nTras revisar la información, ahora mismo no podemos ofrecer una opción adecuada para {{business_name}}. Conservaremos tus datos únicamente durante el plazo informado en nuestra política de privacidad.\n\nGracias por pensar en Elixe."],
                'gl' => ['Solicitude non viable', 'Actualización sobre a túa solicitude', "Ola {{contact_name}},\n\nTras revisar a información, agora mesmo non podemos ofrecer unha opción axeitada para {{business_name}}. Conservaremos os teus datos unicamente durante o prazo informado na nosa política de privacidade.\n\nGrazas por pensar en Elixe."],
            ],
            'thank_you' => [
                'es' => ['Agradecimiento', 'Gracias por confiar en Elixe', "Hola {{contact_name}},\n\nGracias por confiar en Elixe para {{business_name}}. Seguimos a tu disposición para cualquier consulta.\n\nUn saludo,\nEquipo Elixe"],
                'gl' => ['Agradecemento', 'Grazas por confiar en Elixe', "Ola {{contact_name}},\n\nGrazas por confiar en Elixe para {{business_name}}. Seguimos á túa disposición para calquera consulta.\n\nUn saúdo,\nEquipo Elixe"],
            ],
        ];

        foreach ($manualTemplates as $key => $locales) {
            foreach ($locales as $locale => [$name, $subject, $body]) {
                $templates[] = [
                    'key' => "{$key}_{$locale}",
                    'name' => "{$name} (".strtoupper($locale).')',
                    'lead_type' => null,
                    'locale' => $locale,
                    'subject' => $subject,
                    'body' => $body,
                    'is_active' => true,
                ];
            }
        }

        return $templates;
    }
}
