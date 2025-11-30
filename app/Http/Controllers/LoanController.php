<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User; // Importante para buscar admins
use Illuminate\Support\Facades\Notification; // Importante para enviar
use App\Notifications\NewLoanRequest;
use App\Notifications\LoanStatusChanged;

class LoanController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'duration' => 'required|integer|min:1',
        ]);

        $start = Carbon::parse($request->date . ' ' . $request->time);
        
        // --- CORRECCIÓN AQUÍ: Agregamos (int) ---
        $end = $start->copy()->addHours((int) $request->duration);
        // ----------------------------------------
        
        $item = Item::find($request->item_id);

        if (!$item->isAvailable($start, $end)) {
            return back()->with('error', 'El producto no está disponible en ese horario.');
        }

        $loan = Loan::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'start_date' => $start,
            'end_date' => $end,
            'status' => 'pending'
        ]);

        // Notificaciones al Admin
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewLoanRequest($loan));
        
        return back()->with('success', 'Solicitud enviada correctamente.');
    }

    // Acciones del admin
public function updateStatus(Request $request, $id) {
        $loan = Loan::findOrFail($id);
        
        // 1. Lógica de cambio de estado (Aprobar/Rechazar)
        if ($request->action == 'approve') {
            $loan->status = 'active';
            $loan->admin_comment = $request->comment ?? 'Aprobado';
        } elseif ($request->action == 'reject') {
            $loan->status = 'rejected';
            $loan->admin_comment = $request->comment ?? 'Rechazado';
        } elseif ($request->action == 'finish') {
            $loan->status = 'finished';
        }
        
        $loan->save();

        // 2. Notificar al USUARIO (Tu código actual)
        if ($request->action == 'approve' || $request->action == 'reject') {
            $loan->user->notify(new LoanStatusChanged($loan));
        }

        // --- [NUEVO] --- 
        // 3. LIMPIAR LA NOTIFICACIÓN DEL ADMIN AUTOMÁTICAMENTE
        // Buscamos en las notificaciones no leídas del admin aquella que coincida con este loan_id
        $notification = auth()->user()->unreadNotifications
                            ->where('data.loan_id', $loan->id) 
                            ->first();

        // Si existe esa notificación, la marcamos como leída (desaparece de la lista amarilla)
        if ($notification) {
            $notification->markAsRead();
        }
        // -----------------------------------------------------

        return back()->with('success', 'Estado actualizado.');
    }
}