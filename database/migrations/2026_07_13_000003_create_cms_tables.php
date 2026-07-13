<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title_es')->nullable();
            $table->string('title_gl')->nullable();
            $table->text('subtitle_es')->nullable();
            $table->text('subtitle_gl')->nullable();
            $table->longText('content_es')->nullable();
            $table->longText('content_gl')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('general');
            $table->string('question_es');
            $table->string('question_gl')->nullable();
            $table->longText('answer_es');
            $table->longText('answer_gl')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['category', 'active', 'sort_order']);
        });

        Schema::create('legal_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_es');
            $table->string('title_gl')->nullable();
            $table->longText('content_es');
            $table->longText('content_gl')->nullable();
            $table->boolean('active')->default(true);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->string('type')->default('text');
            $table->boolean('is_public')->default(false);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('legal_pages');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('content_blocks');
    }
};
