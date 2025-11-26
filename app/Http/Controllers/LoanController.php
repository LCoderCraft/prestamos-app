<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        $end = $start->copy()->addHours($request->duration);
        $item = Item::find($request->item_id);

        if (!$item->isAvailable($start, $end)) {
            return back()->with('error', 'El producto no está disponible en ese horario.');
        }

        Loan::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'start_date' => $start,
            'end_date' => $end,
            'status' => 'pending'
        ]);

        // Aquí podrías disparar el email con Laravel Mail si quisieras
        
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
        return back()->with('success', 'Estado actualizado.');
    }
}