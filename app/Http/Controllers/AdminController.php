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
        return back()->with('success', 'Producto agregado.');
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

        return back()->with('success', 'Producto actualizado correctamente.');
    }
}