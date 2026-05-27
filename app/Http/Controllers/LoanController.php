<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use App\Models\Loan;
use App\Models\Item;
use App\Models\User;

use App\Notifications\NewLoanRequest;
use App\Notifications\LoanStatusChanged;

use App\Mail\LoanStatusUpdate;
use App\Mail\LoanReturned; 

class LoanController extends Controller
{
    // los usuarios solicitan prestamos de equipos desde el dashboard
    // esta funcion valida, checa disponibilidad y guarda la solicitud
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'duration' => 'required|integer|min:1',
        ]);

        // calculo el rango de la solicitud con fecha y hora que puso el usuario
        $start = Carbon::parse($request->date . ' ' . $request->time);
        $end = $start->copy()->addHours((int) $request->duration);
        
        $item = Item::find($request->item_id);

        // si no hay stock disponible, busco cuando se desocupa el siguiente equipo
        // y le sugiero al usuario que agende a partir de esa hora
        if (!$item->isAvailable($start, $end)) {
            
            $nextFreeLoan = Loan::where('item_id', $item->id)
                ->whereIn('status', ['active', 'pending'])
                ->where(function($q) use ($start, $end) {
                    $q->where('start_date', '<', $end)
                      ->where('end_date', '>', $start);
                })
                ->orderBy('end_date', 'asc') 
                ->first();

            $suggestion = "";
            if ($nextFreeLoan) {
                $timeFree = $nextFreeLoan->end_date->format('H:i');
                $suggestion = "Un equipo se desocupará a las <b>{$timeFree}</b> hrs. Por favor agenda a partir de esa hora.";
            }

            return back()->with('error', "No hay stock suficiente en este horario. {$suggestion}");
        }

        // si hay disponibilidad, creo el prestamo como pendiente
        $loan = Loan::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'start_date' => $start,
            'end_date' => $end,
            'status' => 'pending'
        ]);

        // notifico a los admins para que revisen la solicitud
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewLoanRequest($loan));
        
        return back()->with('success', 'Solicitud enviada correctamente.');
    }

    // los admins pueden cambiar el estado de un prestamo desde el admin dashboard
    // aprobar, rechazar o marcar como devuelto (finish)
    // cuando finalizan, se manda correo al usuario con la observacion
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
            
            $observation = $request->comment ?? 'Entregado en tiempo y forma sin daños.';
            $loan->admin_comment = "DEVOLUCIÓN: " . $observation;
            
            // mando correo de devolucion solo cuando finaliza el prestamo
            if ($loan->user->email) {
                try {
                    Mail::to($loan->user->email)->send(new LoanReturned($loan, $observation));
                } catch (\Exception $e) {}
            }
        }
        
        $loan->save();

        // notificacion en el sistema y por correo para cualquier cambio de estado
        if ($request->action == 'approve' || $request->action == 'reject' || $request->action == 'finish') {
            $loan->user->notify(new LoanStatusChanged($loan));
            
            if ($loan->user->email) {
                try { 
                    Mail::to($loan->user->email)->send(new LoanStatusUpdate($loan)); 
                } catch (\Exception $e) {}
            }
        }

        // marco como leida la notificacion del admin
        $n = auth()->user()->unreadNotifications->where('data.loan_id', $loan->id)->first();
        if($n) $n->markAsRead();

        return redirect('/admin')->with('success', 'Proceso completado.');
    }
}