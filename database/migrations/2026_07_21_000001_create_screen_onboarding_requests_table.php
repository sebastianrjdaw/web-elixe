<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screen_onboarding_requests', function (Blueprint $table) {
            $table->id();
            $table->string('status', 30)->default('borrador')->index();
            $table->string('internal_code')->unique();
            $table->string('establishment_name');
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 40)->nullable();
            $table->string('address')->nullable();
            $table->string('municipality')->nullable()->index();
            $table->string('province')->nullable()->index();
            $table->string('postal_code', 12)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_type', 40)->nullable()->index();
            $table->string('location_sector', 40)->nullable()->index();
            $table->boolean('web_visible')->default(false);
            $table->string('commercial_status', 30)->default('privado')->index();
            $table->boolean('has_existing_screen')->default(false);
            $table->boolean('requires_elixe_screen')->default(false);
            $table->boolean('internet_available')->default(false);
            $table->string('physical_location')->nullable();
            $table->text('installation_notes')->nullable();
            $table->text('advertising_restrictions')->nullable();
            $table->text('internal_notes')->nullable();
            $table->unsignedBigInteger('xibo_display_id')->nullable()->unique();
            $table->string('xibo_sync_status', 30)->default('no_enviado');
            $table->text('xibo_error_message')->nullable();
            $table->foreignId('screen_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('sent_to_xibo_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screen_onboarding_requests');
    }
};
