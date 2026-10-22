<?php

use App\Http\Controllers\BodyCompositionController;
use App\Http\Controllers\EssentialSheetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressSheetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLevelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Public demo — intentionally accessible without authentication.
Route::get('/demo', [BodyCompositionController::class, 'show'])->name('demo');
Route::post('/demo/body-composition', [BodyCompositionController::class, 'store'])
    ->name('demo.body-composition.store');
Route::post('/demo/essential-sheet', [EssentialSheetController::class, 'store'])
    ->name('demo.essential-sheet.store');
Route::post('/demo/progress-sheet', [ProgressSheetController::class, 'store'])
    ->name('demo.progress-sheet.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('user', UserController::class);
    Route::resource('user-level', UserLevelController::class);
});

require __DIR__.'/auth.php';
