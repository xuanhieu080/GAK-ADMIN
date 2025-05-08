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
        Schema::table('attribute_groups', function (Blueprint $table) {
            $table->string('name_en')->nullable();
            $table->string('link_en', 400)->nullable();
            $table->string('link', 400)->nullable()->change();
        });

        Schema::table('attributes', function (Blueprint $table) {
            $table->string('name_en')->nullable();
            $table->string('link_en', 400)->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_en')->nullable();
            $table->string('slug_en')->index()->unique()->nullable();
            $table->text('description_en')->nullable();
            $table->text('content_seo_en')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_key_en')->nullable();
            $table->string('meta_description_en', 400)->nullable();
            $table->string('meta_description', 400)->nullable()->change();
        });

        Schema::table('page_groups', function (Blueprint $table) {
            $table->string('name_en')->nullable();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('name_en')->nullable();
            $table->string('title_en', 350)->nullable();
            $table->text('description_en')->nullable();
            $table->string('slug_en')->index()->unique()->nullable();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_key_en')->nullable();
            $table->string('meta_description_en', 400)->nullable();
            $table->string('meta_description', 400)->nullable()->change();
            $table->text('description_short_en')->nullable();
            $table->string('link_en', 400)->nullable();
        });

        Schema::table('post_groups', function (Blueprint $table) {
            $table->string('name_en')->nullable();
            $table->string('slug_en')->index()->unique()->nullable();
            $table->text('description_en')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_key_en')->nullable();
            $table->string('meta_description_en', 400)->nullable();
            $table->string('meta_description', 400)->nullable()->change();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('title_en')->nullable();
            $table->string('slug_en')->index()->unique()->nullable();
            $table->text('content_en')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_key_en')->nullable();
            $table->string('meta_description_en', 400)->nullable();
            $table->string('meta_description', 400)->nullable()->change();
        });

        Schema::table('product_variant_mains', function (Blueprint $table) {
            $table->text('description_en')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_key_en')->nullable();
            $table->string('meta_description_en', 400)->nullable();
            $table->string('meta_description', 400)->nullable()->change();
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('name_en')->nullable();
            $table->text('description_en')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_key_en')->nullable();
            $table->string('meta_description_en', 400)->nullable();
            $table->string('meta_description', 400)->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('slug_en')->unique()->nullable();
            $table->string('name_en')->nullable();
            $table->text('description_en')->nullable();
            $table->text('highlight_en')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->string('meta_key_en')->nullable();
            $table->string('meta_description_en', 400)->nullable();
            $table->string('meta_description', 400)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attribute_groups', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'link_en']);
        });

        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'link_en']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'slug_en', 'description_en', 'content_seo_en', 'meta_title_en', 'meta_key_en', 'meta_description_en']);
        });

        Schema::table('page_groups', function (Blueprint $table) {
            $table->dropColumn(['name_en']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'title_en', 'description_en', 'slug_en', 'meta_title_en', 'meta_key_en', 'meta_description_en','description_short_en','link_en']);
        });

        Schema::table('post_groups', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'slug_en', 'description_en', 'meta_title_en', 'meta_key_en', 'meta_description_en']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'slug_en', 'content_en', 'meta_title_en', 'meta_key_en', 'meta_description_en']);
        });

        Schema::table('product_variant_mains', function (Blueprint $table) {
            $table->dropColumn(['description_en', 'meta_title_en', 'meta_key_en', 'meta_description_en']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'description_en', 'meta_title_en', 'meta_key_en', 'meta_description_en']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name_en','slug_en', 'description_en', 'highlight_en', 'meta_title_en', 'meta_key_en', 'meta_description_en']);
        });
    }
};
