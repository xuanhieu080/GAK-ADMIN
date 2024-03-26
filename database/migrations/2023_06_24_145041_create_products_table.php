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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->text('image')->nullable();
            $table->text('description')->nullable();
            $table->double('price', 15)->default(0);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->integer('qty')->default(100);
            $table->integer('qty_sold')->default(0);
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
