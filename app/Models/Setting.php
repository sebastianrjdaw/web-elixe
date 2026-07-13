<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'label',
        'type',
        'is_public',
        'updated_by',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }
}
