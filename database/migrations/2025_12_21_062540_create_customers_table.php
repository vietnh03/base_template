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
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('gender')->length(50)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('password')->nullable();
            $table->boolean('is_verified')->default(0);
            $table->string('token')->nullable();
            $table->text('notes')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};