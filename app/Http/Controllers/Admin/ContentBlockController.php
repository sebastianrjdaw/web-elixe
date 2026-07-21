<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContentBlockController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/ContentBlocks', [
            'blocks' => ContentBlock::orderBy('sort_order')->get()->map(fn (ContentBlock $block) => $this->payload($block)),
        ]);
    }

    public function update(Request $request, ContentBlock $contentBlock): RedirectResponse
    {
        $data = $request->validate([
            'title_es' => ['nullable', 'string', 'max:255'],
            'title_gl' => ['nullable', 'string', 'max:255'],
            'subtitle_es' => ['nullable', 'string', 'max:1000'],
            'subtitle_gl' => ['nullable', 'string', 'max:1000'],
            'content_es' => ['nullable', 'string', 'max:8000'],
            'content_gl' => ['nullable', 'string', 'max:8000'],
            'active' => ['boolean'],
        ]);

        $old = $contentBlock->only(array_keys($data));
        $contentBlock->update(array_merge($data, ['updated_by' => $request->user()?->id]));

        AuditLogger::record('content.updated', $contentBlock, $old, $contentBlock->only(array_keys($data)), $request);

        return back()->with('success', 'Contenido actualizado correctamente.');
    }

    private function payload(ContentBlock $block): array
    {
        return [
            'id' => $block->id,
            'key' => $block->key,
            'title_es' => $block->title_es,
            'title_gl' => $block->title_gl,
            'subtitle_es' => $block->subtitle_es,
            'subtitle_gl' => $block->subtitle_gl,
            'content_es' => $block->content_es,
            'content_gl' => $block->content_gl,
            'active' => $block->active,
            'sort_order' => $block->sort_order,
            'label' => $this->labels()[$block->key] ?? $block->key,
            'group' => str_starts_with($block->key, 'feature_') ? 'Cards de beneficios' : (str_starts_with($block->key, 'process_') ? 'Pasos del proceso' : 'Secciones principales'),
        ];
    }

    private function labels(): array
    {
        return [
            'hero' => 'Cabecera principal', 'venues' => 'Card para locales', 'advertisers' => 'Card para anunciantes',
            'how_it_works' => 'Introducción: cómo funciona', 'feature_screens' => 'Card 1: pantallas reales',
            'feature_proximity' => 'Card 2: impacto de proximidad', 'feature_management' => 'Card 3: gestión sencilla',
            'process_needs' => 'Paso 1: cuéntanos qué necesitas', 'process_proposal' => 'Paso 2: diseñamos la propuesta',
            'process_launch' => 'Paso 3: puesta en marcha',
        ];
    }
}
