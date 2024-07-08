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
            $table->bigInteger('id', true);
            $table->string('nama_barang');
            $table->string('kode');
            $table->string('spesifikasi');
            $table->integer('quantity');
            $table->date('expected_eta');
            $table->text('project_and_customer');
            $table->timestamps();
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
