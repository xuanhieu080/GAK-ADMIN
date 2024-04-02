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
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('show_dashboard')->default(0)->nullable();
        });
        Schema::table('attribute_groups', function (Blueprint $table) {
            $table->integer('priority')->default(0)->nullable();
        });
        Schema::table('attributes', function (Blueprint $table) {
            $table->text('link')->nullable();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_hot')->default(0)->nullable();
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_hot')->default(0)->nullable();
            $table->boolean('is_new')->default(0)->nullable();
            $table->integer('view')->default(0)->nullable();
        });
        Schema::table('post_groups', function (Blueprint $table) {
            $table->boolean('is_hot')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['show_dashboard']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_hot']);
        });

        Schema::table('attribute_groups', function (Blueprint $table) {
            $table->dropColumn(['priority']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['is_hot', 'is_new', 'view']);
        });

        Schema::table('post_groups', function (Blueprint $table) {
            $table->dropColumn(['is_hot']);
        });

        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn(['link']);
        });
    }
};
