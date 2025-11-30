<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewLoanRequest;
use App\Notifications\LoanStatusChanged;

// --- OJO AQUÍ: Faltaban estas librerías para el correo ---
use Illuminate\Support\Facades\Mail;
use App\Mail\LoanStatusUpdate;
// ---------------------------------------------------------

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
        $end = $start->copy()->addHours((int) $request->duration);
        
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

        // Notificaciones al Admin (Burbuja)
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewLoanRequest($loan));
        
        return back()->with('success', 'Solicitud enviada correctamente.');
    }

    // Acciones del admin
    public function updateStatus(Request $request, $id) {
        $loan = Loan::findOrFail($id);
        
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

        // Si se aprobó o rechazó...
        if ($request->action == 'approve' || $request->action == 'reject') {
            
            // 1. Enviar Notificación Interna (Burbuja)
            $loan->user->notify(new LoanStatusChanged($loan));

            // --- OJO AQUÍ: ESTO ES LO QUE FALTABA (EL CORREO) ---
            if ($loan->user->email) {
                // Usamos try-catch para que si falla el internet no rompa la página
                try {
                    Mail::to($loan->user->email)->send(new LoanStatusUpdate($loan));
                } catch (\Exception $e) {
                    // El correo falló, pero no detenemos el sistema
                }
            }
            // ----------------------------------------------------
        }

        // Limpiar la notificación amarilla del admin
        $notification = auth()->user()->unreadNotifications
                            ->where('data.loan_id', $loan->id) 
                            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Estado actualizado.');
    }
}