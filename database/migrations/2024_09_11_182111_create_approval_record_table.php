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
        Schema::create('approval_record', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no')->nullable();
            $table->date('date')->nullable();
            $table->string('type')->nullable();
            $table->string('requestor')->nullable();
            $table->unsignedBigInteger('requestor_id')->nullable();
            $table->string('approver')->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->string('action')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_record');
    }
};
