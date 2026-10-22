<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Row-level security policies. The SQL lives in database/sql/rls.sql so it can be read and defended.
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(file_get_contents(database_path('sql/rls.sql')));
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            DO $$
            DECLARE r record;
            BEGIN
                FOR r IN SELECT schemaname, tablename, policyname FROM pg_policies WHERE schemaname = 'public' LOOP
                    EXECUTE format('DROP POLICY %I ON %I.%I', r.policyname, r.schemaname, r.tablename);
                END LOOP;
            END
            $$;
            ALTER TABLE tickets DISABLE ROW LEVEL SECURITY;
            ALTER TABLE ticket_status_histories DISABLE ROW LEVEL SECURITY;
            ALTER TABLE ticket_parts DISABLE ROW LEVEL SECURITY;
            ALTER TABLE parts DISABLE ROW LEVEL SECURITY;
            ALTER TABLE low_stock_alerts DISABLE ROW LEVEL SECURITY;
            DROP FUNCTION IF EXISTS app_user_id();
            DROP FUNCTION IF EXISTS app_role();
        SQL);
    }
};
