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
        Schema::create('bukti_pengeluaran_barang', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status')->nullable();
            $table->string('salesman')->nullable();
            $table->date('date')->nullable();
            $table->string('no_po')->nullable();
            $table->string('delivery_by')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('no_bpb')->nullable();
            $table->string('customer')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_pic_name')->nullable();
            $table->string('customer_pic_phone')->nullable();
            $table->string('request_by')->nullable();
            $table->unsignedBigInteger('request_by_id')->nullable();
            $table->date('request_by_date')->nullable();
            $table->string('approved_by')->nullable();
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->date('approved_by_date')->nullable();
            $table->string('approved_by_status')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukti_pengeluaran_barang');
    }
};
