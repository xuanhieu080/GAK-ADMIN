<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_upcoming')->default(0)->nullable();
            $table->boolean('is_new')->default(0)->nullable();
            $table->boolean('is_uniform')->default(0)->nullable();
        });
        Schema::table('attribute_groups', function (Blueprint $table) {
            $table->boolean('is_color')->default(0)->nullable();
            $table->string('link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_upcoming', 'is_new', 'is_uniform']);
        });

        Schema::table('attribute_groups', function (Blueprint $table) {
            $table->dropColumn(['is_color', 'link']);
        });
    }
};
