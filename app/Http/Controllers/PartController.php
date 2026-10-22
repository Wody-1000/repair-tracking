<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartRequest;
use App\Models\LowStockAlert;
use App\Models\Part;
use App\Models\PartCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PartController extends Controller
{
    public function index(Request $request)
    {
        $parts = Part::with('category')
            ->when($request->filled('q'), fn ($q) => $q->where(function ($w) use ($request) {
                $w->where('sku', 'ilike', '%'.$request->query('q').'%')
                    ->orWhere('name', 'ilike', '%'.$request->query('q').'%');
            }))
            ->when($request->boolean('low'), fn ($q) => $q->lowStock())
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('parts.index', compact('parts'));
    }

    public function create()
    {
        return view('parts.form', ['part' => new Part(['quantity' => 0, 'reorder_level' => 5]), 'categories' => PartCategory::orderBy('name')->get()]);
    }

    public function store(PartRequest $request): RedirectResponse
    {
        Part::create($request->validated());

        return redirect()->route('parts.index')->with('success', 'Part added to inventory.');
    }

    public function edit(Part $part)
    {
        return view('parts.form', ['part' => $part, 'categories' => PartCategory::orderBy('name')->get()]);
    }

    public function update(PartRequest $request, Part $part): RedirectResponse
    {
        $part->update($request->validated());

        // Restocked above the reorder level -> close open alerts
        if (! $part->isLowStock()) {
            LowStockAlert::where('part_id', $part->id)->where('resolved', false)->update(['resolved' => true]);
        }

        return redirect()->route('parts.index')->with('success', 'Part updated.');
    }

    public function destroy(Part $part): RedirectResponse
    {
        try {
            $part->delete();
        } catch (QueryException $e) {
            // ticket_parts.part_id is ON DELETE RESTRICT: used parts keep their history
            return back()->withErrors(['delete' => "{$part->name} has been used on tickets and cannot be deleted."]);
        }

        return redirect()->route('parts.index')->with('success', 'Part deleted.');
    }
}
