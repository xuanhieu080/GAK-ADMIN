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
        Schema::table('product_variants', function (Blueprint $table) {
            $table->text('option_name')->nullable();
            $table->double('price_discount')->default(0)->nullable();
            $table->integer('discount')->default(0)->nullable();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->double('price_discount')->default(0)->nullable();
            $table->integer('discount')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['option_name', 'price_discount', 'discount']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price_discount', 'discount']);
        });
    }
};
