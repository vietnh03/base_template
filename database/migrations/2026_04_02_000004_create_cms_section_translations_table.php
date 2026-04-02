<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cms_section_translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cms_section_id');
            $table->string('locale');
            $table->json('content')->nullable();
            $table->unique(['cms_section_id', 'locale']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_section_translations');
    }
};
