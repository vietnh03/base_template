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
        Schema::create('shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('status')->nullable();
            $table->integer('total_qty')->nullable();
            $table->integer('total_weight')->nullable();
            $table->string('carrier_code')->nullable();
            $table->string('carrier_title')->nullable();
            $table->text('track_number')->nullable();
            $table->boolean('email_sent')->default(0);
            $table->uuid('user_id')->nullable();
            $table->string('user_type')->nullable();
            $table->uuid('order_id');
            $table->uuid('order_address_id')->nullable();
            $table->integer('inventory_source_id')->unsigned()->nullable();
            $table->string('inventory_source_name')->nullable();
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
        Schema::dropIfExists('shipments');
    }
};
