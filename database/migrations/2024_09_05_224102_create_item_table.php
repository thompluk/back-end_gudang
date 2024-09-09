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
        Schema::create('item', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->string('item_name')->nullable();
            $table->string('no_edp')->nullable();
            $table->string('no_sn')->nullable();
            $table->string('no_ppb')->nullable();
            $table->string('no_po')->nullable();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 15, 0)->nullable();
            $table->text('remarks')->nullable();
            $table->string('item_unit')->nullable();
            $table->boolean('is_in_stock')->nullable();
            $table->date('arrival_date')->nullable();
            $table->date('leaving_date')->nullable();
            $table->string('receiver')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->timestamps();

            $table->foreign('stock_id')->references('id')->on('stock_item')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item');
    }
};
