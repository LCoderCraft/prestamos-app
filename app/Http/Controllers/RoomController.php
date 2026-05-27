<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\ComputerRoom;
use App\Models\RoomReservation;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\NewRoomReservation;
use Illuminate\Support\Facades\Notification;

// controlador para las reservaciones de centros de computo
// aqui manejo el calendario semanal y la logica de reservar
class RoomController extends Controller
{
    // muestra el calendario con las reservaciones de la semana
    // el usuario puede cambiar de semana con los botones anterior/siguiente
    // tambien puede seleccionar que centro de computo ver
    public function index(Request $request)
    {
        // solo centros activos
        $rooms = ComputerRoom::where('is_active', true)->get();
        // si no seleccionaron un centro, agarro el primero
        $selectedRoomId = $request->room_id ?? $rooms->first()?->id;

        // calculo la semana: empieza en lunes, termina en viernes
        // si la url trae una fecha, uso esa, si no, la semana actual
        $startOfWeek = Carbon::parse($request->week ?? now())->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $startOfWeek->copy()->endOfWeek(Carbon::FRIDAY);

        // busco reservaciones que se traslapen con la semana
        // uso orWhere porque una reservacion puede empezar antes y terminar dentro de la semana
        $reservations = RoomReservation::with('user')
            ->where('computer_room_id', $selectedRoomId)
            ->where(function ($q) use ($startOfWeek, $endOfWeek) {
                $q->whereBetween('start_date', [$startOfWeek, $endOfWeek])
                  ->orWhereBetween('end_date', [$startOfWeek, $endOfWeek]);
            })
            ->get();

        // genero los 5 dias de la semana (lunes a viernes)
        $weekDays = [];
        for ($i = 0; $i < 5; $i++) {
            $weekDays[] = $startOfWeek->copy()->addDays($i);
        }

        // las horas que se muestran en el calendario (7am a 8pm)
        $hours = [];
        for ($h = 7; $h <= 20; $h++) {
            $hours[] = $h;
        }

        return view('rooms.index', compact('rooms', 'selectedRoomId', 'weekDays', 'hours', 'reservations', 'startOfWeek', 'endOfWeek'));
    }

    // aqui se guarda la reservacion cuando el usuario llena el formulario
    // primero valido, luego checo si hay espacio, y si todo bien, la creo
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

        // calculo la hora de inicio y fin segun lo que eligio el usuario
        $start = Carbon::parse($request->date . ' ' . $request->time);
        $end = $start->copy()->addHours((int) $request->duration);

        $room = ComputerRoom::find($request->computer_room_id);

        // checo cuantas reservaciones activas o pendientes hay en ese horario
        // uso la misma logica de traslape que en el modelo
        $occupiedCount = RoomReservation::where('computer_room_id', $room->id)
            ->whereIn('status', ['active', 'pending'])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_date', '<', $end)
                  ->where('end_date', '>', $start);
            })
            ->count();

        // si ya no hay lugares, rechazo la reservacion
        if ($occupiedCount >= $room->capacity) {
            return back()->with('error', 'El centro de cómputo está lleno en ese horario.');
        }

        // creo la reservacion como pendiente, para que un admin la apruebe despues
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

        // les aviso a los admins que hay una nueva reservacion pendiente
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewRoomReservation($reservation));

        return redirect()->route('rooms.index')->with('success', 'Reservación enviada para aprobación.');
    }
}