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
        Schema::create('surat_jalan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no_surat_jalan')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('company')->nullable();
            $table->unsignedBigInteger('menyerahkan_id')->nullable();
            $table->string('menyerahkan')->nullable();
            $table->date('menyerahkan_date')->nullable();
            $table->unsignedBigInteger('mengetahui_id')->nullable();
            $table->string('mengetahui')->nullable();
            $table->string('mengetahui_status')->nullable();
            $table->date('mengetahui_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan');
    }
};
