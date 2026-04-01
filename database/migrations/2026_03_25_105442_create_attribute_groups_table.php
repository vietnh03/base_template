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
        Schema::create('attribute_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('position');
            $table->boolean('is_user_defined')->default(1);
            $table->uuid('attribute_family_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_groups');
    }
};
