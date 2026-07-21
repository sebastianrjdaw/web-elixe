<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreenOnboardingRequest extends Model
{
    use HasFactory;

    public const STATUSES = ['borrador', 'pendiente_revision', 'aprobado', 'enviado_a_xibo', 'error_xibo', 'activo', 'descartado'];
    public const LOCATION_TYPES = ['bar', 'restaurante', 'cafeteria', 'lavanderia', 'gimnasio', 'peluqueria', 'clinica', 'tienda', 'hotel', 'supermercado', 'oficina', 'centro_comercial', 'farmacia', 'autoescuela', 'estanco', 'panaderia', 'coworking', 'otro'];
    public const LOCATION_SECTORS = ['hosteleria', 'servicios', 'salud', 'retail', 'ocio', 'turismo', 'empresa', 'transporte', 'educacion', 'otro'];
    public const COMMERCIAL_STATUSES = ['disponible', 'limitado', 'completo', 'pausado', 'mantenimiento', 'privado'];

    protected $guarded = ['id', 'created_by', 'reviewed_by', 'approved_at', 'sent_to_xibo_at', 'xibo_display_id', 'xibo_sync_status', 'xibo_error_message', 'screen_id'];

    protected $casts = [
        'web_visible' => 'boolean', 'has_existing_screen' => 'boolean',
        'requires_elixe_screen' => 'boolean', 'internet_available' => 'boolean',
        'latitude' => 'float', 'longitude' => 'float', 'approved_at' => 'datetime',
        'sent_to_xibo_at' => 'datetime',
    ];

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function screen(): BelongsTo { return $this->belongsTo(Screen::class); }

    public function preparationBlockers(): array
    {
        $labels = [
            'internal_code' => 'Falta el nombre interno.', 'establishment_name' => 'Falta el nombre del establecimiento.',
            'address' => 'Falta la dirección.', 'municipality' => 'Falta el municipio.', 'province' => 'Falta la provincia.',
            'latitude' => 'Falta la latitud.', 'longitude' => 'Falta la longitud.', 'location_type' => 'No se ha seleccionado el tipo de local.',
            'location_sector' => 'No se ha seleccionado el sector.', 'commercial_status' => 'Falta el estado comercial.',
        ];

        return collect($labels)->filter(fn (string $label, string $field) => $this->{$field} === null || $this->{$field} === '')->values()->all();
    }
}
