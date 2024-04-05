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
        Schema::create('page_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('column')->default(1)->nullable();
            $table->boolean('is_active')->default(1)->nullable();
            $table->timestamps();
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->string('title', 350)->nullable();
            $table->text('description_short')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_groups');
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['group_id','title', 'description_short']);
        });
    }
};
