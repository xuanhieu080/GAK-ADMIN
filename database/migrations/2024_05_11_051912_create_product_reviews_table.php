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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('product_code')->nullable();
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->string('product_variant_code')->nullable();
            $table->double('rate')->default(5)->nullable();
            $table->string('customer_name')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->text('option_name')->nullable();
            $table->text('option')->nullable();
            $table->text('description')->nullable();
            $table->text('reply')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->timestamp('date');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->double('rate')->default(0)->nullable();
            $table->double('rate_count')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['rate', 'rate_count']);
        });
    }
};
