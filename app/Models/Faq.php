<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'category',
        'question_es',
        'question_gl',
        'answer_es',
        'answer_gl',
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
            'id' => $this->id,
            'category' => $this->category,
            'question' => $this->localized('question', $locale),
            'answer' => $this->localized('answer', $locale),
        ];
    }

    private function localized(string $field, string $locale): string
    {
        return $this->getAttribute("{$field}_{$locale}") ?: $this->getAttribute("{$field}_es");
    }
}
