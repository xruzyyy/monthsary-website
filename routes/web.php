<?php

use App\Http\Controllers\AnniversaryController;
use App\Http\Controllers\AnniversaryPinController;
use App\Http\Controllers\AnniversaryPuzzleController;
use App\Http\Controllers\AnniversaryRevealController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AnniversaryController::class, 'index'])->name('anniversary');

Route::inertia('/dashboard', 'Dashboard')
    ->middleware('auth')
    ->name('dashboard');

require __DIR__.'/settings.php';

Route::get('/anniversary/pin', [AnniversaryPinController::class, 'create'])
    ->name('anniversary.pin');
Route::post('/anniversary/unlock', [AnniversaryPinController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('anniversary.unlock');

Route::middleware('anniversary.pin_verified')->group(function (): void {
    Route::get('/anniversary/puzzle', [AnniversaryPuzzleController::class, 'show'])
        ->name('anniversary.puzzle');
    Route::get('/anniversary/photo', [AnniversaryRevealController::class, 'photo'])
        ->name('anniversary.photo');
    Route::get('/anniversary/gallery/{photo}', [AnniversaryRevealController::class, 'galleryPhoto'])
        ->whereNumber('photo')
        ->name('anniversary.gallery.photo');
    Route::post('/anniversary/puzzle/move', [AnniversaryPuzzleController::class, 'move'])
        ->name('anniversary.puzzle.move');
    Route::post('/anniversary/puzzle/restart', [AnniversaryPuzzleController::class, 'restart'])
        ->name('anniversary.puzzle.restart');
    Route::post('/anniversary/puzzle/complete', [AnniversaryPuzzleController::class, 'complete'])
        ->name('anniversary.puzzle.complete');
});

Route::get('/anniversary/surprise', [AnniversaryRevealController::class, 'show'])
    ->middleware('anniversary.puzzle_completed')
    ->name('anniversary.surprise');

Route::post('/anniversary/replay', [AnniversaryRevealController::class, 'replay'])
    ->name('anniversary.replay');
