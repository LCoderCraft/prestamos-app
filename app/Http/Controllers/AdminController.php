<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Item;

class AdminController extends Controller
{
    public function index()
    {
        $loans = Loan::with(['user', 'item'])->orderBy('created_at', 'desc')->get();
        $items = Item::all();
        return view('admin.dashboard', compact('loans', 'items'));
    }

    public function storeItem(Request $request) {
        Item::create($request->all());
        return back()->with('success', 'Producto agregado.');
    }
}