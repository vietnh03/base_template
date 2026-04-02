<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_category_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_category_id');
            $table->string('locale');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['post_category_id', 'locale'], 'post_category_translation_index_unique');
            $table->unique(['slug', 'locale'], 'post_category_translation_slug_index_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_category_translations');
    }
};
