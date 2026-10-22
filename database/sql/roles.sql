-- Run ONCE as a superuser (e.g. postgres) AFTER `php artisan migrate:fresh --seed`:
--   psql -U postgres -d repair_tracking -f database/sql/roles.sql
-- Three roles with different privileges: repair_app (data changes), repair_readonly (reports), repair_backup (pg_dump) (change the passwords before real use).

DO $$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'repair_app') THEN
        CREATE ROLE repair_app LOGIN PASSWORD 'app_pass_change_me';
    END IF;
    IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'repair_readonly') THEN
        CREATE ROLE repair_readonly LOGIN PASSWORD 'readonly_pass_change_me';
    END IF;
    IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'repair_backup') THEN
        CREATE ROLE repair_backup LOGIN PASSWORD 'backup_pass_change_me';
    END IF;
END
$$;

-- Start clean so the script can be re-run
REVOKE ALL ON ALL TABLES IN SCHEMA public FROM repair_app, repair_readonly, repair_backup;
REVOKE ALL ON ALL SEQUENCES IN SCHEMA public FROM repair_app, repair_readonly, repair_backup;

GRANT CONNECT ON DATABASE repair_tracking TO repair_app, repair_readonly, repair_backup;
GRANT USAGE ON SCHEMA public TO repair_app, repair_readonly, repair_backup;

-- repair_app: what the Laravel app uses day to day (data changes only, no DDL)
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO repair_app;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO repair_app;
-- Application code must never change the migration history
REVOKE INSERT, UPDATE, DELETE ON migrations FROM repair_app;

-- repair_readonly: reports and defense demos (SELECT only)
GRANT SELECT ON ALL TABLES IN SCHEMA public TO repair_readonly;
-- Readonly must not read password hashes
REVOKE SELECT ON users FROM repair_readonly;
GRANT SELECT (id, name, email, role, created_at) ON users TO repair_readonly;

-- repair_backup: read access to every table (incl. users) so pg_dump works; cannot write anything
GRANT pg_read_all_data TO repair_backup;

-- ---------------------------------------------------------------------------
-- Row-level security (policies for repair_app are in database/sql/rls.sql)
-- ---------------------------------------------------------------------------
-- repair_backup must see every row, otherwise pg_dump refuses to run on tables with RLS.
ALTER ROLE repair_backup BYPASSRLS;

-- repair_readonly (Reports) gets its own SELECT-only policy on each RLS table.
DO $$
DECLARE t text;
BEGIN
    FOREACH t IN ARRAY ARRAY['tickets', 'ticket_status_histories', 'ticket_parts', 'parts', 'low_stock_alerts'] LOOP
        EXECUTE format('DROP POLICY IF EXISTS readonly_select ON %I', t);
        EXECUTE format('CREATE POLICY readonly_select ON %I FOR SELECT TO repair_readonly USING (true)', t);
    END LOOP;
END
$$;
