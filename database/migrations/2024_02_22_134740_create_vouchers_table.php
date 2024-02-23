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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->integer('value');
            $table->bigInteger('min_bill_value');
            $table->enum('status', ['finished', 'upcoming', 'happening']);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->bigInteger('quantity');
            $table->enum('type', ['public', 'private']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
