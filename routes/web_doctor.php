<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\PostConsultationController;

Route::get('pdf', [PostConsultationController::class, 'viewPdf']);
Route::middleware(['auth'])->group(function () {
    //Agent routes
    Route::middleware('isDoctor')->group(function () {
        Route::prefix('doctor')->name('doctor.')->namespace('\App\Http\Controllers\Doctor')->group(function () {
            Route::get('profile', 'DoctorController@profile')->name('profile');
            Route::post('update', 'DoctorController@update')->name('update');

            Route::get('services', [DoctorController::class, 'getServices'])->name('services');
            // Liste des services de l'hopital //
            Route::get('hopital/services', [DoctorController::class, 'getHopitalServices'])->name('hopitalservices');

            Route::get('doctors/{prestations}', [DoctorController::class, 'getDoctors'])->name('doctors');
            Route::get('prestations/{service}', [DoctorController::class, 'getPrestations'])->name('prestations');
            Route::get('infirmiers/{service}', [DoctorController::class, 'getInfirmiers'])->name('infirmiers');
            Route::get('prix_prestation', [DoctorController::class, 'getPrixPrestation'])->name('prix_prestation');
            Route::get('prestation_services', [DoctorController::class, 'getPrestationServices'])->name('prestation_services');

            //consultation
            Route::prefix('consultation')->name('consultation.')->group(function () {
                //view consultation
                Route::get('today', 'ConsultationController@today')->name('today');
                Route::get('all', 'ConsultationController@allPatients')->name('all');
                Route::get('history', 'ConsultationController@history')->name('history');
                //Route::get('detail/{id}', 'ConsultationController@detailconsulation')->name('detailConsul');
                Route::get('detail/{id}', 'ConsultationController@detail')->name('detail');
                Route::get('info/{id}', 'ConsultationController@infoPatient')->name('info');
                Route::get('patient/{id}/card', 'ConsultationController@patientCard')->name('patient.card');
                //formulaire consultation
                Route::get('formulaire/{id}', 'ConsultationController@formulaire')->name('formulaire');
                Route::post('call/start/{id}', 'ConsultationController@startCall')->name('call.start');
                Route::post('call/end/{id}', 'ConsultationController@endCall')->name('call.end');
                Route::get('call/status/{id}', 'ConsultationController@callStatus')->name('call.status');
                Route::get('call/history', 'ConsultationController@callHistory')->name('call.history');
                Route::get('online/patient-info/{id}', 'ConsultationController@onlinePatientInfo')->name('online.patient.info');


                //store consultation
                Route::prefix('formulaire')->name('store.')->group(function () {
                    Route::post('accouchement', 'ConsultationController@storeAccouchement')->name('accouchement');
                    Route::post('post-natale', 'ConsultationController@storePostNatale')->name('post.natale');
                    Route::post('pre-natale', 'ConsultationController@storePreNatale')->name('pre.natale');
                    Route::post('curative', 'ConsultationController@storeConsultationCurative')->name('curative');

                });

                //formulaire issue consultation
                Route::get('formulaire_issue/{title}/{issue}/{id}', 'ConsultationController@formulaireIssue')->name('formulaire.issue');
                //issue consultation
                Route::prefix('issue')->name('store.issue.')->group(function () {

                    Route::post('justification', 'IssueController@storeIssueJustification')->name('justification');
                    Route::post('deces', 'IssueController@storeIssueDeces')->name('deces');

                    Route::post('hospitalisation/{idConsultation}', 'IssueController@storeIssueHospitalisation')->name('hospitalisation');

                });


            });

            //Hospitalisation
            Route::prefix('hospitalisation')->name('hospitalisation.')->group(function () {

                //list
                Route::get('all', 'HospitalisationController@all')->name('all');
                Route::get('in_progress', 'HospitalisationController@in_progress')->name('in_progress');
                Route::get('pending_room', 'HospitalisationController@pending_room')->name('pending_room');
                Route::get('history', 'HospitalisationController@history')->name('history');

                //follow up
                Route::get('edit/{id}', 'HospitalisationController@edit')->name('edit');
                Route::get('protocol/{type}/edit/{id}', 'HospitalisationController@protocolPage')->name('edit.protocol');

                Route::post('update_new_day/{id}', 'HospitalisationController@update_new_day')->name('update.new');
                Route::post('update_already/{id}', 'HospitalisationController@update_already')->name('update.already');
                Route::get('validate/{id}', 'HospitalisationController@validated')->name('validate');
                Route::get('manufacturing/{id}', 'HospitalisationController@manufacturing')->name('manufacturing');
                Route::get('download-facture/{id}', 'HospitalisationController@downloadFacture')->name('download_facture');

                //pdf file
                Route::get('detail/{id}', 'HospitalisationController@detail')->name('detail');
                Route::get('ordonnances/{id}', 'HospitalisationController@ordonnances')->name('ordonnances');


                //ajax request
                Route::get('bedroom/{type}', 'HospitalisationController@bedroom')->name('bedroom');
                Route::get('bed/{id}', 'HospitalisationController@bed')->name('bed');
                Route::get('infirmiers', 'HospitalisationController@infirmiers')->name('infirmiers');
                Route::get('drugs', 'HospitalisationController@drugs')->name('drugs');
                Route::get('drugsData', 'HospitalisationController@drugsData')->name('drugsData');

            });


            //patient
            Route::prefix('patient')->name('patient.')->group(function () {

                Route::get('list', 'PatientController@list')->name('list');
                Route::get('show/{id}', 'PatientController@show')->name('detail');
                Route::get('dossier_medical/{id}', 'PatientController@dossierMedical')->name('dossier_medical');
                Route::get('parcours/{id}', 'PatientController@parcoursIntervention')->name('parcours');
            });

            //declaration
            Route::middleware('isChief')->prefix('declaration')->name('declaration.')->group(function () {

                Route::get('search', 'DeclarationController@searchPatient')->name('search');

                //deces
                Route::prefix('deces')->name('deces.')->group(function () {
                    Route::get('list', 'DeclarationController@listDeces')->name('list');
                    Route::get('add/{person}', 'DeclarationController@addDeces')->name('add');
                    Route::get('show/{id}', 'DeclarationController@showDeces')->name('show');
                    Route::post('store/patient', 'DeclarationController@storeDeces')->name('store.patient');
                    Route::post('store/enfant', 'DeclarationController@storeDeces')->name('store.enfant');
                });

                //naissance
                Route::prefix('naissance')->name('naissance.')->group(function () {
                    Route::get('list', 'DeclarationController@listNaissance')->name('list');
                    Route::get('add', 'DeclarationController@addNaissance')->name('add');
                    Route::get('show/{id}', 'DeclarationController@showNaissance')->name('show');
                    Route::post('store', 'DeclarationController@storeNaissance')->name('store');
                });

                //pdf
                Route::prefix('certificat')->name('certificat.')->group(function () {
                    Route::get('naissance/{id}', 'DeclarationController@certificatNaissance')->name('naissance');
                    Route::get('deces/{id}', 'DeclarationController@certificatDeces')->name('deces');
                });

            });

        });
    });
});
