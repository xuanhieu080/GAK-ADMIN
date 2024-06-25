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
        Schema::create('product_variant_mains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('name', 355)->nullable();
            $table->string('code')->nullable();
            $table->string('params',355)->nullable();
            $table->string('option_name')->nullable();
            $table->json('option_all')->nullable();
            $table->json('option_group')->nullable();
            $table->json('options')->nullable();
            $table->text('description')->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_key', 255)->nullable();
            $table->boolean('is_active')->nullable()->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_mains');
    }
};
