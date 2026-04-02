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
        Schema::create('post_tag_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_tag_id');
            $table->string('locale');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['post_tag_id', 'locale'], 'post_tag_translation_index_unique');
            $table->unique(['slug', 'locale'], 'post_tag_translation_slug_index_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_tag_translations');
    }
};
