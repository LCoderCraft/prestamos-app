<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\RoomReservation;
use Illuminate\Support\Facades\Auth;

// controlador del dashboard del usuario normal
// si el usuario es admin, lo mando al admin dashboard
class DashboardController extends Controller
{
    public function index()
    {
        // si es admin, que no se quede aqui, que se vaya a su dashboard
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // solo equipos activos para mostrar
        $items = Item::where('is_active', true)->get();
        
        // prestamos del usuario, los mas recientes primero
        $myLoans = Auth::user()->loans()->with('item')->latest()->get();

        // reservaciones de centros de computo que aun estan pendientes o activas
        // las ordeno por fecha para ver las mas proximas primero
        $myRooms = RoomReservation::with('computerRoom')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'active'])
            ->orderBy('start_date', 'asc')
            ->get();

        return view('dashboard', compact('items', 'myLoans', 'myRooms'));
    }
    
    // el usuario puede cambiar su email y contraseña desde el modal de perfil
    // pide la contraseña actual por seguridad
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