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
        Schema::create('stock_item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stock_name')->nullable();
            $table->string('tipe')->nullable();
            $table->string('prinsipal')->nullable();
            $table->unsignedBigInteger('prinsipal_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_item');
    }
};
