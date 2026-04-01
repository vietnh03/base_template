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
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sku')->nullable();
            $table->string('name')->nullable();
            $table->string('coupon_code')->nullable();
            $table->decimal('weight', 12, 4)->default(0)->nullable();
            $table->decimal('total_weight', 12, 4)->default(0)->nullable();
            $table->integer('qty_ordered')->default(0)->nullable();
            $table->integer('qty_shipped')->default(0)->nullable();
            $table->integer('qty_invoiced')->default(0)->nullable();
            $table->integer('qty_canceled')->default(0)->nullable();
            $table->integer('qty_refunded')->default(0)->nullable();
            $table->decimal('price', 12, 4)->default(0);
            $table->decimal('base_price', 12, 4)->default(0);
            $table->decimal('total', 12, 4)->default(0);
            $table->decimal('base_total', 12, 4)->default(0);
            $table->decimal('total_invoiced', 12, 4)->default(0);
            $table->decimal('base_total_invoiced', 12, 4)->default(0);
            $table->decimal('amount_refunded', 12, 4)->default(0);
            $table->decimal('base_amount_refunded', 12, 4)->default(0);
            $table->decimal('discount_percent', 12, 4)->default(0)->nullable();
            $table->decimal('discount_amount', 12, 4)->default(0)->nullable();
            $table->decimal('base_discount_amount', 12, 4)->default(0)->nullable();
            $table->decimal('discount_invoiced', 12, 4)->default(0)->nullable();
            $table->decimal('base_discount_invoiced', 12, 4)->default(0)->nullable();
            $table->decimal('discount_refunded', 12, 4)->default(0)->nullable();
            $table->decimal('base_discount_refunded', 12, 4)->default(0)->nullable();
            $table->decimal('tax_percent', 12, 4)->default(0)->nullable();
            $table->decimal('tax_amount', 12, 4)->default(0)->nullable();
            $table->decimal('base_tax_amount', 12, 4)->default(0)->nullable();
            $table->decimal('tax_amount_invoiced', 12, 4)->default(0)->nullable();
            $table->decimal('base_tax_amount_invoiced', 12, 4)->default(0)->nullable();
            $table->decimal('tax_amount_refunded', 12, 4)->default(0)->nullable();
            $table->decimal('base_tax_amount_refunded', 12, 4)->default(0)->nullable();
            $table->uuid('product_id')->nullable();
            $table->uuid('order_id')->nullable();
            $table->uuid('parent_id')->nullable();
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
        Schema::dropIfExists('order_items');
    }
};
