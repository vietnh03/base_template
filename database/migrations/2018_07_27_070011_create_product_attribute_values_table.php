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
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('locale')->nullable();
            $table->text('text_value')->nullable();
            $table->boolean('boolean_value')->nullable();
            $table->integer('integer_value')->nullable();
            $table->decimal('float_value', 12, 4)->nullable();
            $table->dateTime('datetime_value')->nullable();
            $table->date('date_value')->nullable();
            $table->json('json_value')->nullable();
            $table->uuid('product_id');
            $table->uuid('attribute_id');

            $table->unique(['locale', 'attribute_id', 'product_id'], 'attribute_value_index_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_attribute_values');
    }
};
