<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('tukang_id');
            $table->unsignedBigInteger('service_id');
            $table->string('conversation_id');
            $table->text('service_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->json('service_details')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('tukang_id')->references('id')->on('tukangs')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');

            $table->index(['customer_id', 'status']);
            $table->index(['tukang_id', 'status']);
            $table->index(['service_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
