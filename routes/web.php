<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Storage;

Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');
// API Routes สำหรับตารางงาน
Route::prefix('api')->group(function () {
    Route::resource('schedules', ScheduleController::class)->except(['create', 'edit']);
});
// Route::get('/schedules', [ScheduleController::class, 'index']);
// Route::post('/schedules', [ScheduleController::class, 'store']);
// Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
// Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
// Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
// Route::get('/schedules', [ScheduleController::class, 'getEvents']);

Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
Route::get('/schedules/events', [ScheduleController::class, 'getEvents'])->name('schedules.getEvents');
Route::post('/schedules/store', [ScheduleController::class, 'store'])->name('schedules.store');
Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
Route::put('/schedules/{schedule}/update', [ScheduleController::class, 'update'])->name('schedules.update');
Route::delete('/schedules/{schedule}/delete', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

Route::get('/schedules', [ScheduleController::class, 'getEvents']);
Route::post('/schedules', [ScheduleController::class, 'store']);
Route::put('/schedules/{schedule}', [ScheduleController::class, 'update']);
Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy']);

Route::get('/schedules/{schedule}', [ScheduleController::class, 'show']);
Route::put('/schedules/{id}/update-status', [ScheduleController::class, 'updateStatus']);
Route::get('/storage/{filename}', function ($filename) {
    $path = storage_path('app/public/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
});
Route::delete('/files/{id}', [ScheduleController::class, 'deleteFile'])->name('files.delete');