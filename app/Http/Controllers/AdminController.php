<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Item;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Préstamos Activos o Pendientes (Para la pestaña de Gestión)
        $activeLoans = Loan::with(['user', 'item'])
                           ->whereIn('status', ['pending', 'active'])
                           ->orderBy('start_date', 'asc')
                           ->get();

        // 2. Historial (Finalizados, Rechazados, Cancelados)
        $historyLoans = Loan::with(['user', 'item'])
                            ->whereIn('status', ['finished', 'rejected', 'cancelled'])
                            ->orderBy('updated_at', 'desc') // Lo más reciente arriba
                            ->get();

        $items = Item::all();

        // Enviamos ambas variables a la vista
        return view('admin.dashboard', compact('activeLoans', 'historyLoans', 'items'));
    }

    public function storeItem(Request $request) {
        Item::create($request->all());
        return back()->with('success', 'Producto agregado.');
    }

    // Actualizar producto (Cantidad, Foto, Disponibilidad)
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
        // El checkbox envía "1" si está marcado, o nada si no lo está.
        $item->is_active = $request->has('is_active'); 

        $item->save();

        return back()->with('success', 'Producto actualizado correctamente.');
    }
}