<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('profile.index');
});

Route::get('table-profile', [\App\Http\Controllers\ProfileController::class, 'getDataProfile'])
    ->name('table-profile'); /** Route untuk memuat data Json dari tabel profile */

Route::post('profile', [\App\Http\Controllers\ProfileController::class, 'store']);
Route::get('profile-edit/{id}/edit', [\App\Http\Controllers\ProfileController::class, 'edit']);
Route::post('profile-update/{id}', [\App\Http\Controllers\ProfileController::class, 'update']);
Route::delete('delete-profile/{id}', [\App\Http\Controllers\ProfileController::class, 'destroy']);
