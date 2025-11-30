<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // --- AQUÍ ESTÁ LA CLAVE ---
        // Si el usuario es administrador, lo mandamos a SU panel
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        // --------------------------

        // Si NO es admin (es alumno), cargamos la lógica normal
        
        // 1. Obtener items ACTIVOS
        $items = Item::where('is_active', true)->get();
        
        // 2. Obtener mis préstamos
        $myLoans = Auth::user()->loans()->with('item')->latest()->get();

        // 3. Cargar la VISTA de Alumno
        return view('dashboard', compact('items', 'myLoans'));
    }
    
    // Función para actualizar perfil (esta ya la tenías, la dejamos aquí)
    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
            'current_password' => 'required|current_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user->email = $request->email;

        if ($request->filled('new_password')) {
            $user->password = bcrypt($request->new_password);
        }

        $user->save();

        return back()->with('success', '¡Tus datos han sido actualizados correctamente!');
    }
}