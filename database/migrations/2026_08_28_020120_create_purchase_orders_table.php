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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('reference')->index();
            $table->string('status')->default('pending');
            $table->string('description')->nullable();
            $table->foreignUlid('fulfillment_id');
            $table->foreignUlid('manufacturer_id');
            $table->foreignUlid('factory_id');
            $table->foreignUlid('procurement_id');
            $table->datetime('etd_finished_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
