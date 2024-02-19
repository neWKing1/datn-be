<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promotion_variants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('variant_id')->unsigned();
            $table->bigInteger('promotion_id')->unsigned();
            $table->foreign('variant_id')->references('id')->on('variants');
            $table->foreign('promotion_id')->references('id')->on('promotions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_variants');
    }
};
