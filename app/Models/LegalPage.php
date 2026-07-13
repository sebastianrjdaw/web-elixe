<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    protected $fillable = [
        'slug',
        'title_es',
        'title_gl',
        'content_es',
        'content_gl',
        'active',
        'updated_by',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function publicPayload(string $locale = 'es'): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->localized('title', $locale),
            'content' => $this->localized('content', $locale),
        ];
    }

    private function localized(string $field, string $locale): string
    {
        return $this->getAttribute("{$field}_{$locale}") ?: $this->getAttribute("{$field}_es");
    }
}
