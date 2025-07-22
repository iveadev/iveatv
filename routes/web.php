<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\programationController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return redirect('dashboard');
})->name('home');
Route::get('/show', [BannerController::class, 'display'])->name('banner.display');
Route::get('/old', [BannerController::class, 'old'])->name('banner.old');
 Route::get('/streaming/{id}', [BannerController::class, 'getStreaming'])->name('streaming');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/banners', [BannerController::class, 'index'])->name('banner.index');
    Route::get('/file/list', [FileController::class, 'getList'])->name('file.list');
    Route::post('/files/{id}', [FileController::class, 'update'])->name('file.update');
    Route::resource('/event', EventController::class)->names('event');
    Route::resource('/files', FileController::class)->names('file');
    Route::get('/dashboard', [programationController::class, 'index'])->name('dashboard');
    Route::resource('/programation', FileController::class)->names('programation');
    
});

require __DIR__.'/auth.php';
