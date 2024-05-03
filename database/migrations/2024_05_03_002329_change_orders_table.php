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
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_details');
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();
            $table->string('status', 255)->default('PENDING');
            $table->timestamp('date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('user_phone')->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_phone',20)->nullable();
            $table->string('customer_email')->nullable();
            $table->text('note')->nullable();
            $table->string('address',355)->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->double('total')->default(0)->nullable();
            $table->double('discount')->default(0)->nullable();
            $table->double('shipping_cost')->default(0)->nullable()->comment('Phí vận chuyển');
            $table->double('savings')->default(0)->nullable()->comment('Giảm giá theo từng sản phẩm');
            $table->string('payment_method')->default('COD')->nullable()->comment('Hình thức thanh toán');
            $table->timestamps();
        });
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_code')->nullable();
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->string('product_variant_name')->nullable();
            $table->string('product_variant_code')->nullable();
            $table->string('option_name')->nullable();
            $table->text('note')->nullable();
            $table->integer('qty')->default(0);
            $table->double('price',15)->default(0);
            $table->double('cost',15)->default(0)->comment('Giá gốc');
            $table->double('total',15)->default(0);
            $table->double('discount',15)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
