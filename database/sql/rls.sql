-- Row-level security (RLS), aligned with Laravel authentication.
-- Run by migration 2026_10_18_000011_enable_row_level_security.php (kept here so it can be read and defended).
--
-- How it lines up with Laravel:
--   The app connects with ONE database login (repair_app). On every request the middleware
--   App\Http\Middleware\SetDatabaseContext tells PostgreSQL who is calling:
--       set_config('app.role',    <admin | technician | front_desk | public>)
--       set_config('app.user_id', <users.id or empty>)
--   The policies below read those two settings, so the database enforces the same rules as
--   the EnsureRole middleware, even if a controller has a bug.
--   If the app does not say who is calling (no setting), every policy fails and no rows are visible.
--
-- The table owner (the account that runs migrations) bypasses RLS, so migrate:fresh --seed still works.
-- The backup role has BYPASSRLS and the read-only role has its own SELECT policies (both in roles.sql).

-- Helpers: who is calling?
CREATE OR REPLACE FUNCTION app_role() RETURNS text
LANGUAGE sql STABLE AS $$ SELECT NULLIF(current_setting('app.role', true), '') $$;

CREATE OR REPLACE FUNCTION app_user_id() RETURNS bigint
LANGUAGE sql STABLE AS $$ SELECT NULLIF(current_setting('app.user_id', true), '')::bigint $$;

-- ---------------------------------------------------------------- tickets
ALTER TABLE tickets ENABLE ROW LEVEL SECURITY;

-- Everyone may read (customers use the public status page, staff use the app)
CREATE POLICY tickets_select ON tickets FOR SELECT
    USING (app_role() IN ('admin', 'technician', 'front_desk', 'public'));

-- Intake: admin and front desk
CREATE POLICY tickets_insert ON tickets FOR INSERT
    WITH CHECK (app_role() IN ('admin', 'front_desk'));

CREATE POLICY tickets_update_staff ON tickets FOR UPDATE
    USING (app_role() IN ('admin', 'front_desk'))
    WITH CHECK (app_role() IN ('admin', 'front_desk'));

-- Technicians can only work on tickets that are unassigned or assigned to themselves
CREATE POLICY tickets_update_technician ON tickets FOR UPDATE
    USING (app_role() = 'technician' AND (assigned_to IS NULL OR assigned_to = app_user_id()))
    WITH CHECK (app_role() = 'technician');

CREATE POLICY tickets_delete ON tickets FOR DELETE
    USING (app_role() = 'admin');

-- ------------------------------------------------ ticket_status_histories
ALTER TABLE ticket_status_histories ENABLE ROW LEVEL SECURITY;

CREATE POLICY histories_select ON ticket_status_histories FOR SELECT
    USING (app_role() IN ('admin', 'technician', 'front_desk', 'public'));

CREATE POLICY histories_insert ON ticket_status_histories FOR INSERT
    WITH CHECK (app_role() IN ('admin', 'technician', 'front_desk'));

-- ------------------------------------------------------------ ticket_parts
ALTER TABLE ticket_parts ENABLE ROW LEVEL SECURITY;

CREATE POLICY ticket_parts_select ON ticket_parts FOR SELECT
    USING (app_role() IN ('admin', 'technician', 'front_desk'));

CREATE POLICY ticket_parts_insert ON ticket_parts FOR INSERT
    WITH CHECK (
        app_role() = 'admin'
        OR (app_role() = 'technician' AND EXISTS (
            SELECT 1 FROM tickets t
            WHERE t.id = ticket_parts.ticket_id AND (t.assigned_to IS NULL OR t.assigned_to = app_user_id())))
    );

CREATE POLICY ticket_parts_delete ON ticket_parts FOR DELETE
    USING (
        app_role() = 'admin'
        OR (app_role() = 'technician' AND EXISTS (
            SELECT 1 FROM tickets t
            WHERE t.id = ticket_parts.ticket_id AND (t.assigned_to IS NULL OR t.assigned_to = app_user_id())))
    );

-- ------------------------------------------------------------------- parts
-- Everyone on staff can look. Only admin edits the catalogue by hand.
-- Stock quantity moves through the SECURITY DEFINER trigger, so technicians can use parts without write access here.
ALTER TABLE parts ENABLE ROW LEVEL SECURITY;

CREATE POLICY parts_select ON parts FOR SELECT
    USING (app_role() IN ('admin', 'technician', 'front_desk'));
CREATE POLICY parts_insert ON parts FOR INSERT WITH CHECK (app_role() = 'admin');
CREATE POLICY parts_update ON parts FOR UPDATE USING (app_role() = 'admin') WITH CHECK (app_role() = 'admin');
CREATE POLICY parts_delete ON parts FOR DELETE USING (app_role() = 'admin');

-- --------------------------------------------------------- low_stock_alerts
-- Rows are created and closed by the trigger functions. An admin can also close them from the Parts page.
ALTER TABLE low_stock_alerts ENABLE ROW LEVEL SECURITY;

CREATE POLICY alerts_select ON low_stock_alerts FOR SELECT
    USING (app_role() IN ('admin', 'technician', 'front_desk'));
CREATE POLICY alerts_update ON low_stock_alerts FOR UPDATE
    USING (app_role() = 'admin') WITH CHECK (app_role() = 'admin');
