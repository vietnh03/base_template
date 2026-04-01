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
            $table->string('full_name', 100)->comment('Tên khách hàng');
            $table->string('phone_number', 20)->comment('Số điện thoại');
            $table->string('email', 100)->nullable()->comment('Email');
            $table->enum('customer_type', ['Individual', 'Business'])->default('Individual');
            $table->enum('customer_status', ['Lead', 'Active', 'Inactive', 'VIP'])->default('Lead');
            $table->uuid('assigned_staff_id')->nullable()->comment('UUID của nhân viên phụ trách');

            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('source', 50)->default('Other');
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('customer_status');
            $table->index('customer_type');
            $table->index('assigned_staff_id');
            $table->index('email');
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