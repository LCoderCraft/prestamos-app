<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Item;
use App\Models\RoomReservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
class ReportController extends Controller
{
    public function index()
    {
        return view('reportes');
    }

    public function diario()
    {
        $date = Carbon::today();
        $loans = Loan::with(['user', 'item'])
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();
        $reservations = RoomReservation::with(['user', 'computerRoom'])
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdfs.reporte', [
            'title' => 'Reporte Diario - ' . $date->format('d/m/Y'),
            'date' => $date,
            'loans' => $loans,
            'reservations' => $reservations,
            'type' => 'diario',
        ]);

        return $pdf->download('reporte-diario-' . $date->format('Y-m-d') . '.pdf');
    }

    public function semanal()
    {
        $end = Carbon::today();
        $start = $end->copy()->subDays(7);
        $loans = Loan::with(['user', 'item'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();
        $reservations = RoomReservation::with(['user', 'computerRoom'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdfs.reporte', [
            'title' => 'Reporte Semanal - ' . $start->format('d/m/Y') . ' al ' . $end->format('d/m/Y'),
            'date' => $start . ' - ' . $end,
            'loans' => $loans,
            'reservations' => $reservations,
            'type' => 'semanal',
        ]);

        return $pdf->download('reporte-semanal-' . $start->format('Y-m-d') . '.pdf');
    }

    public function productoStats(Request $request)
    {
        $productId = $request->input('product_id');

        $loans = Loan::with(['user', 'item'])
            ->where('item_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get();

        $total = $loans->count();
        $approved = $loans->whereIn('status', ['active', 'finished'])->count();
        $rejected = $loans->where('status', 'rejected')->count();
        $uniqueUsers = $loans->pluck('user_id')->unique()->count();

        $loanData = $loans->map(function ($loan) {
            return [
                'user' => $loan->user?->username ?? 'Usuario eliminado',
                'date' => $loan->created_at ? $loan->created_at->format('d/m/Y') : '—',
                'time' => ($loan->start_date ? $loan->start_date->format('H:i') : '¿?') . ' - ' . ($loan->end_date ? $loan->end_date->format('H:i') : '¿?'),
                'status' => $loan->status,
                'admin_comment' => $loan->admin_comment,
            ];
        });

        return response()->json([
            'total' => $total,
            'approved' => $approved,
            'rejected' => $rejected,
            'unique_users' => $uniqueUsers,
            'loans' => $loanData,
        ]);
    }

    public function mensual()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $loans = Loan::with(['user', 'item'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();
        $reservations = RoomReservation::with(['user', 'computerRoom'])
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('pdfs.reporte', [
            'title' => 'Reporte Mensual - ' . $start->format('F Y'),
            'date' => $start . ' - ' . $end,
            'loans' => $loans,
            'reservations' => $reservations,
            'type' => 'mensual',
        ]);

        return $pdf->download('reporte-mensual-' . $start->format('Y-m') . '.pdf');
    }
}
