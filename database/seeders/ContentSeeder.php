<?php

namespace Database\Seeders;

use App\Models\ContentBlock;
use App\Models\Faq;
use App\Models\LegalPage;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->blocks() as $block) {
            ContentBlock::updateOrCreate(['key' => $block['key']], $block);
        }

        foreach ($this->faqs() as $faq) {
            Faq::updateOrCreate([
                'category' => $faq['category'],
                'question_es' => $faq['question_es'],
            ], $faq);
        }

        foreach ($this->legalPages() as $page) {
            LegalPage::updateOrCreate(['slug' => $page['slug']], $page);
        }

        foreach ($this->settings() as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
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
}
