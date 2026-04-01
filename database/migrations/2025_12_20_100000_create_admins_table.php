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
        Schema::create('admins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['super_admin', 'admin'])->default('admin')->comment('Admin role');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Account status');
            $table->json('permissions')->nullable()->comment('Custom permissions');
            $table->timestamp('last_login_at')->nullable()->comment('Last login timestamp');
            $table->string('last_login_ip', 45)->nullable()->comment('Last login IP address');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes()->comment('Soft delete timestamp');

            // Indexes
            $table->index('role');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
