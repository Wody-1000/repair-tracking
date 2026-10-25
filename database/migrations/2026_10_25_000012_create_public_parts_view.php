<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// View for the public "Available parts" page. The SQL lives in database/sql so it can be read and defended.
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/public_parts_view.sql')));
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS v_public_parts');
    }
};
