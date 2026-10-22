<?php

namespace App\Http\Controllers;

use App\Support\DbContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Reports use the read-only database role (connection `pgsql_readonly`): this module can never change data.
class ReportController extends Controller
{
    public function index(Request $request)
    {
        $db = DB::connection('pgsql_readonly');
        // Same caller context as the main connection (matters if the read-only credentials are not set and the main login is used)
        DbContext::apply($db, $request->user());

        $byStatus = $db->table('repair_statuses as s')
            ->leftJoin('tickets as t', 't.status_id', '=', 's.id')
            ->groupBy('s.id', 's.name', 's.sort_order')
            ->orderBy('s.sort_order')
            ->get(['s.name', DB::raw('count(t.id) as total')]);

        $perMonth = $db->table('tickets')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy(DB::raw("to_char(created_at, 'YYYY-MM')"))
            ->orderBy(DB::raw("to_char(created_at, 'YYYY-MM')"))
            ->get([DB::raw("to_char(created_at, 'YYYY-MM') as month"), DB::raw('count(*) as total')]);

        $topParts = $db->table('ticket_parts as tp')
            ->join('parts as p', 'p.id', '=', 'tp.part_id')
            ->groupBy('p.id', 'p.sku', 'p.name')
            ->orderByDesc(DB::raw('sum(tp.quantity)'))
            ->limit(10)
            ->get(['p.sku', 'p.name', DB::raw('sum(tp.quantity) as used'), DB::raw('sum(tp.quantity * tp.unit_price) as value')]);

        $lowStock = $db->table('v_low_stock_parts')->orderBy('quantity')->get();

        $revenue = $db->table('v_ticket_totals as v')
            ->join('tickets as t', 't.id', '=', 'v.ticket_id')
            ->join('repair_statuses as s', 's.id', '=', 't.status_id')
            ->where('s.name', 'Released')
            ->selectRaw('count(*) as tickets, coalesce(sum(v.parts_cost),0) as parts, coalesce(sum(v.labor_cost),0) as labor, coalesce(sum(v.total_cost),0) as total')
            ->first();

        return view('reports.index', compact('byStatus', 'perMonth', 'topParts', 'lowStock', 'revenue'));
    }
}
