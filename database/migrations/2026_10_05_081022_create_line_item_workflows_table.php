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
        Schema::create('line_item_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('line_item_id')->constrained('line_items')->cascadeOnDelete();
            $table->foreignUlid('workflow_id');
            $table->foreignUlid('worker_id')->nullable(); 
            $table->string('desription')->nullable();
            $table->integer('cost');
            $table->timestamp('start_at')->nullable();
            $table->timestamp('finished_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_item_workflows');
    }
};
