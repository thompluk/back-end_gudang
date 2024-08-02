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
        Schema::create('permintaan_pembelian_barang_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ppb_id')->nullable();
            $table->string('nama_barang')->nullable();
            $table->string('kode')->nullable();
            $table->string('spesifikasi')->nullable();
            $table->integer('quantity')->nullable();
            $table->date('expected_eta')->nullable();
            $table->text('project_and_customer')->nullable();
            $table->timestamps();

            $table->foreign('ppb_id')->references('id')->on('permintaan_pembelian_barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_pembelian_barang_detail');
    }
};


