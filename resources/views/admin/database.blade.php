@extends('layouts.app')
@section('title', 'Database')
@section('content')
<style>
    pre.code { background:#0f1521; color:#d6e2ff; padding:12px 14px; border-radius:8px; overflow-x:auto; font:12.5px/1.5 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; margin:8px 0 0; white-space:pre; }
    code.k { background:#eef1f7; padding:1px 6px; border-radius:5px; font:12.5px ui-monospace, Menlo, Consolas, monospace; word-break:break-word; }
    .small td, .small th { font-size:13px; }
    .yes { color:var(--ok); font-weight:600; } .no { color:var(--bad); font-weight:600; }
    .toc a { margin-right:14px; color:var(--brand); text-decoration:none; }
</style>

<h1>Database administration</h1>
<p class="muted">Everything on this page is read live from PostgreSQL. Admin only.</p>
<p class="toc">
    <a href="#connection">Connection</a><a href="#roles">Roles</a><a href="#design">Design</a><a href="#indexes">Indexes</a><a href="#rls">Row-level security</a><a href="#trigger">Function &amp; triggers</a><a href="#backup">Backup &amp; restore</a>
</p>

<div class="card" id="connection">
    <h2>1. Laravel connection settings</h2>
    <p>Set in <code class="k">.env</code> and read by <code class="k">config/database.php</code>. Passwords are never shown.</p>
    <table class="small">
        <thead><tr><th>Laravel connection</th><th>Host</th><th>Port</th><th>Database</th><th>Login used</th><th>Used by</th></tr></thead>
        <tr><td><code class="k">pgsql</code> (default)</td><td>{{ $connections[0]['host'] }}</td><td>{{ $connections[0]['port'] }}</td><td>{{ $connections[0]['database'] }}</td><td>{{ $connections[0]['username'] }}</td><td>The whole app (Eloquent, migrations)</td></tr>
        <tr><td><code class="k">pgsql_readonly</code></td><td>{{ $connections[1]['host'] }}</td><td>{{ $connections[1]['port'] }}</td><td>{{ $connections[1]['database'] }}</td><td>{{ $connections[1]['username'] }}</td><td>Reports page only</td></tr>
    </table>
    <p style="margin-bottom:0">Live check: this request is connected to <strong>{{ $current->db }}</strong> as <strong>{{ $current->usr }}</strong> ({{ $current->ver }}).
        PostgreSQL was told the caller is <code class="k">app.role = {{ $context->role ?? 'none' }}</code>, <code class="k">app.user_id = {{ $context->uid ?? 'none' }}</code>.</p>
    @if ($current->usr === 'postgres' || $bypass)
        <p class="flash err" style="margin:10px 0 0">The app is connected with the owner login, which skips privilege limits and row-level security. After running <code class="k">roles.sql</code>, switch <code class="k">DB_USERNAME</code> to <code class="k">repair_app</code> in <code class="k">.env</code>.</p>
    @else
        <p class="flash ok" style="margin:10px 0 0">The app is running with a limited login, so the privileges and policies below are really being enforced.</p>
    @endif
</div>

<div class="card" id="roles">
    <h2>2. Roles and privileges</h2>
    <table class="small">
        <thead><tr><th>Role</th><th>Can log in</th><th>Superuser</th><th>Bypass RLS</th><th>Purpose</th></tr></thead>
        @php $purpose = ['postgres' => 'Owner. Runs migrations (DDL).', 'repair_app' => 'The Laravel app: data changes only, no DDL.', 'repair_readonly' => 'Reports: SELECT only, no password hashes.', 'repair_backup' => 'pg_dump: reads everything, writes nothing.']; @endphp
        @foreach ($roles as $r)
            <tr><td><code class="k">{{ $r->rolname }}</code></td><td>{{ $r->rolcanlogin ? 'yes' : 'no' }}</td><td>{{ $r->rolsuper ? 'yes' : 'no' }}</td><td>{{ $r->rolbypassrls ? 'yes' : 'no' }}</td><td>{{ $purpose[$r->rolname] ?? '' }}</td></tr>
        @endforeach
    </table>
    @if (count($privRoles))
        <h2 style="margin-top:16px">Table privileges <span class="muted" style="font-weight:400;font-size:13px">S = select, I = insert, U = update, D = delete</span></h2>
        <table class="small">
            <thead><tr><th>Table</th>@foreach ($privRoles as $role)<th>{{ $role }}</th>@endforeach</tr></thead>
            @foreach ($privileges as $tbl => $byRole)
                <tr><td>{{ $tbl }}</td>@foreach ($privRoles as $role)<td><code class="k">{{ $byRole[$role] ?? '----' }}</code></td>@endforeach</tr>
            @endforeach
        </table>
        <p style="margin-bottom:0">Can the role read <code class="k">users.password</code>?
            @foreach ($passwordAccess as $p)
                <code class="k">{{ $p->rolname }}</code> <span class="{{ $p->can_read ? 'yes' : 'no' }}">{{ $p->can_read ? 'yes' : 'no' }}</span>@if (! $loop->last), @endif
            @endforeach
        </p>
    @else
        <p class="flash err" style="margin-top:12px">The limited roles do not exist yet. Run <code class="k">psql -U postgres -d repair_tracking -f database/sql/roles.sql</code>.</p>
    @endif
</div>

<div class="card" id="design">
    <h2>3. Design: normalization, keys and constraints</h2>
    <ul style="margin-top:0">
        <li>Customers are separate from tickets, so a returning customer is stored once.</li>
        <li>Statuses, part categories and ticket status history are their own tables (no repeated text, one-to-many history).</li>
        <li><code class="k">ticket_parts</code> is the many-to-many junction and keeps the unit price at the time of use.</li>
        <li>Totals are not stored. The view <code class="k">v_ticket_totals</code> calculates them, so they cannot go out of sync.</li>
    </ul>
    <h2>Tables and columns ({{ count($tables) }})</h2>
    <table class="small">
        @foreach ($tables as $t)<tr><td><strong>{{ $t->table_name }}</strong></td><td class="muted">{{ $t->cols }}</td></tr>@endforeach
    </table>
    <h2 style="margin-top:16px">Keys and constraints ({{ count($constraints) }})</h2>
    <table class="small">
        <thead><tr><th>Table</th><th>Type</th><th>Name</th><th>Rule</th></tr></thead>
        @foreach ($constraints as $c)
            <tr><td>{{ $c->tbl }}</td><td>{{ $c->type }}</td><td><code class="k">{{ $c->conname }}</code></td><td><code class="k">{{ $c->def }}</code></td></tr>
        @endforeach
    </table>
</div>

<div class="card" id="indexes">
    <h2>4. Indexes and EXPLAIN</h2>
    <table class="small">
        <thead><tr><th>Table</th><th>Index</th><th>Definition</th></tr></thead>
        @foreach ($indexes as $i)
            <tr><td>{{ $i->tablename }}</td><td><code class="k">{{ $i->indexname }}</code></td><td><code class="k">{{ $i->indexdef }}</code></td></tr>
        @endforeach
    </table>
    <h2 style="margin-top:16px">EXPLAIN ANALYZE, run just now</h2>
    <p class="muted">The demo tables are small, so PostgreSQL would normally scan them sequentially. Sequential scans are switched off only inside this one rolled-back transaction to show which index each query uses.</p>
    @foreach ($explains as $e)
        <p style="margin:14px 0 0"><strong>{{ $e['title'] }}</strong> <span class="badge">expected: {{ $e['index'] }}</span></p>
        <pre class="code">{{ $e['sql'] }}

{{ $e['plan'] }}</pre>
    @endforeach
</div>

<div class="card" id="rls">
    <h2>5. Row-level security (aligned with Laravel login)</h2>
    <p>The app uses one database login, so on every request the <code class="k">SetDatabaseContext</code> middleware tells PostgreSQL the Laravel user's role and id.
        The policies read that context, so the database enforces the same rules as <code class="k">EnsureRole</code>, even if a controller has a bug.</p>
    <table class="small">
        <thead><tr><th>Table</th><th>RLS</th></tr></thead>
        @foreach ($rlsTables as $t)
            <tr><td>{{ $t->tbl }}</td><td><span class="{{ $t->enabled ? 'yes' : 'muted' }}">{{ $t->enabled ? 'enabled' : 'off' }}</span></td></tr>
        @endforeach
    </table>
    <h2 style="margin-top:16px">What each caller can see and change <span class="muted" style="font-weight:400;font-size:13px">(tested live, then rolled back)</span></h2>
    <table class="small">
        <thead><tr><th>Caller context</th><th class="num">Tickets seen</th><th class="num">Parts seen</th><th class="num">Tickets it can edit</th><th class="num">Parts it can edit</th></tr></thead>
        @foreach ($rlsDemo as $r)
            <tr><td>{{ $r['label'] }}</td><td class="num">{{ $r['tickets_seen'] }}</td><td class="num">{{ $r['parts_seen'] }}</td><td class="num">{{ $r['tickets_editable'] }}</td><td class="num">{{ $r['parts_editable'] }}</td></tr>
        @endforeach
    </table>
    @if ($bypass)
        <p class="flash err" style="margin:10px 0 0">All rows look the same because this connection bypasses RLS (owner login). Switch to <code class="k">repair_app</code> to see the policies working.</p>
    @endif
    <h2 style="margin-top:16px">Policies ({{ count($policies) }})</h2>
    <table class="small">
        <thead><tr><th>Table</th><th>Policy</th><th>Command</th><th>Applies to</th><th>Rule</th></tr></thead>
        @foreach ($policies as $p)
            <tr><td>{{ $p->tablename }}</td><td><code class="k">{{ $p->policyname }}</code></td><td>{{ $p->cmd }}</td><td>{{ $p->roles }}</td><td><code class="k">{{ $p->qual ?? $p->with_check }}</code></td></tr>
        @endforeach
    </table>
</div>

<div class="card" id="trigger">
    <h2>6. Function, triggers and views</h2>
    <p>Using a part on a ticket runs <code class="k">fn_deduct_part_stock()</code>: it locks the part row, refuses when stock is short, deducts the quantity and opens one low-stock alert. Removing the part runs <code class="k">fn_restore_part_stock()</code>, which returns the stock and closes the alert.</p>
    <table class="small">
        <thead><tr><th>Function</th><th>Arguments</th><th>Runs as</th></tr></thead>
        @foreach ($functions as $f)
            <tr><td><code class="k">{{ $f->name }}</code></td><td>{{ $f->args ?: 'none' }}</td><td>{{ $f->definer ? 'owner (SECURITY DEFINER)' : 'caller' }}</td></tr>
        @endforeach
    </table>
    <table class="small" style="margin-top:12px">
        <thead><tr><th>Table</th><th>Trigger</th><th>Definition</th></tr></thead>
        @foreach ($triggers as $t)
            <tr><td>{{ $t->tbl }}</td><td><code class="k">{{ $t->name }}</code></td><td><code class="k">{{ $t->def }}</code></td></tr>
        @endforeach
    </table>
    <p style="margin-bottom:0">Views: @foreach ($views as $v)<code class="k">{{ $v->viewname }}</code> @endforeach</p>
</div>

<div class="card" id="backup">
    <h2>7. Backup and restore</h2>
    <p>Run these in a terminal from the project folder:</p>
    <pre class="code">./scripts/backup.sh                          # writes backups/repair_tracking_&lt;timestamp&gt;.dump (as repair_backup)
./scripts/restore.sh backups/&lt;file&gt;.dump     # restores into a NEW database and compares row counts</pre>
    <h2 style="margin-top:16px">Backup files</h2>
    @if ($backups->isEmpty())
        <p class="muted">No backup yet. Run <code class="k">./scripts/backup.sh</code>.</p>
    @else
        <table class="small">
            <thead><tr><th>File</th><th>Size</th><th>Created</th></tr></thead>
            @foreach ($backups as $b)<tr><td><code class="k">{{ $b['name'] }}</code></td><td>{{ $b['size'] }}</td><td>{{ $b['time'] }}</td></tr>@endforeach
        </table>
    @endif
    <h2 style="margin-top:16px">Last restore proof</h2>
    @if ($restoreLog)
        <pre class="code">{{ $restoreLog }}</pre>
    @else
        <p class="muted" style="margin-bottom:0">No restore has been run yet. Run <code class="k">./scripts/restore.sh</code> on a backup file and this proof will appear here.</p>
    @endif
</div>
@endsection
