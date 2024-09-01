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
        Schema::create('purchase_order_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('po_id')->nullable();
            $table->string('item')->nullable();
            $table->string('no_ppb')->nullable();
            $table->unsignedBigInteger('ppb_detail_id')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_price', 15, 0)->nullable();
            $table->integer('discount')->nullable();
            $table->decimal('amount', 15, 0)->nullable();
            $table->text('remarks')->nullable();
            $table->string('item_unit')->nullable();
            $table->timestamps();

            $table->foreign('po_id')->references('id')->on('purchase_order')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_detail');
    }
};
