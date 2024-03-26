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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('title',350)->nullable();
            $table->string('code', 20)->nullable();
            $table->string('status', 255)->default('PENDING');
            $table->timestamp('date')->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('customer_phone',15)->nullable();
            $table->text('note')->nullable();
            $table->text('reason_rejected')->nullable()->comment('Lý do trả lại');
            $table->integer('qty')->default(0);
            $table->double('price',15)->default(0);
            $table->double('total',15)->default(0);
            $table->double('discount',15)->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
