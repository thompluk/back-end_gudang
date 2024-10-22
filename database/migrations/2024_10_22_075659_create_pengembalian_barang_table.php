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
        Schema::create('pengembalian_barang', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status')->nullable();
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->string('no_surat_jalan')->nullable();
            $table->string('penerima')->nullable();
            $table->unsignedBigInteger('penerima_id')->nullable();
            $table->date('penerima_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian_barang');
    }
};
