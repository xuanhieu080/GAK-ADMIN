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
        Schema::table('variants', function (Blueprint $table) {
            $table->boolean('is_main')->default(0)->nullable();
        });
        Schema::table('attribute_groups', function (Blueprint $table) {
            $table->boolean('is_main')->default(0)->nullable();
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedBigInteger('product_main_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->dropColumn(['is_main']);
        });
        Schema::table('attribute_groups', function (Blueprint $table) {
            $table->dropColumn(['is_main']);
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['product_main_id']);
        });
    }
};
