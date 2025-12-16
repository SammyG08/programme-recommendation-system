<?php

use App\Http\Controllers\ProgrammeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/process/core/results', [ProgrammeController::class, 'processCoreResults'])->name('process-core');
Route::post('/process/electives/results', [ProgrammeController::class, 'processElectiveResults'])->name('process-electives');
