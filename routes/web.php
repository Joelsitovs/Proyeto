<?php

use App\Http\Controllers\Morfeo3dController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubidaS3Controller;
use Illuminate\Support\Facades\Route;

Route::get ('/', [Morfeo3dController::class, 'index'])->name('morfeo3d.index');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::get('/canvas', function () {
    return view('morfeo3d.canvas');
});

Route::get('/api/obtener-datos-base', [SubidaS3Controller::class, 'obtenerDatos']);
Route::post('/api/guardar-url-firmada', [SubidaS3Controller::class, 'guardarUrlFirmada']);
Route::get('/api/obtener-url-firmada', [SubidaS3Controller::class, 'obtenerUrlFirmada']);

Route::post('/upload_s3', [SubidaS3Controller::class, 'ejecutarSubidaYConversion'])->name('morfeo3d.upload_s3');