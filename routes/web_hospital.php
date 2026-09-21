<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    //Hopital routes
    Route::middleware('isHospital')->group(function () {
        Route::prefix('hospital')->name('hospital.')->namespace('\App\Http\Controllers\Hospital')->group(function () {

            Route::get('profile', 'HospitalController@index')->name('profile');
            Route::post('update', 'HospitalController@update')->name('update');
            Route::get('delete-watermark', 'HospitalController@deleteWatermark')->name('delete.watermark');

            // Écran TV Salle d'Attente H24 (Appel vocal des patients)
            Route::get('waiting-room-screen', 'WaitingScreenController@screen')->name('waiting_screen');
            Route::get('waiting-room-screen/updates', 'WaitingScreenController@getUpdates')->name('waiting_screen.updates');
            Route::get('waiting-room-screen/tts-audio', 'WaitingScreenController@getTtsAudio')->name('waiting_screen.tts');
            Route::post('waiting-room-screen/test', 'WaitingScreenController@testCall')->name('waiting_screen.test');

            Route::prefix('service')->name('service.')->group(function () {
                Route::get('index', 'ServiceController@index')->name('index');
                Route::get('show/{id}', 'ServiceController@show')->name('show');
                Route::get('add', 'ServiceController@add')->name('add');
                Route::post('store', 'ServiceController@store')->name('store');
                Route::put('update/{id}', 'ServiceController@update')->name('update');
                Route::get('status/{id}', 'ServiceController@status')->name('status');
                Route::get('delete/{id}', 'ServiceController@delete')->name('delete');


                Route::prefix('service')->name('service.')->group(function () {
                    Route::get('delete/{idService}', 'ServiceController@deleteService')->name('delete');

                    //ajax request
                    Route::get('search/{idService}', 'ServiceController@searchService')->name('search');
                });

                //ajax request
                Route::get('search/{iderviceHospital}', 'ServiceController@searchServiceServiceHospital')->name('data.service.search');
            });

            Route::prefix('type')->name('type.')->group(function () {

                //type doctor
                Route::prefix('doctor')->name('doctor.')->group(function () {
                    Route::get('index', 'TypeDoctorController@index')->name('index');
                    Route::get('show/{id}', 'TypeDoctorController@show')->name('show');
                    Route::get('add', 'TypeDoctorController@add')->name('add');
                    Route::post('store', 'TypeDoctorController@store')->name('store');
                    Route::put('update/{id}', 'TypeDoctorController@update')->name('update');
                    Route::get('status/{id}', 'TypeDoctorController@status')->name('status');
                    Route::get('delete/{id}', 'TypeDoctorController@delete')->name('delete');
                });

                //type consultation
                Route::prefix('consultation')->name('consultation.')->group(function () {
                    Route::get('index', 'TypeConsultationController@index')->name('index');
                    Route::get('show/{id}', 'TypeConsultationController@show')->name('show');
                    Route::get('create', 'TypeConsultationController@create')->name('create');
                    Route::post('store', 'TypeConsultationController@store')->name('store');
                    Route::put('update/{id}', 'TypeConsultationController@update')->name('update');
                    Route::get('status/{id}', 'TypeConsultationController@status')->name('status');
                    Route::get('delete/{id}', 'TypeConsultationController@delete')->name('delete');
                });

                //type examen
                Route::prefix('examen')->name('examen.')->group(function () {
                    Route::get('index', 'TypeExamenController@index')->name('index');
                    Route::get('show/{id}', 'TypeExamenController@show')->name('show');
                    Route::get('create', 'TypeExamenController@create')->name('create');
                    Route::post('store', 'TypeExamenController@store')->name('store');
                    Route::put('update/{id}', 'TypeExamenController@update')->name('update');
                    Route::get('status/{id}', 'TypeExamenController@status')->name('status');
                    Route::get('delete/{id}', 'TypeExamenController@delete')->name('delete');
                });

                //type assurance
                Route::prefix('assurance')->name('assurance.')->group(function () {
                    Route::get('index', 'TypeAssuranceController@index')->name('index');
                    Route::get('show/{id}', 'TypeAssuranceController@show')->name('show');
                    Route::get('create', 'TypeAssuranceController@create')->name('create');
                    Route::post('store', 'TypeAssuranceController@store')->name('store');
                    Route::put('update/{id}', 'TypeAssuranceController@update')->name('update');
                    Route::get('status/{id}', 'TypeAssuranceController@status')->name('status');
                    Route::get('delete/{id}', 'TypeAssuranceController@delete')->name('delete');
                });
            });

            Route::prefix('doctor')->name('doctor.')->group(function () {
                Route::get('index/sage', 'DoctorController@indexS')->name('index.sage');
                Route::get('index/medecin', 'DoctorController@index')->name('index.medecin');
                Route::get('show/{id}', 'DoctorController@show')->name('show');
                Route::get('add/{agent}', 'DoctorController@add')->name('add');
                Route::post('storeDoctor', 'DoctorController@storeDoctor')->name('storeDoctor');
                Route::post('storeSageF', 'DoctorController@storeSageF')->name('storeSageF');
                Route::put('update/{id}/{typeAgent}', 'DoctorController@update')->name('update');
                Route::get('status/{id}', 'DoctorController@status')->name('status');
                Route::get('badge/{id}', 'DoctorController@badge')->name('badge');
                Route::get('delete/{id}', 'DoctorController@delete')->name('delete');

                Route::get('chief/{id}', 'DoctorController@chief')->name('chief');
            });

            Route::prefix('infirmier')->name('infirmier.')->group(function () {
                //infirmier
                Route::get('index', 'InfirmierController@index')->name('index');
                Route::get('show/{id}', 'InfirmierController@show')->name('show');
                Route::get('add', 'InfirmierController@add')->name('add');
                Route::post('store', 'InfirmierController@store')->name('store');
                Route::put('update/{id}', 'InfirmierController@update')->name('update');
                Route::get('status/{id}', 'InfirmierController@status')->name('status');
                Route::get('delete/{id}', 'InfirmierController@delete')->name('delete');
            });

            Route::prefix('pharmacy')->name('pharmacy.')->group(function () {
                //pharmacy
                Route::get('index', 'PharmacyController@index')->name('index');
                Route::get('show/{id}', 'PharmacyController@show')->name('show');
                Route::get('add', 'PharmacyController@add')->name('add');
                Route::post('store', 'PharmacyController@store')->name('store');
                Route::put('update/{id}', 'PharmacyController@update')->name('update');
                Route::get('status/{id}', 'PharmacyController@status')->name('status');
                Route::get('delete/{id}', 'PharmacyController@delete')->name('delete');
            });

            Route::prefix('secretariat')->name('secretariat.')->group(function () {
                //secretariat
                Route::get('index', 'SecretariatController@index')->name('index');
                Route::get('show/{id}', 'SecretariatController@show')->name('show');
                Route::get('add', 'SecretariatController@add')->name('add');
                Route::post('store', 'SecretariatController@store')->name('store');
                Route::put('update/{id}', 'SecretariatController@update')->name('update');
                Route::get('status/{id}', 'SecretariatController@status')->name('status');
                Route::get('delete/{id}', 'SecretariatController@delete')->name('delete');
            });

            Route::prefix('cashier')->name('cashier.')->group(function () {
                //cashier
                Route::get('index', 'CashierController@index')->name('index');
                Route::get('show/{id}', 'CashierController@show')->name('show');
                Route::get('add', 'CashierController@add')->name('add');
                Route::post('store', 'CashierController@store')->name('store');
                Route::put('update/{id}', 'CashierController@update')->name('update');
                Route::get('status/{id}', 'CashierController@status')->name('status');
                Route::get('delete/{id}', 'CashierController@delete')->name('delete');
            });

            Route::prefix('accountant')->name('accountant.')->group(function () {
                //accountant
                Route::get('index', 'AccountantController@index')->name('index');
                Route::get('show/{id}', 'AccountantController@show')->name('show');
                Route::get('add', 'AccountantController@add')->name('add');
                Route::post('store', 'AccountantController@store')->name('store');
                Route::put('update/{id}', 'AccountantController@update')->name('update');
                Route::get('status/{id}', 'AccountantController@status')->name('status');
                Route::get('delete/{id}', 'AccountantController@delete')->name('delete');
            });

            //Chambre & Lits
            Route::prefix('bedroom')->name('bedroom.')->group(function () {
                //Bedroom
                Route::get('index', 'BedroomController@index')->name('index');
                Route::get('show/{id}', 'BedroomController@show')->name('show');
                Route::get('add', 'BedroomController@add')->name('add');
                Route::post('store', 'BedroomController@store')->name('store');
                Route::put('update/{id}', 'BedroomController@update')->name('update');
                Route::get('status/{id}', 'BedroomController@status')->name('status');
                Route::get('delete/{id}', 'BedroomController@delete')->name('delete');

                //Bed
                Route::prefix('bed')->name('bed.')->group(function () {
                    Route::post('store/{idBedRoom}', 'BedroomController@bedStore')->name('store');
                    Route::get('status/{idBed}', 'BedroomController@bedShow')->name('status');
                    Route::get('delete/{idBedRoom}', 'BedroomController@bedDelete')->name('delete');
                });
            });


            Route::get('grille', 'HospitalController@grille')->name('grille');

            Route::prefix('patient')->name('patient.')->group(function () {
                Route::get('index', 'PatientController@index')->name('index');
                Route::get('show/{id}', 'PatientController@show')->name('detail');
                Route::get('dossier_medical/{id}', 'PatientController@dossierMedical')->name('dossier_medical');
                Route::get('parcours/{id}', 'PatientController@parcoursIntervention')->name('parcours');
            });

            //consultation
            Route::prefix('consultation')->name('consultation.')->group(function () {

                //view consultation
                Route::get('today', 'ConsultationController@today')->name('today');
                Route::get('history', 'ConsultationController@history')->name('history');
                Route::get('detail/{id}', 'ConsultationController@detail')->name('detail');

                //imprimer post consultation
                Route::get('post/imprimer/{post}/{id}', 'ConsultationController@Impression')->name('imprimer.post');
            });
            //declaration
            Route::prefix('declaration')->name('declaration.')->group(function () {

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

            //Mise en observation
            Route::prefix('observation')->name('observation.')->group(function () {
                Route::get('list', 'ObservationController@list')->name('list');
                Route::get('detail/{id}', 'ObservationController@detail')->name('detail');
            });

            //Mise en hospitalisation
            Route::prefix('hospitalisation')->name('hospitalisation.')->group(function () {
                Route::get('list', 'HospitalisationController@list')->name('list');
                Route::get('detail/{id}', 'HospitalisationController@detail')->name('detail');
            });

            //Recette
            Route::prefix('recette')->name('recette.')->group(function () {
                Route::get('list', 'RecetteController@list')->name('list');
                Route::get('day/{date}', 'RecetteController@day')->name('day');
                Route::get('detail/{day}/{id}', 'RecetteController@detail')->name('detail');
            });
        });
    });
});
