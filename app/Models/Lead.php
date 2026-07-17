<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_token',
        'type',
        'status',
        'locale',
        'business_name',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'address',
        'city',
        'municipality',
        'province',
        'location_type',
        'has_tv',
        'has_screen',
        'wants_elixe_screen',
        'wants_ad_revenue',
        'wants_ad_control',
        'sector',
        'activity_sector',
        'interest_zone',
        'campaign_message',
        'preferred_dates',
        'preferred_contact_method',
        'preferred_call_time',
        'budget_range',
        'selected_zones',
        'message',
        'privacy_accepted_at',
        'captcha_verified_at',
    ];

    protected $casts = [
        'has_tv' => 'boolean',
        'has_screen' => 'boolean',
        'wants_elixe_screen' => 'boolean',
        'wants_ad_revenue' => 'boolean',
        'wants_ad_control' => 'boolean',
        'selected_zones' => 'array',
        'privacy_accepted_at' => 'datetime',
        'captcha_verified_at' => 'datetime',
    ];

    public function screens(): BelongsToMany
    {
        return $this->belongsToMany(Screen::class)->withTimestamps();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }

    public function latestActivity(): HasOne
    {
        return $this->hasOne(LeadActivity::class)->latestOfMany();
    }
}
