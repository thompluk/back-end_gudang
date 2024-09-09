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
        Schema::create('surat_jalan_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->unsignedBigInteger('stock_item_id')->nullable();
            $table->string('nama_barang')->nullable();
            $table->integer('quantity')->nullable();
            $table->boolean('is_dikembalikan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_detail');
    }
};
