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
            $table->foreignId('status_id')->constrained('order_status', 'id');
            $table->foreignId('payment_id')->constrained('payments', 'id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('token')->nullable();

            $table->string('seller_by')->nullable();
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('recipient_email')->nullable();
            $table->string('recipient_city');
            $table->string('recipient_district');
            $table->string('recipient_ward');
            $table->string('recipient_detail');
            $table->string('recipient_note')->nullable();
            $table->string('shipping_by')->nullable();
            $table->integer('shipping_cost');
            $table->integer('order_discount')->default(0);
            $table->boolean('is_payment')->default(false);
            $table->boolean('is_process')->default(false);

            $table->rememberToken();
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
