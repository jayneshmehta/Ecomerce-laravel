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
                $table->string('orderId')->unique();
                $table->string('orderGroupId');
                $table->unsignedBigInteger('productId');
                $table->unsignedBigInteger('userId');
                $table->string('quantity');
                $table->string('ShippingAddress');
                $table->string('contactNo');
                $table->string('TotalAmount');
                $table->enum('shippingType',['Primary','Express','Normal'])->default('Normal');
                $table->enum('paymentType',['upi','card','cod'])->default('cod');
                $table->enum('status', ['In process', 'Pending', 'Ready for dispatch', 'Dispatched','Out for Delivery','Delivered'])->default('In process');
                $table->foreign('userId')->references("id")->on("users");
                $table->foreign('productId')->references("id")->on("products");
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
