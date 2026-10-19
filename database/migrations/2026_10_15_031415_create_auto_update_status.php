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
        DB::unprepared('
            CREATE TRIGGER t_line_items_status_au
            AFTER UPDATE ON line_items
            FOR EACH ROW
            BEGIN
                -- 1. Deklarasi variabel untuk menampung hasil pengecekan
                DECLARE distinct_status_count INT;
                DECLARE new_po_status VARCHAR(50);

                -- Hanya jalankan logika jika kolom status di line_items benar-benar berubah
                IF OLD.status <> NEW.status THEN

                    -- Hitung ada berapa variasi status unik di PO ini
                    SELECT COUNT(DISTINCT status) INTO distinct_status_count 
                    FROM line_items 
                    WHERE purchase_order_item_id = NEW.purchase_order_item_id;

                    -- 2. Logika Penentuan Status PO
                    IF distinct_status_count = 1 THEN
                        -- Jika status sama semua, ambil status tersebut (misal: "completed", "pending")
                        SELECT DISTINCT status INTO new_po_status 
                        FROM line_items 
                        WHERE purchase_order_item_id = NEW.purchase_order_item_id 
                        LIMIT 1;
                    ELSE
                        -- Jika statusnya berbeda-beda, set menjadi "mixed"
                        SET new_po_status = "mixed";
                    END IF;

                    -- 3. Update status di tabel purchase_order_items
                    UPDATE purchase_order_items
                    SET status = new_po_status, updated_at = NOW()
                    WHERE id = NEW.purchase_order_item_id;

                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER t_purchase_order_items_status_au
            AFTER UPDATE ON purchase_order_items
            FOR EACH ROW
            BEGIN
                -- 1. Deklarasi variabel untuk menampung hasil pengecekan
                DECLARE distinct_status_count INT;
                DECLARE new_po_status VARCHAR(50);

                -- Hanya jalankan logika jika kolom status di line_items benar-benar berubah
                IF OLD.status <> NEW.status THEN

                    -- Hitung ada berapa variasi status unik di PO ini
                    SELECT COUNT(DISTINCT status) INTO distinct_status_count 
                    FROM purchase_order_items 
                    WHERE purchase_order_id = NEW.purchase_order_id;

                    -- 2. Logika Penentuan Status PO
                    IF distinct_status_count = 1 THEN
                        -- Jika status sama semua, ambil status tersebut (misal: "completed", "pending")
                        SELECT DISTINCT status INTO new_po_status 
                        FROM purchase_order_items 
                        WHERE purchase_order_id = NEW.purchase_order_id 
                        LIMIT 1;
                    ELSE
                        -- Jika statusnya berbeda-beda, set menjadi "mixed"
                        SET new_po_status = "mixed";
                    END IF;

                    -- 3. Update status di tabel purchase_orders
                    UPDATE purchase_orders
                    SET status = new_po_status, updated_at = NOW()
                    WHERE id = NEW.purchase_order_id;

                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS t_line_items_status_au');
        DB::unprepared('DROP TRIGGER IF EXISTS t_purchase_order_items_status_au');
    }
};
