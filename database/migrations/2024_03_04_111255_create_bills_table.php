<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('note')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->integer('total_money')->nullable();
            $table->string('money_reduce')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('money_ship')->default(0);
            $table->enum('timeline', [0, 1, 2, 3, 4, 5, 6, 7])->default(0);
            $table->enum('type', ['delivery', 'at the counter'])->default('at the counter');
            $table->enum('payment_method', ['cash', 'card'])->default('cash');;
            $table->enum('status', ['active', 'no-active'])->default('no-active');
            $table->bigInteger('voucher_id')->unsigned()->nullable();
            $table->bigInteger('customer_id')->unsigned()->nullable();
            $table->foreign('voucher_id')->references('id')->on('vouchers');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
