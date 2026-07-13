<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screen_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('screen_id')->constrained()->cascadeOnDelete();
            $table->string('tag');
            $table->string('value')->nullable();
            $table->timestamps();

            $table->unique(['screen_id', 'tag']);
            $table->index(['tag', 'value']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screen_tags');
    }
};
