<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'source',
        'status',
        'started_at',
        'finished_at',
        'records_found',
        'records_created',
        'records_updated',
        'records_skipped',
        'error_message',
        'triggered_by_user_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
