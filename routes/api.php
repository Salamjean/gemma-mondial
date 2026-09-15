<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\OneSignalTokenController;
use App\Http\Controllers\api\Patient\DataController;
use App\Http\Controllers\api\ContactController;
use App\Http\Controllers\Api\FingerprintController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\Ministere\MinistereController;
use App\Http\Controllers\api\PatientController;
use App\Http\Controllers\Api\RealFingerprintController;
use App\Http\Controllers\BiometricController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1/patient')->group(
    function () {

        Route::post('/sendMail', [ContactController::class, 'store']);
        // guest admin
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/confirm', [AuthController::class, 'confirmLogin']);
        //Liste des patients 
        Route::get('list', [PatientController::class, 'allPatients']);

        Route::get('cities', [DataController::class, 'cities']);

        Route::group(['middleware' => ['auth:sanctum']], function () {

            Route::post('update', [DataController::class, 'update']);
            Route::get('show', [DataController::class, 'show']);
            Route::post('one-signal/token', [OneSignalTokenController::class, 'store']);

            Route::get('consultations', [DataController::class, 'consultations']);
            Route::get('declarations', [DataController::class, 'declarations']);
            Route::post('rdv/create', [DataController::class, 'createRendezVous']);
            Route::get('rdv', [DataController::class, 'rendezVous']);
            Route::get('doctors', [DataController::class, 'getDoctors']);
            Route::get('services', [DataController::class, 'getServices']);
            Route::get('pending-paid-consultation', [DataController::class, 'getPendingPaidConsultation']);
            Route::get('active-call', [DataController::class, 'checkActiveCall']);
            Route::post('accept-call', [DataController::class, 'acceptCall']);
            Route::post('reject-call', [DataController::class, 'rejectCall']);
            Route::post('end-call', [DataController::class, 'endCall']);
            Route::get('calls/history', [DataController::class, 'callHistory']);
            Route::post('online-consultation/request', [DataController::class, 'requestOnlineConsultation']);
            Route::get('online-consultation/status/{id}', [DataController::class, 'checkOnlineConsultationStatus']);
            Route::get('verify-wave-payment/{id}', [DataController::class, 'verifyWavePayment']);

            Route::get('rdv/{id}', [DataController::class, 'deleteRendezVous']);
        });

        Route::post('wave/webhook', [DataController::class, 'waveWebhook']);
    }
);

Route::prefix('ministere')->group(
    function () {
        Route::get('/patients/today', [MinistereController::class, 'countPatientToday']);
        Route::get('/consultations/today', [MinistereController::class, 'countConsultationToday']);
        Route::get('/paiements/today', [MinistereController::class, 'countPaiementToday']);
        Route::get('/patients/hospitalise/today', [MinistereController::class, 'countPatientHospitaliseToday']);
    }
);

    Route::post('/auth', [FingerprintController::class, 'authenticate']);
    Route::post('/capture', [FingerprintController::class, 'capture']);
    Route::get('/config', [FingerprintController::class, 'getConfig']);
    Route::post('/test', [FingerprintController::class, 'testConnection']);


    Route::prefix('fingerprint')->group(function () {
    Route::post('/real/capture', [RealFingerprintController::class, 'startCapture']);
    Route::get('/real/status', [RealFingerprintController::class, 'checkScannerStatus']);
    Route::get('/real/config', [RealFingerprintController::class, 'getPusherConfig']);
    Route::post('/real/save', [RealFingerprintController::class, 'saveToPatient']);
    
    // Authentification Pusher
    Route::post('/broadcast/auth', function (Request $request) {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Utiliser l'authentification intégrée de Laravel
        return Broadcast::auth($request);
    });
});
