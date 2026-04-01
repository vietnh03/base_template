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
        Schema::create('product_flat', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sku');
            $table->string('name')->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('url_key')->nullable();
            $table->boolean('new')->nullable();
            $table->boolean('featured')->nullable();
            $table->boolean('status')->nullable();
            $table->string('thumbnail')->nullable();

            $table->decimal('price', 12, 4)->nullable();
            $table->decimal('cost_price', 12, 4)->nullable();
            $table->decimal('special_price', 12, 4)->nullable();
            $table->date('special_price_from')->nullable();
            $table->date('special_price_to')->nullable();
            $table->decimal('weight', 12, 4)->nullable();

            $table->uuid('product_id');
            $table->uuid('parent_id')->nullable();
            $table->uuid('attribute_family_id')->nullable();

            $table->string('locale')->nullable();

            $table->boolean('visible_individually')->nullable();

            $table->text('meta_title')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();

            $table->unique(['product_id', 'locale'], 'product_flat_unique_index');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_flat');
    }
};
