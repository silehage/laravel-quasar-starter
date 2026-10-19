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
        Schema::create('product_lines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('product_id')->index();
            $table->string('sku_number')->index();
            $table->string('name_override')->nullable();
            $table->string('variant_override')->nullable();
            $table->integer('base_price')->default(0);
            $table->integer('retail_price')->default(0);
            $table->integer('weight')->default(0);
            $table->boolean('is_mp_product')->default(false);
            $table->boolean('is_unl_stock')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_lines');
    }
};
