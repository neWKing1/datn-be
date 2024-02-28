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
        Schema::disableForeignKeyConstraints();

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments', 'id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('token')->nullable();

            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('recipient_email');
            $table->string('recipient_city');
            $table->string('recipient_district');
            $table->string('recipient_ward');
            $table->string('recipient_detail');
            $table->string('recipient_note');
            $table->string('shipping_by');
            $table->integer('shipping_cost');
            $table->enum('payment_status', ['Thanh toán khi nhận hàng', 'Chờ thanh toán', 'Đã thanh toán']);

            $table->timestamps();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
