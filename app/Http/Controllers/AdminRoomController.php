<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ComputerRoom;
use App\Models\RoomReservation;
use App\Models\User;
use App\Notifications\RoomReservationStatusChanged;
use App\Mail\RoomReservationConfirmation;
use Illuminate\Support\Facades\Mail;
class AdminRoomController extends Controller
{
    public function index()
    {
        $rooms = ComputerRoom::all();
        $reservations = RoomReservation::with(['user', 'computerRoom'])
            ->whereIn('status', ['pending', 'active'])
            ->orderByRaw("FIELD(status, 'pending') DESC")
            ->orderBy('start_date', 'asc')
            ->get();
        $history = RoomReservation::with(['user', 'computerRoom'])
            ->whereIn('status', ['finished', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('admin.rooms.index', compact('rooms', 'reservations', 'history'));
    }
    public function storeRoom(Request $request)
    {
        ComputerRoom::create($request->validate([
            'name' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'location' => 'nullable|string',
            'photo_url' => 'nullable|url',
        ]));
        return redirect('/admin/rooms')->with('success', 'Centro de cómputo agregado.');
    }
    public function updateStatus(Request $request, $id)
    {
        $reservation = RoomReservation::findOrFail($id);
        if ($request->action === 'approve') {
            $reservation->status = 'active';
            $reservation->admin_comment = $request->comment ?? 'Aprobado';
        } elseif ($request->action === 'reject') {
            $reservation->status = 'rejected';
            $reservation->admin_comment = $request->comment ?? 'Rechazado';
        } elseif ($request->action === 'finish') {
            $reservation->status = 'finished';
            $reservation->admin_comment = $request->comment ?? 'Finalizado';
        }
        $reservation->save();

        if ($request->action === 'approve' || $request->action === 'reject') {
            $reservation->user->notify(new RoomReservationStatusChanged($reservation));
            if ($reservation->user->email) {
                try {
                    Mail::to($reservation->user->email)->send(new RoomReservationConfirmation($reservation));
                } catch (\Exception $e) {}
            }
        }

        return redirect('/admin/rooms')->with('success', 'Reservación actualizada.');
    }

    public function updateRoom(Request $request, $id)
    {
        $room = ComputerRoom::findOrFail($id);
        $room->name = $request->name;
        $room->capacity = $request->capacity;
        $room->location = $request->location;
        $room->is_active = $request->has('is_active');
        $room->save();
        return redirect('/admin/rooms')->with('success', 'Centro de cómputo actualizado.');
    }

    public function destroyRoom($id)
    {
        $room = ComputerRoom::findOrFail($id);
        $room->delete();
        return redirect('/admin/rooms')->with('success', 'Centro de cómputo eliminado.');
    }

    public function pendingCount()
    {
        $count = RoomReservation::where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    }
}