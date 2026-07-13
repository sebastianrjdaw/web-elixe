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

        return back();
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
        ];
    }
}
