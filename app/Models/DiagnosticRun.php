<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticRun extends Model
{
    protected $fillable = [
        'status',
        'checks',
        'triggered_by_user_id',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'checks' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }
}
