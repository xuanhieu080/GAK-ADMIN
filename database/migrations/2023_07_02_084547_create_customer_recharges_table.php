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
        Schema::create('customer_recharges', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->double('amount')->default(0);
            $table->string('description');
            $table->string('status')->default('SUCCESS')->comment('FAILED, SUCCESS');
            $table->unsignedBigInteger('bank_id');
            $table->timestamp('date')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_recharges');
    }
};
