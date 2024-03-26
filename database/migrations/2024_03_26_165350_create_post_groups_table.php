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
        Schema::create('post_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_key', 255)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_key', 255)->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_groups');
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['meta_title','meta_description','meta_key', 'group_id']);
        });
    }
};
