<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth; 

Route::get('/', function () {
    if (Auth::check()) {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::put('/profile/update-credentials', [DashboardController::class, 'updateSettings'])->name('profile.update_credentials');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', action: [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================
    // RUTAS EXCLUSIVAS PARA ADMINISTRADORES
    // =========================================================
    Route::middleware('admin')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::post('/admin/loans/{id}', [LoanController::class, 'updateStatus'])->name('admin.loans.update');
        Route::post('/admin/items', [AdminController::class, 'storeItem'])->name('admin.items.store');
        Route::put('/admin/items/{id}', [App\Http\Controllers\AdminController::class, 'updateItem'])->name('admin.items.update');
        
        // Vistas de demostración para el sistema de préstamos
        Route::view('/admin/reportes', 'reportes')->name('admin.reportes.index');
        Route::view('/admin/codigos', 'codigos')->name('admin.codigos.index');
    });

    Route::get('/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read');

    Route::get('/notifications/check', function () {
        $user = auth()->user();
        $unread = $user->unreadNotifications;
        
        return response()->json([
            'count' => $unread->count(),
            'latest' => $unread->first() ? $unread->first()->data : null
        ]);
    })->name('notifications.check');

});

Route::get('/generar-codigos-faltantes', function() {
    $items = App\Models\Item::whereNull('barcode')->get();
    foreach($items as $item) {
        $item->barcode = 'UAS-INV-' . strtoupper(\Illuminate\Support\Str::random(6));
        $item->save();
    }
    return "¡Listo! Se generaron " . $items->count() . " códigos nuevos para los equipos existentes.";
});

require __DIR__.'/auth.php';