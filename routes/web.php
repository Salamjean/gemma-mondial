<?php

use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\PostConsultationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('accueil');
Route::get('/apropos', [\App\Http\Controllers\HomeController::class, 'about'])->name('apropos');
Route::get('impression/{type}/{id}', [\App\Http\Controllers\Patient\PatientController::class, 'Impression'])->name('impression');


// Auth::routes();
Route::get('/login', [CustomAuthController::class, 'index'])->name('index');
Route::post('/login', [CustomAuthController::class, 'login'])->name('login');
Route::post('/logout', [CustomAuthController::class, 'logout'])->name('logout');
Route::get('/register', [CustomAuthController::class, 'register'])->name('register');


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    //planning
    Route::middleware('isPersonal')->get('planning', [\App\Http\Controllers\PlanningController::class, 'index'])->name('planning');
    Route::middleware('isPersonal')->get('permission', [\App\Http\Controllers\PermissionController::class, 'index'])->name('permission');
    Route::middleware('isHospital')->get('permission/{status}', [\App\Http\Controllers\PermissionController::class, 'status'])->name('permission.status');
    Route::middleware('isHospital')->get('permission/{id}/{status}', [\App\Http\Controllers\PermissionController::class, 'update'])->name('permission.update');
    Route::middleware('isPersonal')->post('permission', [\App\Http\Controllers\PermissionController::class, 'store'])->name('permission.store');
    Route::middleware('isPersonal')->post('permission/user-update/{id}', [\App\Http\Controllers\PermissionController::class, 'userUpdate'])->name('permission.userUpdate');

    Route::middleware(['isDoctorOrInfirmier'])->group(function () {
        //post consultation
        Route::prefix('consultation/post')->name('consultation.store.post.')->group(function () {
            Route::post('ordonnance/{type}', [PostConsultationController::class, 'storePostOrdonnance'])->name('ordonnance');
            Route::post('bulletin', [PostConsultationController::class, 'storePostBulletinExamen'])->name('bulletin.examen');
            Route::post('arret_travail', [PostConsultationController::class, 'storePostArretTravail'])->name('arret.travail');
        });
        //imprimer post consultation
        Route::get('consultation/post/imprimer/{post}/{id}', [PostConsultationController::class, 'Impression'])->name('consultation.imprimer.post');
        
        // Consultations en ligne et appels instantanés
        Route::get('doctor/consultation/incoming-requests', [\App\Http\Controllers\Doctor\ConsultationController::class, 'getIncomingCallRequests'])->name('doctor.consultation.incoming_requests');
        Route::post('doctor/consultation/call/accept-patient-request/{id}', [\App\Http\Controllers\Doctor\ConsultationController::class, 'acceptPatientOnlineCall'])->name('doctor.consultation.accept_patient_call');
        Route::get('doctor/consultation/pending-online-requests', [\App\Http\Controllers\Doctor\ConsultationController::class, 'getPendingOnlineRequests'])->name('doctor.consultation.pending_online_requests');
        Route::post('doctor/consultation/pickup-pending/{id}', [\App\Http\Controllers\Doctor\ConsultationController::class, 'pickupPendingRequest'])->name('doctor.consultation.pickup_pending');
        Route::post('doctor/consultation/call/reject/{id}', [\App\Http\Controllers\Doctor\ConsultationController::class, 'rejectIncomingCall'])->name('doctor.consultation.reject_call');
    });

    Route::middleware(['isInfirmier', 'isDoctor', 'isHospital', 'isPatient'])->group(function () {});
});
