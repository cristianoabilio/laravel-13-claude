<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.index');
});

Route::get('/dashboard', function () {
    return view('patient.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/patient.logout', [PatientController::class, 'logout'])->name('patient.logout');
    Route::get('/doctor/dashboard', [DoctorController::class, 'index'])->name('doctor.dashboard');
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});

require __DIR__.'/auth.php';
