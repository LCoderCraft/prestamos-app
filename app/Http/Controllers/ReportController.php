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
