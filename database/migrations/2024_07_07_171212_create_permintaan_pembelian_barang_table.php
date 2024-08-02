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
        if (!Schema::hasTable('permintaan_pembelian_barang')) {
            Schema::create('permintaan_pembelian_barang', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('no_ppb');
                $table->date('tanggal');
                $table->string('status');
                $table->string('pemohon');
                $table->string('pemohon_id');
                $table->string('mengetahui')->nullable();
                $table->string('mengetahui_id')->nullable();
                $table->string('mengetahui_status')->nullable();
                $table->string('menyetujui')->nullable();
                $table->string('menyetujui_id')->nullable();
                $table->string('menyetujui_status')->nullable();
                $table->string('purchasing')->nullable();
                $table->string('purchasing_id')->nullable();
                $table->string('purchasing_status')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_pembelian_barang');
    }
};

