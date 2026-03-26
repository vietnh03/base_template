<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku')->unique();
            $table->string('type');
            $table->boolean('status')->default(0);
            $table->decimal('cost_price', 12, 4)->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('attribute_family_id')->unsigned()->nullable();
            $table->json('additional')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->integer('category_id')->unsigned();
        });

        Schema::create('product_relations', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned();
            $table->integer('child_id')->unsigned();
        });

        Schema::create('product_super_attributes', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->integer('attribute_id')->unsigned();
        });

        Schema::create('product_up_sells', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned();
            $table->integer('child_id')->unsigned();
        });

        Schema::create('product_cross_sells', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned();
            $table->integer('child_id')->unsigned();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('product_tags', function (Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->integer('tag_id')->unsigned();

            $table->primary(['product_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_cross_sells');

        Schema::dropIfExists('product_up_sells');

        Schema::dropIfExists('product_super_attributes');

        Schema::dropIfExists('product_relations');

        Schema::dropIfExists('product_categories');

        Schema::dropIfExists('product_tags');

        Schema::dropIfExists('tags');

        Schema::dropIfExists('products');
    }
};
