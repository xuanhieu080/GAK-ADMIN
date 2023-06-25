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
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->nullable();
            $table->string('image')->nullable();
            $table->string('value')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_file')->comment('0: string, 1: image')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs');
    }
};
