<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Public page: customers can browse the parts that are in stock. Read-only.
// It reads the view v_public_parts, which has no SKU, no stock counts and no reorder levels.
class PublicPartController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        $parts = DB::table('v_public_parts')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'ilike', '%'.$request->query('q').'%'))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->query('category')))
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = DB::table('v_public_parts')->distinct()->orderBy('category')->pluck('category');

        return view('parts.public', compact('parts', 'categories'));
    }
}
