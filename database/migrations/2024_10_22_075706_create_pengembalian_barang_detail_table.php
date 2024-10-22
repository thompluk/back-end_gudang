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
        Schema::create('pengembalian_barang_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pengembalian_barang_id')->nullable();
            $table->unsignedBigInteger('surat_jalan_detail_id')->nullable();
            $table->unsignedBigInteger('stock_material_id')->nullable();
            $table->string('nama_barang')->nullable();
            $table->integer('quantity_dikirim')->nullable();
            $table->integer('quantity_dikembalikan')->nullable();
            $table->text('keterangan')->nullable();

            $table->foreign('pengembalian_barang_id')->references('id')->on('pengembalian_barang')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_barang_detail');
    }
};
