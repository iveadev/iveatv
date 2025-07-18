<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\FileController;

Route::get('/', [BannerController::class, 'display'])->name('banner.display');
Route::get('/old', [BannerController::class, 'old'])->name('banner.old');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/banners', [BannerController::class, 'index'])->name('banner.index');
    Route::post('/files/{id}', [FileController::class, 'update'])->name('file.update');
    Route::resource('/files', FileController::class)->names('file');
    Route::get('/dashboard', [BannerController::class, 'index'])->name('dashboard');
    
});

require __DIR__.'/auth.php';
