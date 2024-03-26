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
            $table->nestedSet();
            $table->boolean('show_header')->default(0)->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_key', 255)->nullable();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('slug', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_key', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropNestedSet();
            $table->dropColumn(['show_header', 'slug', 'meta_title','meta_description', 'meta_key']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['slug', 'meta_title','meta_description', 'meta_key']);
        });
    }
};
