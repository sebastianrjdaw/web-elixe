<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Screen extends Model
{
    use HasFactory;

    protected $fillable = [
        'xibo_display_id',
        'public_code',
        'display_name',
        'public_name',
        'description',
        'address',
        'municipality',
        'province',
        'latitude',
        'longitude',
        'location_type',
        'location_sector',
        'web_visible_from_xibo',
        'commercial_status',
        'local_visibility_override',
        'display_type',
        'orientation',
        'resolution',
        'licensed',
        'logged_in',
        'media_inventory_status',
        'last_accessed_at',
        'xibo_display_group_id',
        'raw_xibo_payload',
        'synced_at',
    ];

    protected $casts = [
        'licensed' => 'boolean',
        'logged_in' => 'boolean',
        'media_inventory_status' => 'boolean',
        'web_visible_from_xibo' => 'boolean',
        'local_visibility_override' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'raw_xibo_payload' => 'array',
        'last_accessed_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function tags(): HasMany
    {
        return $this->hasMany(ScreenTag::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('licensed', true)
            ->where('logged_in', true)
            ->where('media_inventory_status', true);
    }

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('web_visible_from_xibo', true)
            ->where('commercial_status', 'disponible')
            ->where(fn (Builder $query) => $query
                ->whereNull('local_visibility_override')
                ->orWhere('local_visibility_override', true));
    }

    public function tagValue(string $tag): ?string
    {
        return $this->tags->firstWhere('tag', $tag)?->value;
    }

    public function publicPayload(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->public_name ?: $this->description ?: 'Pantalla Elixe',
            'municipality' => $this->municipality,
            'province' => $this->province,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'locationType' => $this->location_type ?: $this->tagValue('loc_tipo'),
            'locationSector' => $this->location_sector ?: $this->tagValue('loc_sector'),
            'commercialStatus' => $this->commercial_status ?: $this->tagValue('com_estado'),
        ];
    }

    public function missingFields(): array
    {
        return array_values(array_filter([
            $this->latitude === null ? 'latitude' : null,
            $this->longitude === null ? 'longitude' : null,
            blank($this->location_type) && $this->tagValue('loc_tipo') === null ? 'loc_tipo' : null,
            blank($this->location_sector) && $this->tagValue('loc_sector') === null ? 'loc_sector' : null,
            $this->tagValue('web_visible') === null ? 'web_visible' : null,
            blank($this->commercial_status) && $this->tagValue('com_estado') === null ? 'com_estado' : null,
        ]));
    }

    public function publicVisibilityBlockers(): array
    {
        $blockers = [];

        if ($this->latitude === null || $this->longitude === null) {
            $blockers[] = 'Faltan coordenadas.';
        }

        if ($this->tagValue('web_visible') === null) {
            $blockers[] = 'Falta el tag web_visible=true en Xibo.';
        } elseif (! $this->web_visible_from_xibo) {
            $blockers[] = 'El tag web_visible de Xibo no esta en true.';
        }

        if (($this->commercial_status ?: $this->tagValue('com_estado')) !== 'disponible') {
            $blockers[] = 'El estado comercial no es disponible.';
        }

        if ($this->local_visibility_override === false) {
            $blockers[] = 'Hay un override local que oculta la pantalla.';
        }

        return $blockers;
    }
}
