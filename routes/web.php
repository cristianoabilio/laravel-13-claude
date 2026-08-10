<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SpecialitiesController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('frontend.index');
});

Route::get('/dashboard', function () {
    return view('patient.index');
})->middleware(['auth', 'verified', 'role:patient'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/patient.logout', [PatientController::class, 'logout'])->name('patient.logout');
    Route::get('/doctor/dashboard', [DoctorController::class, 'index'])->middleware('role:doctor')->name('doctor.dashboard');
});

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');

    Route::resource('admin/specialities', SpecialitiesController::class)
        ->except(['show', 'create', 'edit'])
        ->names('admin.specialities');
});

require __DIR__.'/auth.php';
