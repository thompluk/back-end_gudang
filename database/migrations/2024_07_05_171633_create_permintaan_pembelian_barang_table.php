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
        Schema::create('permintaan_pembelian_barang', function (Blueprint $table) {
            $table->string('no_pbb')->primary();
            $table->date('tanggal');
            $table->string('pemohon');
            $table->string('mengetahui');
            $table->string('menyetujui');
            $table->string('purchasing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_pembelian_barang');
    }
};
