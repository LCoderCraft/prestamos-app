<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

// Modelos
use App\Models\Loan;
use App\Models\Item;
use App\Models\User;

// Notificaciones
use App\Notifications\NewLoanRequest;
use App\Notifications\LoanStatusChanged;

// Correos (Mails)
use App\Mail\LoanStatusUpdate;
use App\Mail\LoanReturned; // <--- Esta es la que te marcaba error en la foto

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

        // Crear fechas de inicio y fin solicitadas
        $start = Carbon::parse($request->date . ' ' . $request->time);
        $end = $start->copy()->addHours((int) $request->duration);
        
        $item = Item::find($request->item_id);

        // --- VALIDACIÓN DE DISPONIBILIDAD ---
        if (!$item->isAvailable($start, $end)) {
            
            // Si no está disponible, buscamos CUÁNDO se libera el primero.
            // Buscamos los préstamos que estorban (overlap) y los ordenamos por el que termina primero.
            $nextFreeLoan = Loan::where('item_id', $item->id)
                ->whereIn('status', ['active', 'pending'])
                ->where(function($q) use ($start, $end) {
                    $q->where('start_date', '<', $end)
                      ->where('end_date', '>', $start);
                })
                ->orderBy('end_date', 'asc') // El que termine primero es el que libera el cupo
                ->first();

            $suggestion = "";
            if ($nextFreeLoan) {
                $timeFree = $nextFreeLoan->end_date->format('H:i');
                $suggestion = "Un equipo se desocupará a las <b>{$timeFree}</b> hrs. Por favor agenda a partir de esa hora.";
            }

            // Regresamos el error con la sugerencia
            return back()->with('error', "No hay stock suficiente en este horario. {$suggestion}");
        }

        // Si pasó la validación, creamos el préstamo
        $loan = Loan::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'start_date' => $start,
            'end_date' => $end,
            'status' => 'pending'
        ]);

        // Notificar Admin
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewLoanRequest($loan));
        
        return back()->with('success', 'Solicitud enviada correctamente.');
    }

    // Acciones del admin (Aprobar, Rechazar, Finalizar)
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
            
            // Guardar observación de entrega
            $observation = $request->comment ?? 'Entregado en tiempo y forma sin daños.';
            $loan->admin_comment = "DEVOLUCIÓN: " . $observation;
            
            // Enviar correo de devolución (LoanReturned)
            if ($loan->user->email) {
                try {
                    Mail::to($loan->user->email)->send(new LoanReturned($loan, $observation));
                } catch (\Exception $e) {}
            }
        }
        
        $loan->save();

        // Notificación de aprobación/rechazo (LoanStatusChanged y LoanStatusUpdate)
        if ($request->action == 'approve' || $request->action == 'reject') {
            $loan->user->notify(new LoanStatusChanged($loan));
            
            if ($loan->user->email) {
                try { 
                    Mail::to($loan->user->email)->send(new LoanStatusUpdate($loan)); 
                } catch (\Exception $e) {}
            }
        }

        // Limpiar notificación amarilla del dashboard admin
        $n = auth()->user()->unreadNotifications->where('data.loan_id', $loan->id)->first();
        if($n) $n->markAsRead();

        return back()->with('success', 'Proceso completado.');
    }
}