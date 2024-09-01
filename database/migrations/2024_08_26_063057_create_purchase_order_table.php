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
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('tanggal');
            $table->string('no_po');
            $table->string('status');
            $table->string('vendor')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('ship_to')->nullable();
            $table->unsignedBigInteger('ship_to_id')->nullable();
            $table->string('terms')->nullable();
            $table->string('ship_via')->nullable();
            $table->date('expected_date')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('sub_total', 15, 0)->nullable();
            $table->decimal('discount', 15, 0)->nullable();
            $table->decimal('freight_cost', 15, 0)->nullable();
            $table->decimal('ppn', 15, 0)->nullable()->nullable();
            $table->decimal('total_order', 15, 0)->nullable();
            $table->string('say')->nullable();
            $table->text('description')->nullable();
            $table->string('prepared_by')->nullable();
            $table->unsignedBigInteger('prepared_by_id')->nullable();
            $table->date('prepared_by_date')->nullable();
            $table->string('verified_by')->nullable();
            $table->unsignedBigInteger('verified_by_id')->nullable();
            $table->date('verified_by_date')->nullable();
            $table->string('verified_by_status')->nullable();
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
        Schema::dropIfExists('purchase_order');
    }
};
