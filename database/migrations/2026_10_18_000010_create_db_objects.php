<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Indexes, trigger functions, triggers and views. The SQL lives in database/sql so it can be read and defended.
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/functions_triggers_views.sql')));
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            DROP VIEW IF EXISTS v_ticket_totals;
            DROP VIEW IF EXISTS v_low_stock_parts;
            DROP TRIGGER IF EXISTS trg_restore_part_stock ON ticket_parts;
            DROP TRIGGER IF EXISTS trg_deduct_part_stock ON ticket_parts;
            DROP FUNCTION IF EXISTS fn_restore_part_stock();
            DROP FUNCTION IF EXISTS fn_deduct_part_stock();
            DROP INDEX IF EXISTS idx_ticket_parts_part;
            DROP INDEX IF EXISTS idx_ticket_parts_ticket;
            DROP INDEX IF EXISTS idx_tickets_customer;
            DROP INDEX IF EXISTS idx_tickets_status_created;
            DROP INDEX IF EXISTS idx_parts_low_stock;
        SQL);
    }
};
