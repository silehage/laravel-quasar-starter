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
        Schema::create('product_workflow_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('product_id');
            $table->foreignUlid('workflow_id');
            $table->integer('custom_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_workflow_costs');
    }
};
