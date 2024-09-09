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
        Schema::create('stock_material', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stock_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('no_ppb')->nullable();
            $table->string('no_po')->nullable();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 15, 0)->nullable();
            $table->text('remarks')->nullable();
            $table->string('item_unit')->nullable();
            $table->date('arrival_date')->nullable();
            $table->string('receiver')->nullable();
            $table->unsignedBigInteger('receiver_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_material');
    }
};
