<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ManagerDashboardController;
use App\Http\Controllers\VoormanDashboardController;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\PlanningController;

Route::get('/', function () {
    return redirect()->route(Auth::check() ? (auth()->user()->role->name === 'manager' ? 'manager.dashboard' : 'voorman.dashboard') : 'login');
});
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'manager'])->group(function () {
    Route::get('/manager/dashboard', [ManagerDashboardController::class, 'index'])
        ->name('manager.dashboard');
});

Route::middleware(['auth', 'voorman'])->group(function () {
    // 1) tvoja postojeća ruta za prikaz dashboarda
    Route::get('/voorman/dashboard', [VoormanDashboardController::class, 'index'])
        ->name('voorman.dashboard');

    // 2) nova ruta za AJAX updateItem
    Route::post('/voorman/update/item', [VoormanDashboardController::class, 'updateItem'])
        ->name('voorman.update.item');
});
Route::middleware(['auth','werkvoorbereider'])
    ->group(function() {
        // Prikaz forme za sve hale, GET /planning/week
        Route::get('/planning/week', [PlanningController::class,'editWeekAll'])
            ->name('planning.week.edit');

        // Obrada, POST /planning/week
        Route::post('/planning/week', [PlanningController::class,'updateWeekAll'])
            ->name('planning.week.update');
    });
Route::middleware(['auth','werkvoorbereider'])
    ->get('/test-werk', function(){
        return 'Middleware ok!';
    });
