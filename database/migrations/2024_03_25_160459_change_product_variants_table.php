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
            $table->dropColumn(['attribute_id','variant_id','attribute_group_id','priority']);
            $table->string('name')->default(0)->nullable();
            $table->integer('qty')->default(0)->nullable();
            $table->double('price')->default(0)->nullable();
            $table->text('description')->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_key', 255)->nullable();
            $table->json('options')->nullable();
            $table->boolean('is_active')->default(0)->nullable()->change();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('video_link', 350)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedBigInteger('attribute_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->unsignedBigInteger('attribute_group_id')->nullable();
            $table->integer('priority')->nullable()->default(0);
            $table->dropColumn(['options','name','qty','description','meta_title','meta_description','meta_key']);
            $table->boolean('is_active')->default(0)->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['video_link']);
        });
    }
};
