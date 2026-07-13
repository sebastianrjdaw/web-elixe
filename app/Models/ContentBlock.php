<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = [
        'key',
        'title_es',
        'title_gl',
        'subtitle_es',
        'subtitle_gl',
        'content_es',
        'content_gl',
        'image_path',
        'active',
        'sort_order',
        'updated_by',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function publicPayload(string $locale = 'es'): array
    {
        return [
            'key' => $this->key,
            'title' => $this->localized('title', $locale),
            'subtitle' => $this->localized('subtitle', $locale),
            'content' => $this->localized('content', $locale),
            'imagePath' => $this->image_path,
            'sortOrder' => $this->sort_order,
        ];
    }

    private function localized(string $field, string $locale): ?string
    {
        $localized = $this->getAttribute("{$field}_{$locale}");

        return $localized ?: $this->getAttribute("{$field}_es");
    }
}
