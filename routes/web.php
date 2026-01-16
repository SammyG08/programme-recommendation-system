<?php

use App\ElectivesEnum;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgrammeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $electives = ElectivesEnum::cases();
    return view('welcome', compact('electives'));
})->name('home');
Route::post('/validate/core/inputs', [ProgrammeController::class, 'validateCoreInput'])->name('validate-core-input');
Route::post('/validate/electives/inputs', [ProgrammeController::class, 'validateElectiveInput'])->name('validate-electives-input');
Route::post('/programmes/recommended', [ProgrammeController::class, 'programmesRecommended'])->name('programmes-recommended');

Route::controller(AdminController::class)->group(function () {
    Route::get('admin', 'index')->name('admin')->middleware('auth');
});

Route::get('/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('process-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');