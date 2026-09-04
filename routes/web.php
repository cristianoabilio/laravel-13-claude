<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PayoutRequestController as AdminPayoutRequestController;
use App\Http\Controllers\Admin\SpecialitiesController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Doctor\BankAccountController;
use App\Http\Controllers\Doctor\DoctorAppointmentController;
use App\Http\Controllers\Doctor\DoctorBusinessHourController;
use App\Http\Controllers\Doctor\DoctorClinicController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Doctor\DoctorEducationController;
use App\Http\Controllers\Doctor\DoctorExperienceController;
use App\Http\Controllers\Doctor\DoctorServiceController;
use App\Http\Controllers\Doctor\PayoutRequestController;
use App\Http\Controllers\Frontend\AppointmentController;
use App\Http\Controllers\Frontend\DoctorController as FrontendDoctorController;
use App\Http\Controllers\Frontend\FavoriteController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Doctor\PrescriptionController;
use App\Http\Controllers\Patient\MedicalRecordController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/doctor/{doctorId}', [FrontendDoctorController::class, 'details'])->whereNumber('doctorId')->name('doctor.details');
Route::get('/specialities/{speciality}/doctors', [FrontendDoctorController::class, 'speciality'])->name('doctor.all.speciality');

Route::get('/dashboard', function () {
    return view('patient.index');
})->middleware(['auth', 'verified', 'role:patient'])->name('dashboard');

Route::middleware(['auth', 'role:patient'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/patient.logout', [PatientController::class, 'logout'])->name('patient.logout');
    Route::get('/patient/settings', [PatientController::class, 'settings'])->name('patient.settings');
    Route::get('/patient/invoices', [PatientController::class, 'invoices'])->name('patient.invoices');
    Route::get('/patient/favorites', [PatientController::class, 'favorites'])->name('patient.favorites');
    Route::put('/patient/settings', [PatientController::class, 'updateSettings'])->name('patient.settings.update');
    Route::delete('/patient/settings/photo', [PatientController::class, 'removeProfilePhoto'])->name('patient.settings.photo.destroy');
    Route::get('/patient/change-password', [PatientController::class, 'changePassword'])->name('patient.change_password');
    Route::put('/patient/change-password', [PatientController::class, 'updatePassword'])->name('patient.change_password.update');
    Route::get('/patient/appointments', [PatientController::class, 'appointments'])->name('patient.appointments');
    Route::get('/patient/medical-appointments', [PatientController::class, 'medicalAppointments'])->name('patient.medical_appointments');
    Route::post('/patient/medical-records', [MedicalRecordController::class, 'store'])->name('patient.medical_records.store');
    Route::put('/patient/medical-records/{medicalRecord}', [MedicalRecordController::class, 'update'])->name('patient.medical_records.update');
    Route::delete('/patient/medical-records/{medicalRecord}', [MedicalRecordController::class, 'destroy'])->name('patient.medical_records.destroy');

    Route::post('/doctor/{doctorId}/favorite', [FavoriteController::class, 'toggle'])->whereNumber('doctorId')->name('doctor.favorite.toggle');

    // Booking wizard - only logged-in patients may book. Guests get redirected
    // to login and doctors/admins are blocked by the role:patient middleware.
    Route::get('/doctor/booking/{doctorId}', [FrontendDoctorController::class, 'booking'])->whereNumber('doctorId')->name('doctor.booking');
    Route::post('/doctor/booking/{doctorId}', [AppointmentController::class, 'store'])->whereNumber('doctorId')->name('booking.store');
    Route::get('/doctor/booking/{doctorId}/slots', [AppointmentController::class, 'loadSlots'])->whereNumber('doctorId')->name('booking.slots');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'confirmation'])->name('appointments.confirmation');
});

// Shared with the role:doctor group below - both the patient and the treating
// doctor on an appointment may download its invoice, so this sits outside
// either role-specific group with ownership checked inside the controller.
Route::middleware('auth')->group(function () {
    Route::get('/appointments/{appointment}/invoice', [AppointmentController::class, 'downloadInvoice'])->name('appointments.invoice.download');
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
    Route::patch('/api/doctor/profile/availability', [DoctorController::class, 'updateAvailability'])->name('doctor.profile.availability.update');
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
    Route::get('/doctor/requests', [DoctorAppointmentController::class, 'requests'])->name('doctor.requests');
    Route::patch('/doctor/requests/{appointment}/accept', [DoctorAppointmentController::class, 'accept'])->name('doctor.requests.accept');
    Route::patch('/doctor/requests/{appointment}/reject', [DoctorAppointmentController::class, 'reject'])->name('doctor.requests.reject');
    Route::patch('/doctor/appointments/{appointment}/complete', [DoctorAppointmentController::class, 'complete'])->name('doctor.appointments.complete');

    Route::get('/doctor/specialities', [DoctorController::class, 'specialities'])->name('doctor.specialities');
    Route::get('/doctor/appointments', [DoctorController::class, 'appointments'])->name('doctor.appointments');
    Route::get('/doctor/patients', [DoctorController::class, 'patients'])->name('doctor.patients');
    Route::get('/patients/details/{patient}', [DoctorController::class, 'patientDetails'])->name('patient.details');
    Route::get('/doctor/invoices', [DoctorController::class, 'invoices'])->name('doctor.invoices');
    Route::get('/doctor/accounts', [DoctorController::class, 'accounts'])->name('doctor.accounts');
    Route::put('/doctor/bank-account', [BankAccountController::class, 'update'])->name('doctor.bank_account.update');
    Route::post('/doctor/payout-requests', [PayoutRequestController::class, 'store'])->name('doctor.payout_requests.store');
    Route::post('/patients/{patient}/prescriptions', [PrescriptionController::class, 'store'])->name('doctor.prescriptions.store');
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

    Route::get('/admin/payout-requests', [AdminPayoutRequestController::class, 'index'])->name('admin.payout_requests.index');
    Route::patch('/admin/payout-requests/{payoutRequest}/approve', [AdminPayoutRequestController::class, 'approve'])->name('admin.payout_requests.approve');
    Route::patch('/admin/payout-requests/{payoutRequest}/cancel', [AdminPayoutRequestController::class, 'cancel'])->name('admin.payout_requests.cancel');
});

require __DIR__.'/auth.php';
