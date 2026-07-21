<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $blocks = [
            ['key' => 'feature_screens', 'title_es' => 'Pantallas reales', 'title_gl' => 'Pantallas reais', 'content_es' => 'Instaladas en establecimientos de la red y gestionadas por Elixe.', 'content_gl' => 'Instaladas en establecementos da rede e xestionadas por Elixe.', 'sort_order' => 50],
            ['key' => 'feature_proximity', 'title_es' => 'Impacto de proximidad', 'title_gl' => 'Impacto de proximidade', 'content_es' => 'Campañas por zona y tipo de local para llegar a una audiencia relevante.', 'content_gl' => 'Campañas por zona e tipo de local para chegar a unha audiencia relevante.', 'sort_order' => 60],
            ['key' => 'feature_management', 'title_es' => 'Gestión sencilla', 'title_gl' => 'Xestión sinxela', 'content_es' => 'Te acompañamos desde la idea hasta la publicación y el mantenimiento.', 'content_gl' => 'Acompañámoste desde a idea ata a publicación e o mantemento.', 'sort_order' => 70],
            ['key' => 'process_needs', 'title_es' => 'Cuéntanos qué necesitas', 'title_gl' => 'Cóntanos que necesitas', 'content_es' => 'Local, campaña u otra consulta: un único formulario adaptado a ti.', 'content_gl' => 'Local, campaña ou outra consulta: un único formulario adaptado a ti.', 'sort_order' => 80],
            ['key' => 'process_proposal', 'title_es' => 'Diseñamos la propuesta', 'title_gl' => 'Deseñamos a proposta', 'content_es' => 'Revisamos zonas, pantallas, objetivos y disponibilidad contigo.', 'content_gl' => 'Revisamos zonas, pantallas, obxectivos e dispoñibilidade contigo.', 'sort_order' => 90],
            ['key' => 'process_launch', 'title_es' => 'Lo ponemos en marcha', 'title_gl' => 'Poñémolo en marcha', 'content_es' => 'Elixe gestiona la configuración, la publicación y el seguimiento.', 'content_gl' => 'Elixe xestiona a configuración, a publicación e o seguimento.', 'sort_order' => 100],
        ];

        foreach ($blocks as $block) {
            DB::table('content_blocks')->updateOrInsert(
                ['key' => $block['key']],
                [...$block, 'active' => true, 'created_at' => $now, 'updated_at' => $now],
            );
        }
    }

    public function down(): void
    {
        DB::table('content_blocks')->whereIn('key', [
            'feature_screens', 'feature_proximity', 'feature_management',
            'process_needs', 'process_proposal', 'process_launch',
        ])->delete();
    }
};
