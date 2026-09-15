<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Infirmier\CareController;
use App\Http\Controllers\Infirmier\SuiviController;

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/hopital/pansements/courant', [CareController::class, 'getHopitalPansementsCourant'])->name('hopital.pansements.courant');
    Route::get('/hopital/pansements/complexe', [CareController::class, 'getHopitalPansementsComplexe'])->name('hopital.pansements.complexe');

    Route::get('/hopital/soins/respiratoire', [CareController::class, 'getHopitalSoinsRespiratoire'])->name('hopital.soins.respiratoire');
    Route::get('/hopital/soins/digestif', [CareController::class, 'getHopitalSoinsDigestif'])->name('hopital.soins.digestif');
    Route::get('/hopital/soins/genito-urinaire', [CareController::class, 'getHopitalSoinsGenitoUrinaire'])->name('hopital.soins.genito-urinaire');

    Route::middleware('isInfirmier')->group(function () {
        Route::prefix('infirmier')->name('infirmier.')->namespace('\App\Http\Controllers\Infirmier')->group(function () {

            //Profil
            Route::get('profile', 'InfirmierController@profile')->name('profile');
            Route::put('update', 'InfirmierController@update')->name('update');

            //care
            Route::prefix('care')->name('care.')->group(function () {
                Route::get('new', 'CareController@new')->name('new');
                Route::get('all', 'CareController@allCares')->name('all');
                Route::get('history', 'CareController@history')->name('history');
                Route::get('formulaire/{id}', 'CareController@formulaire')->name('formulaire');

                Route::post('store/{id}', 'CareController@store')->name('store');

                Route::get('detail/{id}', 'CareController@detail')->name('detail');


                //after payment is done
                Route::get('payment_success/{id}', 'CareController@payment_success')->name('payment_success');
                //Issue a payment
                Route::post('issue', 'CareController@issue')->name('issue');
                //ordonnance
                Route::post('ordonnance/{id}', 'CareController@ordonnance')->name('ordonnance');

                Route::get('imprimer/{id}', 'CareController@impression')->name('impression');
            });

            //consultation
            Route::prefix('consultation')->name('consultation.')->group(function () {
                Route::get('today', 'ConsultationController@today')->name('today');
                Route::get('all', 'ConsultationController@allPatients')->name('all');
                Route::get('history', 'ConsultationController@history')->name('history');
                Route::get('detail/{id}', 'ConsultationController@detail')->name('detail');
                Route::get('info/{id}', 'ConsultationController@infoPatient')->name('info');

                Route::get('formulaire/{id}', 'ConsultationController@formulaire')->name('formulaire');
                Route::get('doctors/{infirmierId}', 'InfirmierController@getDoctors')->name('doctors');
                Route::post('formulaire', 'ConsultationController@store')->name('formulaire.store');

                Route::get('formulaire/issue/{id}', 'ConsultationController@formulaireIssue')->name('formulaire.issue');
                Route::get('patient/{id}/card', 'ConsultationController@patientCard')->name('patient.card');
            });
            //suivi hospitalisation
            Route::prefix('suivi')->name('suivi.')->group(function () {
                Route::get('/patient/{id}', [SuiviController::class, 'suiviePatient'])->name('hospitalisation');
                Route::post('/applique/protocol/{id}', [SuiviController::class, 'appliqueProtocol'])->name('applique');
                Route::post('/make/surveillance', [SuiviController::class, 'makeSuveillance'])->name('surveillance');
            });

            //hospitalisation list
            Route::prefix('hospitalisation')->name('hospitalisation.')->group(function () {
                Route::get('in_progress', 'HospitalisationController@in_progress')->name('in_progress');
                Route::get('history', 'HospitalisationController@history')->name('history');
            });
        });


    });
});
