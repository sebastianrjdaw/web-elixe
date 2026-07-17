<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        DB::table('users')->update(['is_admin' => true]);

        Schema::table('screens', function (Blueprint $table) {
            $table->ulid('public_id')->nullable()->after('id');
        });

        DB::table('screens')->orderBy('id')->each(function (object $screen): void {
            DB::table('screens')->where('id', $screen->id)->update(['public_id' => (string) Str::ulid()]);
        });

        Schema::table('screens', function (Blueprint $table) {
            $table->unique('public_id');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->uuid('submission_token')->nullable()->unique()->after('id');
            $table->string('locale', 2)->default('es')->after('status');
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 60);
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'created_at']);
        });

        Schema::create('response_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('lead_type')->nullable();
            $table->string('locale', 2)->default('es');
            $table->string('subject');
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['lead_type', 'locale', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('response_templates');
        Schema::dropIfExists('lead_activities');

        Schema::table('leads', function (Blueprint $table) {
            $table->dropUnique(['submission_token']);
            $table->dropColumn(['submission_token', 'locale']);
        });

        Schema::table('screens', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
            $table->dropColumn('public_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
