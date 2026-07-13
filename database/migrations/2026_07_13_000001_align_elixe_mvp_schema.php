<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('screens', function (Blueprint $table) {
            if (! Schema::hasColumn('screens', 'public_name')) {
                $table->string('public_name')->nullable()->after('display_name');
            }

            if (! Schema::hasColumn('screens', 'municipality')) {
                $table->string('municipality')->nullable()->after('address');
            }

            if (! Schema::hasColumn('screens', 'province')) {
                $table->string('province')->nullable()->after('municipality');
            }

            if (! Schema::hasColumn('screens', 'location_type')) {
                $table->string('location_type')->nullable()->after('longitude');
            }

            if (! Schema::hasColumn('screens', 'location_sector')) {
                $table->string('location_sector')->nullable()->after('location_type');
            }

            if (! Schema::hasColumn('screens', 'web_visible_from_xibo')) {
                $table->boolean('web_visible_from_xibo')->default(false)->after('location_sector');
            }

            if (! Schema::hasColumn('screens', 'commercial_status')) {
                $table->string('commercial_status')->nullable()->after('web_visible_from_xibo');
            }

            if (! Schema::hasColumn('screens', 'local_visibility_override')) {
                $table->boolean('local_visibility_override')->nullable()->after('commercial_status');
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'municipality')) {
                $table->string('municipality')->nullable()->after('city');
            }

            if (! Schema::hasColumn('leads', 'has_screen')) {
                $table->boolean('has_screen')->nullable()->after('has_tv');
            }

            if (! Schema::hasColumn('leads', 'activity_sector')) {
                $table->string('activity_sector')->nullable()->after('sector');
            }

            if (! Schema::hasColumn('leads', 'interest_zone')) {
                $table->string('interest_zone')->nullable()->after('activity_sector');
            }

            if (! Schema::hasColumn('leads', 'preferred_contact_method')) {
                $table->string('preferred_contact_method')->nullable()->after('preferred_dates');
            }

            if (! Schema::hasColumn('leads', 'preferred_call_time')) {
                $table->string('preferred_call_time')->nullable()->after('preferred_contact_method');
            }

            if (! Schema::hasColumn('leads', 'captcha_verified_at')) {
                $table->timestamp('captcha_verified_at')->nullable()->after('privacy_accepted_at');
            }
        });

        Schema::table('sync_runs', function (Blueprint $table) {
            if (! Schema::hasColumn('sync_runs', 'records_skipped')) {
                $table->unsignedInteger('records_skipped')->default(0)->after('records_updated');
            }

            if (! Schema::hasColumn('sync_runs', 'triggered_by_user_id')) {
                $table->foreignId('triggered_by_user_id')->nullable()->after('error_message')->constrained('users')->nullOnDelete();
            }
        });
    }
};
