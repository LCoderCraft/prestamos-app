<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $items = Item::all();
        $myLoans = Auth::user()->loans()->with('item')->latest()->get();

        return view('dashboard', compact('items', 'myLoans'));
    }
}