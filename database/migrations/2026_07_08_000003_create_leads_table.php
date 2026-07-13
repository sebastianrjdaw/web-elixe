<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('status')->default('new');
            $table->string('business_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->string('location_type')->nullable();
            $table->boolean('has_tv')->nullable();
            $table->boolean('has_screen')->nullable();
            $table->boolean('wants_elixe_screen')->nullable();
            $table->boolean('wants_ad_revenue')->nullable();
            $table->boolean('wants_ad_control')->nullable();
            $table->string('sector')->nullable();
            $table->string('activity_sector')->nullable();
            $table->string('interest_zone')->nullable();
            $table->text('campaign_message')->nullable();
            $table->string('preferred_dates')->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->string('preferred_call_time')->nullable();
            $table->string('budget_range')->nullable();
            $table->json('selected_zones')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('privacy_accepted_at');
            $table->timestamp('captcha_verified_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
