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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('quantity')->unsigned()->default(0);
            $table->string('sku')->nullable();
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->string('coupon_code')->nullable();
            $table->decimal('weight', 12, 4)->default(0);
            $table->decimal('total_weight', 12, 4)->default(0);
            $table->decimal('base_total_weight', 12, 4)->default(0);
            $table->decimal('price', 12, 4)->default(1);
            $table->decimal('base_price', 12, 4)->default(0);
            $table->decimal('custom_price', 12, 4)->nullable();
            $table->decimal('total', 12, 4)->default(0);
            $table->decimal('base_total', 12, 4)->default(0);
            $table->decimal('tax_percent', 12, 4)->default(0)->nullable();
            $table->decimal('tax_amount', 12, 4)->default(0)->nullable();
            $table->decimal('base_tax_amount', 12, 4)->default(0)->nullable();
            $table->decimal('discount_percent', 12, 4)->default(0);
            $table->decimal('discount_amount', 12, 4)->default(0);
            $table->decimal('base_discount_amount', 12, 4)->default(0);
            $table->uuid('parent_id')->nullable();
            $table->uuid('product_id');
            $table->uuid('cart_id');
            $table->uuid('tax_category_id')->nullable();
            $table->string('applied_cart_rule_ids')->nullable();
            $table->json('additional')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
};
