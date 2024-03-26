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
            $table->unsignedBigInteger('attribute_id')->nullable();
            $table->unique(['attribute_id', 'attribute_group_id', 'product_id'], 'product_attribute_index')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->dropColumn(['attribute_id', 'attribute_group_id']);
            $table->dropUnique('product_attribute_index');
        });
    }
};
