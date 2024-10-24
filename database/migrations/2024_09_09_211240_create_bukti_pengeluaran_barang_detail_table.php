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
        Schema::create('bukti_pengeluaran_barang_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('bpb_id')->nullable();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->string('stock_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->text('notes')->nullable();

            $table->foreign('bpb_id')->references('id')->on('bukti_pengeluaran_barang')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_pengeluaran_barang_detail');
    }
};
