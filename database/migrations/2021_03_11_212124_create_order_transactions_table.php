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
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_id');
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->decimal('amount', 12, 4)->default(0)->nullable();
            $table->string('payment_method')->nullable();
            $table->json('data')->nullable();
            $table->uuid('invoice_id')->nullable();
            $table->uuid('order_id');
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
        Schema::dropIfExists('order_transactions');
    }
};
