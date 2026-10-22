<?php

namespace App\Http\Controllers;

use App\Models\RepairStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;

// Admin only. Shows the Database Administration work live from PostgreSQL itself:
// connection settings, roles and privileges, keys and constraints, indexes with EXPLAIN,
// row-level security, function and triggers, and backup / restore proof.
class DatabaseAdminController extends Controller
{
    private const LIMITED_ROLES = "'repair_app', 'repair_readonly', 'repair_backup'";

    public function index()
    {
        $db = DB::connection();

        $connections = [];
        foreach (['pgsql', 'pgsql_readonly'] as $name) {
            $c = config("database.connections.$name");
            $connections[] = ['name' => $name, 'host' => $c['host'], 'port' => $c['port'], 'database' => $c['database'], 'username' => $c['username']];
        }

        $current = $db->selectOne("SELECT current_user AS usr, current_database() AS db, split_part(version(), ' on ', 1) AS ver");
        $context = $db->selectOne('SELECT app_role() AS role, app_user_id() AS uid');

        // Owner, superuser and BYPASSRLS roles skip row-level security
        $bypass = (bool) $db->selectOne(
            "SELECT (r.rolsuper OR r.rolbypassrls OR EXISTS (
                SELECT 1 FROM pg_tables t WHERE t.schemaname = 'public' AND t.tablename = 'tickets' AND t.tableowner = current_user
             )) AS bypass FROM pg_roles r WHERE r.rolname = current_user"
        )->bypass;

        $roles = $db->select(
            "SELECT rolname, rolcanlogin, rolsuper, rolbypassrls FROM pg_roles
             WHERE rolname IN ('postgres', ".self::LIMITED_ROLES.') ORDER BY rolname'
        );

        $privileges = [];
        $privRoles = [];
        $rows = $db->select(
            "SELECT c.relname AS tbl, r.rolname AS role,
                    has_table_privilege(r.oid, c.oid, 'SELECT') AS s, has_table_privilege(r.oid, c.oid, 'INSERT') AS i,
                    has_table_privilege(r.oid, c.oid, 'UPDATE') AS u, has_table_privilege(r.oid, c.oid, 'DELETE') AS d
             FROM pg_class c JOIN pg_namespace n ON n.oid = c.relnamespace CROSS JOIN pg_roles r
             WHERE n.nspname = 'public' AND c.relkind = 'r' AND r.rolname IN (".self::LIMITED_ROLES.')
             ORDER BY c.relname, r.rolname'
        );
        foreach ($rows as $r) {
            $privileges[$r->tbl][$r->role] = ($r->s ? 'S' : '-').($r->i ? 'I' : '-').($r->u ? 'U' : '-').($r->d ? 'D' : '-');
            $privRoles[$r->role] = true;
        }
        $privRoles = array_keys($privRoles);

        $passwordAccess = $db->select(
            "SELECT r.rolname, has_column_privilege(r.oid, 'public.users', 'password', 'SELECT') AS can_read
             FROM pg_roles r WHERE r.rolname IN (".self::LIMITED_ROLES.') ORDER BY r.rolname'
        );

        $tables = $db->select(
            "SELECT table_name, string_agg(column_name || ' ' || data_type, ', ' ORDER BY ordinal_position) AS cols
             FROM information_schema.columns WHERE table_schema = 'public' AND table_name <> 'migrations'
             GROUP BY table_name ORDER BY table_name"
        );

        $constraints = $db->select(
            "SELECT c.conrelid::regclass::text AS tbl, c.conname,
                    CASE c.contype WHEN 'p' THEN 'PRIMARY KEY' WHEN 'f' THEN 'FOREIGN KEY' WHEN 'u' THEN 'UNIQUE' WHEN 'c' THEN 'CHECK' END AS type,
                    pg_get_constraintdef(c.oid) AS def
             FROM pg_constraint c JOIN pg_namespace n ON n.oid = c.connamespace
             WHERE n.nspname = 'public' AND c.contype IN ('p', 'f', 'u', 'c') AND c.conrelid::regclass::text <> 'migrations'
             ORDER BY 1, 3, 2"
        );

        $indexes = $db->select(
            "SELECT tablename, indexname, indexdef FROM pg_indexes
             WHERE schemaname = 'public' AND tablename <> 'migrations' ORDER BY tablename, indexname"
        );

        $rlsTables = $db->select(
            "SELECT c.relname AS tbl, c.relrowsecurity AS enabled FROM pg_class c
             WHERE c.relnamespace = 'public'::regnamespace AND c.relkind = 'r' AND c.relname <> 'migrations' ORDER BY c.relname"
        );
        $policies = $db->select(
            "SELECT tablename, policyname, cmd, roles, qual, with_check FROM pg_policies
             WHERE schemaname = 'public' ORDER BY tablename, policyname"
        );

        $triggers = $db->select(
            "SELECT c.relname AS tbl, t.tgname AS name, pg_get_triggerdef(t.oid) AS def
             FROM pg_trigger t JOIN pg_class c ON c.oid = t.tgrelid
             WHERE NOT t.tgisinternal AND c.relnamespace = 'public'::regnamespace ORDER BY c.relname, t.tgname"
        );
        $functions = $db->select(
            "SELECT p.proname AS name, pg_get_function_identity_arguments(p.oid) AS args, p.prosecdef AS definer
             FROM pg_proc p WHERE p.pronamespace = 'public'::regnamespace ORDER BY p.proname"
        );
        $views = $db->select("SELECT viewname FROM pg_views WHERE schemaname = 'public' ORDER BY viewname");

        $backups = collect(glob(base_path('backups/*.dump')) ?: [])
            ->map(fn ($f) => ['name' => basename($f), 'size' => number_format(filesize($f) / 1024, 1).' KB', 'time' => date('Y-m-d H:i', filemtime($f))])
            ->sortByDesc('name')->values();
        $restoreLog = is_file(base_path('backups/restore-verification.log')) ? file_get_contents(base_path('backups/restore-verification.log')) : null;

        return view('admin.database', [
            'connections' => $connections, 'current' => $current, 'context' => $context, 'bypass' => $bypass,
            'roles' => $roles, 'privileges' => $privileges, 'privRoles' => $privRoles, 'passwordAccess' => $passwordAccess,
            'tables' => $tables, 'constraints' => $constraints, 'indexes' => $indexes,
            'explains' => $this->explainDemo($db),
            'rlsTables' => $rlsTables, 'policies' => $policies, 'rlsDemo' => $this->rlsDemo($db),
            'triggers' => $triggers, 'functions' => $functions, 'views' => $views,
            'backups' => $backups, 'restoreLog' => $restoreLog,
        ]);
    }

    /** Live EXPLAIN ANALYZE of the app's main queries. Sequential scans are switched off only inside a rolled-back transaction, so tiny demo tables still show their index. */
    private function explainDemo(ConnectionInterface $db): array
    {
        $pdo = $db->getPdo();
        $ticket = Ticket::orderBy('id')->first();
        $status = RepairStatus::orderBy('id')->first();

        $queries = [
            ['Status page lookup by ticket number', 'tickets_ticket_number_key (unique)',
                'SELECT id, status_id FROM tickets WHERE ticket_number = '.$pdo->quote($ticket?->ticket_number ?? 'RT-0000-0000')],
            ['Ticket list by status, newest first', 'idx_tickets_status_created',
                'SELECT id, ticket_number FROM tickets WHERE status_id = '.(int) ($status?->id ?? 0).' ORDER BY created_at DESC LIMIT 20'],
            ['Low-stock check', 'idx_parts_low_stock (partial index)',
                'SELECT id, sku, quantity FROM parts WHERE quantity <= reorder_level'],
            ['Parts used on one ticket', 'idx_ticket_parts_ticket',
                'SELECT part_id, quantity FROM ticket_parts WHERE ticket_id = '.(int) ($ticket?->id ?? 0)],
        ];

        $out = [];
        $db->beginTransaction();
        try {
            $db->statement('SET LOCAL enable_seqscan = off');
            foreach ($queries as [$title, $index, $sql]) {
                $plan = array_map(fn ($r) => $r->{'QUERY PLAN'}, $db->select('EXPLAIN ANALYZE '.$sql));
                $out[] = ['title' => $title, 'index' => $index, 'sql' => $sql, 'plan' => implode("\n", $plan)];
            }
        } finally {
            $db->rollBack();
        }

        return $out;
    }

    /** Live proof of row-level security: what each caller context can see and change. Everything is rolled back. */
    private function rlsDemo(ConnectionInterface $db): array
    {
        $tech = User::where('role', User::ROLE_TECHNICIAN)->orderBy('id')->first();
        $contexts = [
            ['Nobody identified', '', ''],
            ['Customer (public)', 'public', ''],
            ['Front desk', 'front_desk', ''],
            ['Technician'.($tech ? ' ('.$tech->name.')' : ''), 'technician', (string) ($tech?->id ?? '')],
            ['Admin', 'admin', ''],
        ];

        $rows = [];
        foreach ($contexts as [$label, $role, $uid]) {
            $db->beginTransaction();
            try {
                $db->select("SELECT set_config('app.role', ?, true), set_config('app.user_id', ?, true)", [$role, $uid]);
                $rows[] = [
                    'label' => $label,
                    'tickets_seen' => $db->selectOne('SELECT count(*) AS c FROM tickets')->c,
                    'parts_seen' => $db->selectOne('SELECT count(*) AS c FROM parts')->c,
                    'tickets_editable' => $db->update('UPDATE tickets SET labor_cost = labor_cost'),
                    'parts_editable' => $db->update('UPDATE parts SET quantity = quantity'),
                ];
            } finally {
                $db->rollBack();
            }
        }

        return $rows;
    }
}
