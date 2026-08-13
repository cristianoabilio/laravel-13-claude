<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SpecialitiesController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Doctor\DoctorBusinessHourController;
use App\Http\Controllers\Doctor\DoctorClinicController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Doctor\DoctorEducationController;
use App\Http\Controllers\Doctor\DoctorExperienceController;
use App\Http\Controllers\Doctor\DoctorServiceController;
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
});

Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor/logout', [DoctorController::class, 'logout'])->name('doctor.logout');
    Route::get('/doctor/dashboard', [DoctorController::class, 'index'])->name('doctor.dashboard');
    Route::get('/doctor/profile', [DoctorController::class, 'profile'])->name('doctor.profile');
    Route::put('/doctor/profile', [DoctorController::class, 'updateProfile'])->name('doctor.profile.update');
    Route::delete('/doctor/profile/photo', [DoctorController::class, 'removeProfilePhoto'])->name('doctor.profile.photo.destroy');
    // Under /api/ so it qualifies for the JSON exception rendering configured in
    // bootstrap/app.php (shouldRenderJsonWhen is scoped to "api/*"), even though
    // this is a session-authenticated web route, not the stateless API.
    Route::patch('/api/doctor/profile/languages', [DoctorController::class, 'updateLanguages'])->name('doctor.profile.languages.update');
    Route::get('/doctor/experience', [DoctorController::class, 'experience'])->name('doctor.experience');
    Route::put('/doctor/experiences', [DoctorExperienceController::class, 'update'])->name('doctor.experiences.update');
    Route::delete('/doctor/experiences/{experience}', [DoctorExperienceController::class, 'destroy'])->name('doctor.experiences.destroy');
    Route::delete('/doctor/experiences/{experience}/logo', [DoctorExperienceController::class, 'destroyLogo'])->name('doctor.experiences.logo.destroy');
    Route::get('/doctor/education', [DoctorController::class, 'education'])->name('doctor.education');
    Route::put('/doctor/educations', [DoctorEducationController::class, 'update'])->name('doctor.educations.update');
    Route::delete('/doctor/educations/{education}', [DoctorEducationController::class, 'destroy'])->name('doctor.educations.destroy');
    Route::delete('/doctor/educations/{education}/logo', [DoctorEducationController::class, 'destroyLogo'])->name('doctor.educations.logo.destroy');
    Route::get('/doctor/clinics', [DoctorController::class, 'clinics'])->name('doctor.clinics');
    Route::put('/doctor/clinics', [DoctorClinicController::class, 'update'])->name('doctor.clinics.update');
    Route::delete('/doctor/clinics/{clinic}', [DoctorClinicController::class, 'destroy'])->name('doctor.clinics.destroy');
    Route::delete('/doctor/clinics/{clinic}/logo', [DoctorClinicController::class, 'destroyLogo'])->name('doctor.clinics.logo.destroy');
    Route::delete('/doctor/clinics/images/{image}', [DoctorClinicController::class, 'destroyImage'])->name('doctor.clinics.images.destroy');
    Route::get('/doctor/business', [DoctorController::class, 'business'])->name('doctor.business');
    Route::put('/doctor/business', [DoctorBusinessHourController::class, 'update'])->name('doctor.business.update');

    Route::get('/doctor/specialities', [DoctorController::class, 'specialities'])->name('doctor.specialities');
    Route::put('/doctor/services', [DoctorServiceController::class, 'update'])->name('doctor.services.update');
    Route::delete('/doctor/services/speciality/{speciality}', [DoctorServiceController::class, 'destroySpeciality'])->name('doctor.services.speciality.destroy');
    Route::delete('/doctor/services/{doctorService}', [DoctorServiceController::class, 'destroy'])->name('doctor.services.destroy');

    Route::get('/doctor/change-password', [DoctorController::class, 'changePassword'])->name('doctor.change_password');
    Route::put('/doctor/change-password', [DoctorController::class, 'updatePassword'])->name('doctor.change_password.update');

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
