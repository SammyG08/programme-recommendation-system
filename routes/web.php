<?php

use App\Http\Controllers\ProgrammeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/validate/core/inputs', [ProgrammeController::class, 'validateCoreInput'])->name('validate-core-input');
Route::post('/validate/electives/inputs', [ProgrammeController::class, 'validateElectiveInput'])->name('validate-electives-input');
Route::post('/programmes/recommended', [ProgrammeController::class, 'programmesRecommended'])->name('programmes-recommended');