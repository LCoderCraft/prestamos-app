<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ComputerRoom;
use App\Models\RoomReservation;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\NewRoomReservation;
use Illuminate\Support\Facades\Notification;
class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = ComputerRoom::where('is_active', true)->get();
        $selectedRoomId = $request->room_id ?? $rooms->first()?->id;
        $startOfWeek = Carbon::parse($request->week ?? now())->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::FRIDAY);
        $reservations = RoomReservation::with('user')
            ->where('computer_room_id', $selectedRoomId)
            ->where(function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('start_date', [$startOfWeek, $endOfWeek])
                  ->orWhereBetween('end_date', [$startOfWeek, $endOfWeek]);
            })
            ->get();
        $weekDays = [];
        for ($i = 0; $i < 5; $i++) {
            $weekDays[] = $startOfWeek->copy()->addDays($i);
        }
        $hours = [];
        for ($h = 7; $h <= 20; $h++) {
            $hours[] = $h;
        }
        return view('rooms.index', compact('rooms', 'selectedRoomId', 'weekDays', 'hours', 'reservations', 'startOfWeek', 'endOfWeek'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'computer_room_id' => 'required|exists:computer_rooms,id',
            'requester_type' => 'required|in:user,group,teacher',
            'group_name' => 'required_if:requester_type,group|nullable|string',
            'teacher_name' => 'required_if:requester_type,teacher|nullable|string',
            'purpose' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'duration' => 'required|integer|min:1',
        ]);
        $start = Carbon::parse($request->date . ' ' . $request->time);
        $end = $start->copy()->addHours((int) $request->duration);
        $room = ComputerRoom::find($request->computer_room_id);
        $occupiedCount = RoomReservation::where('computer_room_id', $room->id)
            ->whereIn('status', ['active', 'pending'])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_date', '<', $end)
                  ->where('end_date', '>', $start);
            })
            ->count();
        if ($occupiedCount >= $room->capacity) {
            return back()->with('error', 'El centro de cómputo está lleno en ese horario.');
        }
        $reservation = RoomReservation::create([
            'computer_room_id' => $room->id,
            'user_id' => auth()->id(),
            'requester_type' => $request->requester_type,
            'group_name' => $request->group_name,
            'teacher_name' => $request->teacher_name,
            'purpose' => $request->purpose,
            'start_date' => $start,
            'end_date' => $end,
            'status' => 'pending',
        ]);

        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewRoomReservation($reservation));

        return redirect()->route('rooms.index')->with('success', 'Reservación enviada para aprobación.');
    }
}