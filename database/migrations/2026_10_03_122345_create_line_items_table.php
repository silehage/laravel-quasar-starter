<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('line_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('product_line_id');
            $table->foreignUlid('purchase_order_item_id')->constrained('purchase_order_items')->cascadeOnDelete();
            $table->foreignUlid('workflow_id')->nullable();
            $table->string('barcode')->index();
            $table->string('status')->default('pending');
            $table->string('workflow_status')->default('pending');
            $table->datetime('etd_finished_at')->nullable();
            $table->datetime('finished_at')->nullable();
            $table->integer('production_cost')->default(0);
            $table->integer('material_cost')->default(0);
            $table->bigInteger('sort')->unsigned()->default(0)->index();
            $table->boolean('is_revision')->default(false);

            $table->index(['purchase_order_item_id', 'status']);
        });

        DB::unprepared('
            CREATE TRIGGER t_line_items_sort_bi
            BEFORE INSERT ON line_items
            FOR EACH ROW
            BEGIN
                SET NEW.sort = (SELECT COALESCE(MAX(sort), 0) + 1 FROM line_items);
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS t_line_items_sort_bi');
        Schema::dropIfExists('line_items');
    }
};
