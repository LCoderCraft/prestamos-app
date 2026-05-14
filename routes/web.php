<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth; 

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
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
        
        // Reportes y códigos de barras
        Route::get('/admin/reportes', [App\Http\Controllers\ReportController::class, 'index'])->name('admin.reportes.index');
        Route::get('/admin/reportes/diario', [App\Http\Controllers\ReportController::class, 'diario'])->name('admin.reportes.diario');
        Route::get('/admin/reportes/semanal', [App\Http\Controllers\ReportController::class, 'semanal'])->name('admin.reportes.semanal');
        Route::get('/admin/reportes/mensual', [App\Http\Controllers\ReportController::class, 'mensual'])->name('admin.reportes.mensual');
        Route::get('/admin/codigos', [App\Http\Controllers\AdminController::class, 'codigos'])->name('admin.codigos.index');
        Route::get('/admin/codigos/{id}/regenerar', [App\Http\Controllers\AdminController::class, 'regenerarBarcode'])->name('admin.codigos.regenerar');
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

// =========================================================
// MÓDULO: CENTROS DE CÓMPUTO (RESERVACIONES)
// =========================================================
Route::middleware('auth')->group(function () {
    Route::get('/rooms', [App\Http\Controllers\RoomController::class, 'index'])->name('rooms.index');
    Route::post('/rooms/reserve', [App\Http\Controllers\RoomController::class, 'store'])->name('rooms.store');
    Route::middleware('admin')->group(function () {
        Route::get('/admin/rooms', [App\Http\Controllers\AdminRoomController::class, 'index'])->name('admin.rooms.index');
        Route::post('/admin/rooms', [App\Http\Controllers\AdminRoomController::class, 'storeRoom'])->name('admin.rooms.store');
        Route::put('/admin/rooms/{id}', [App\Http\Controllers\AdminRoomController::class, 'updateRoom'])->name('admin.rooms.update');
        Route::delete('/admin/rooms/{id}', [App\Http\Controllers\AdminRoomController::class, 'destroyRoom'])->name('admin.rooms.destroy');
        Route::post('/admin/rooms/{id}/status', [App\Http\Controllers\AdminRoomController::class, 'updateStatus'])->name('admin.rooms.status');
        Route::get('/admin/rooms/pending-count', [App\Http\Controllers\AdminRoomController::class, 'pendingCount'])->name('admin.rooms.pendingCount');
        Route::get('/admin/support/chat/{id}', [App\Http\Controllers\SupportChatController::class, 'show'])->name('admin.support.chat');
    });
    // =========================================================
    // MÓDULO: CHAT DE AYUDA
    // =========================================================
    Route::get('/support/chat', [App\Http\Controllers\SupportChatController::class, 'index'])->name('support.chat.index');
    Route::post('/support/chat', [App\Http\Controllers\SupportChatController::class, 'store'])->name('support.chat.store');
    Route::get('/support/chat/{id}', [App\Http\Controllers\SupportChatController::class, 'show'])->name('support.chat.show');
    Route::post('/support/chat/{id}/message', [App\Http\Controllers\SupportChatController::class, 'sendMessage'])->name('support.chat.message');
    Route::get('/support/chat/{id}/messages', [App\Http\Controllers\SupportChatController::class, 'messages'])->name('support.chat.messages');
    Route::get('/support/chat/unread/count', [App\Http\Controllers\SupportChatController::class, 'unreadCount'])->name('support.chat.unread');
    Route::get('/support/chat/{id}/export', [App\Http\Controllers\SupportChatController::class, 'export'])->name('support.chat.export');
});

require __DIR__.'/auth.php';