<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Item;

class AdminController extends Controller
{
   public function index()
    {
        $activeLoans = Loan::with(['user', 'item'])
                           ->whereIn('status', ['pending', 'active'])
                           ->orderByRaw("FIELD(status, 'pending') DESC")
                           ->orderBy('created_at', 'asc') 
                           ->get();

        $historyLoans = Loan::with(['user', 'item'])
                            ->whereIn('status', ['finished', 'rejected', 'cancelled'])
                            ->orderBy('updated_at', 'desc')
                            ->get();

        $items = Item::all();

        return view('admin.dashboard', compact('activeLoans', 'historyLoans', 'items'));
    }

    public function storeItem(Request $request) {
        Item::create($request->all());
        return redirect('/admin')->with('success', 'Producto agregado.');
    }

    public function updateItem(Request $request, $id) {
        $item = Item::findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'total_count' => 'required|integer|min:0',
            'photo_url' => 'nullable|url',
        ]);

        $item->name = $request->name;
        $item->total_count = $request->total_count;
        $item->photo_url = $request->photo_url;
        $item->is_active = $request->has('is_active'); 

        $item->save();

        return redirect('/admin')->with('success', 'Producto actualizado correctamente.');
    }

    public function codigos()
    {
        $items = Item::all();
        foreach ($items as $item) {
            if (!$item->barcode) {
                $item->barcode = 'UAS-INV-' . strtoupper(\Illuminate\Support\Str::random(6));
                $item->save();
            }
        }
        return view('codigos', compact('items'));
    }

    public function regenerarBarcode($id)
    {
        $item = Item::findOrFail($id);
        $item->barcode = 'UAS-INV-' . strtoupper(\Illuminate\Support\Str::random(6));
        $item->save();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json(['barcode' => $item->barcode, 'name' => $item->name, 'success' => true]);
        }
        return back()->with('success', 'Código de barras regenerado para ' . $item->name);
    }
}