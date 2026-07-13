<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('xibo_display_id')->unique();
            $table->string('public_code')->unique();
            $table->string('display_name');
            $table->string('public_name')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('location_type')->nullable();
            $table->string('location_sector')->nullable();
            $table->boolean('web_visible_from_xibo')->default(false);
            $table->string('commercial_status')->nullable();
            $table->boolean('local_visibility_override')->nullable();
            $table->string('display_type')->nullable();
            $table->string('orientation')->nullable();
            $table->string('resolution')->nullable();
            $table->boolean('licensed')->default(false);
            $table->boolean('logged_in')->default(false);
            $table->boolean('media_inventory_status')->default(false);
            $table->timestamp('last_accessed_at')->nullable();
            $table->unsignedBigInteger('xibo_display_group_id')->nullable();
            $table->json('raw_xibo_payload')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['licensed', 'logged_in', 'media_inventory_status']);
            $table->index(['latitude', 'longitude']);
            $table->index(['web_visible_from_xibo', 'commercial_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screens');
    }
};
