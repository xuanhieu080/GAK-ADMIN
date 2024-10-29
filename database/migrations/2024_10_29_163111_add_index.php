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
            $table->index('name');
            $table->index('priority');
            $table->index('is_color');
            $table->index('is_main');
        });

        Schema::table('attributes', function (Blueprint $table) {
            $table->index('name');
            $table->index('group_id');
            $table->index('is_color');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index('name');
            $table->index('code');
            $table->index('parent_id');
            $table->index('slug');
            $table->index('order');
            $table->index('is_active');
            $table->index(['slug', 'is_active']);
        });

        Schema::table('page_groups', function (Blueprint $table) {
            $table->index('name');
            $table->index('column');
            $table->index('is_active');
            $table->index(['name', 'is_active']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->index('name');
            $table->index('slug');
            $table->index('is_active');
            $table->index('show_header');
            $table->index('group_id');
            $table->index(['slug', 'is_active']);
        });

        Schema::table('post_groups', function (Blueprint $table) {
            $table->index('name');
            $table->index('slug');
            $table->index('user_id');
            $table->index('is_active');
            $table->index('is_hot');
            $table->index(['slug', 'is_active']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->index('title');
            $table->index('slug');
            $table->index('author_id');
            $table->index('is_active');
            $table->index('is_new');
            $table->index('is_hot');
            $table->index('group_id');
            $table->index(['slug', 'is_active']);
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('product_code');
            $table->index('product_variant_id');
            $table->index('product_variant_code');
        });

        Schema::table('product_variant_mains', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('name');
            $table->index('code');
            $table->index('is_active');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('name');
            $table->index('code');
            $table->index('product_main_id');
            $table->index('is_active');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('code');
            $table->index('name');
            $table->index('category_id');
            $table->index('slug');
            $table->index('is_active');
            $table->index('is_hot');
            $table->index('is_upcoming');
            $table->index('is_new');
            $table->index(['slug', 'is_active']);
        });

        Schema::table('variants', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('attribute_id');
            $table->index('attribute_group_id');
            $table->index('is_hot');
            $table->index('is_main');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attribute_groups', function (Blueprint $table) {
            $table->dropIndex('attribute_groups_name_index');
            $table->dropIndex('attribute_groups_priority_index');
            $table->dropIndex('attribute_groups_is_color_index');
            $table->dropIndex('attribute_groups_is_main_index');
        });

        Schema::table('attributes', function (Blueprint $table) {
            $table->dropIndex('attributes_name_index');
            $table->dropIndex('attributes_group_id_index');
            $table->dropIndex('attributes_is_color_index');
        });


        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_name_index');
            $table->dropIndex('categories_code_index');
            $table->dropIndex('categories_parent_id_index');
            $table->dropIndex('categories_slug_index');
            $table->dropIndex('categories_order_index');
            $table->dropIndex('categories_is_active_index');
            $table->dropIndex('categories_slug_is_active_index');
        });

        Schema::table('page_groups', function (Blueprint $table) {
            $table->dropIndex('page_groups_name_index');
            $table->dropIndex('page_groups_column_index');
            $table->dropIndex('page_groups_is_active_index');
            $table->dropIndex('page_groups_name_is_active_index');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropIndex('pages_name_index');
            $table->dropIndex('pages_slug_index');
            $table->dropIndex('pages_is_active_index');
            $table->dropIndex('pages_show_header_index');
            $table->dropIndex('pages_group_id_index');
            $table->dropIndex('pages_slug_is_active_index');
        });

        Schema::table('post_groups', function (Blueprint $table) {
            $table->dropIndex('post_groups_name_index');
            $table->dropIndex('post_groups_slug_index');
            $table->dropIndex('post_groups_user_id_index');
            $table->dropIndex('post_groups_is_active_index');
            $table->dropIndex('post_groups_is_hot_index');
            $table->dropIndex('post_groups_slug_is_active_index');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex('posts_title_index');
            $table->dropIndex('posts_slug_index');
            $table->dropIndex('posts_author_id_index');
            $table->dropIndex('posts_is_active_index');
            $table->dropIndex('posts_is_new_index');
            $table->dropIndex('posts_is_hot_index');
            $table->dropIndex('posts_group_id_index');
            $table->dropIndex('posts_slug_is_active_index');
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropIndex('product_reviews_product_id_index');
            $table->dropIndex('product_reviews_product_code_index');
            $table->dropIndex('product_reviews_product_variant_id_index');
            $table->dropIndex('product_reviews_product_variant_code_index');
        });

        Schema::table('product_variant_mains', function (Blueprint $table) {
            $table->dropIndex('product_variant_mains_product_id_index');
            $table->dropIndex('product_variant_mains_name_index');
            $table->dropIndex('product_variant_mains_code_index');
            $table->dropIndex('product_variant_mains_is_active_index');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('product_variants_product_id_index');
            $table->dropIndex('product_variants_name_index');
            $table->dropIndex('product_variants_code_index');
            $table->dropIndex('product_variants_product_main_id_index');
            $table->dropIndex('product_variants_is_active_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_code_index');
            $table->dropIndex('products_name_index');
            $table->dropIndex('products_category_id_index');
            $table->dropIndex('products_slug_index');
            $table->dropIndex('products_is_active_index');
            $table->dropIndex('products_is_hot_index');
            $table->dropIndex('products_is_upcoming_index');
            $table->dropIndex('products_is_new_index');
            $table->dropIndex('products_slug_is_active_index');
        });

        Schema::table('variants', function (Blueprint $table) {
            $table->dropIndex('variants_product_id_index');
            $table->dropIndex('variants_attribute_id_index');
            $table->dropIndex('variants_attribute_group_id_index');
            $table->dropIndex('variants_is_hot_index');
            $table->dropIndex('variants_is_main_index');
        });
    }
};
