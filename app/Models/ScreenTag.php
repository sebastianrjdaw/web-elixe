<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreenTag extends Model
{
    use HasFactory;

    protected $fillable = ['screen_id', 'tag', 'value'];

    public function screen(): BelongsTo
    {
        return $this->belongsTo(Screen::class);
    }
}
