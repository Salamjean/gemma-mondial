<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Secretariat\ExamenController;
use App\Http\Controllers\Secretariat\PatientController;
use App\Http\Controllers\Secretariat\ProfileController;
use App\Http\Controllers\Secretariat\AdmissionController;
use App\Http\Controllers\Secretariat\SecretariatController;
use App\Http\Controllers\Secretariat\ConsultationController;
use App\Http\Controllers\Secretariat\HospitalisationController;
use App\Http\Controllers\Secretariat\SecretariatController as SecretariatSecretariatController;

Route::middleware(['auth'])->group(function () {

    //Secretaire routes
    Route::middleware('isSecretariat')->group(function () {
        Route::prefix('secretariat')->name('secretariat.')->namespace('\App\Http\Controllers\Secretariat')->group(function () {

            Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
            Route::post('update', [ProfileController::class, 'update'])->name('update');
            Route::get('userautosearch', [AdmissionController::class, 'userAutoSearch'])->name('userautosearch');

            Route::get('recherche-patient-hospitalisation', [SecretariatController::class, 'getPatientHospitalisation'])->name('recherche_patient_hospitalisation');
            Route::get('recherche-hospitalisation', [SecretariatController::class, 'searchHospitalisation'])->name('search_hospitalisation');

            Route::get('regions', [SecretariatController::class, 'getRegions'])->name('regions');
            Route::get('villes', [SecretariatController::class, 'getDepartments'])->name('departments');
            Route::get('communes', [SecretariatController::class, 'getSubPrefectures'])->name('sub_prefectures');

            Route::get('lieu_naissance', [SecretariatController::class, 'getLieuNaissance'])->name('lieu_naissance');
            Route::get('residence_actuelle', [SecretariatController::class, 'getResidenceActuelle'])->name('residence_actuelle');
            Route::get('residence_habituelle', [SecretariatController::class, 'getResidenceActuelle'])->name('residence_habituelle');
            // Liste des services de l'hopital //
            Route::get('services', [SecretariatController::class, 'getServices'])->name('services');
            // Liste des services de l'hopital //
            Route::get('hopital/services', [SecretariatController::class, 'getHopitalServices'])->name('hopitalservices');
            Route::get('medecins', [SecretariatController::class, 'getMedecins'])->name('medecins');
            Route::get('listepatients', [SecretariatController::class, 'listePatients'])->name('listepatients');
            Route::get('search', [SecretariatController::class, 'searchPatient'])->name('searchPatient');
            Route::get('search/patient', [PatientController::class, 'searchPatients'])->name('searchPatients');
            Route::get('doctors/{prestations}', [SecretariatController::class, 'getDoctors'])->name('doctors');
            Route::get('prestations/{service}', [SecretariatController::class, 'getPrestations'])->name('prestations');
            Route::get('infirmiers/{service}', [SecretariatController::class, 'getInfirmiers'])->name('infirmiers');
            Route::get('prix_prestation', [SecretariatController::class, 'getPrixPrestation'])->name('prix_prestation');
            Route::get('prestation_services', [SecretariatController::class, 'getPrestationServices'])->name('prestation_services');

            Route::get('prix_examen', [SecretariatController::class, 'getPrixExamen'])->name('prix_examen');
            Route::get('type_examens', [SecretariatController::class, 'getTypeExamens'])->name('type_examens');
            Route::get('availabilities', [SecretariatController::class, 'getAvailabilities'])->name('availabilities');

            Route::prefix('patient')->name('patient.')->group(function () {
                Route::get('search', [PatientController::class, 'searchPatients'])->name('search');
                Route::get('searchPatient', [SecretariatController::class, 'searchPatient'])->name('searchPatient');
                Route::get('list', [PatientController::class, 'list'])->name('list');
                Route::get('search/patient/code/{data}', [PatientController::class, 'searchPatientByCode'])->name('searchcode');
                Route::get('search/patient/fullname/{data}', [PatientController::class, 'searchPatientByFullName'])->name('searchfullname');
                Route::get('search/patient/codeassurance/{data}', [PatientController::class, 'searchPatientByNoAssurance'])->name('searchnoassurance');
                Route::get('add', [PatientController::class, 'add'])->name('add');
                Route::get('detail/{id}', [PatientController::class, 'detail'])->name('detail');
                Route::get('dossier_medical/{id}', [PatientController::class, 'dossierMedical'])->name('dossier_medical');
                Route::get('parcours/{id}', [PatientController::class, 'parcoursIntervention'])->name('parcours');
                Route::get('edit/{id}', [PatientController::class, 'edit'])->name('edit');
                Route::get('create', [PatientController::class, 'create'])->name('create');
                Route::post('addpatient', [PatientController::class, 'addPatient'])->name('addpatient');
                Route::put('updatepatient/{id}', [PatientController::class, 'updatePatient'])->name('updatepatient');
                Route::get('/show-patient/{id}', [PatientController::class, 'getPatient'])->name('showpatient');
                Route::get('card/{id}', [PatientController::class, 'card'])->name('card');
            });

            Route::prefix('admission')->name('admission.')->group(function () {
                Route::get('list', [AdmissionController::class, 'list'])->name('list');
                Route::get('search', [AdmissionController::class, 'search'])->name('search');
                Route::get('make', [AdmissionController::class, 'make'])->name('make');
                Route::get('today', [AdmissionController::class, 'today'])->name('today');
                Route::get('history', [AdmissionController::class, 'history'])->name('history');
                Route::get('detail/{id}', [AdmissionController::class, 'detail'])->name('detail');
                Route::post('add', [AdmissionController::class, 'addAdmission'])->name('add');
                Route::post('searchadmission', [AdmissionController::class, 'searchAdmission'])->name('searchadmission');
                Route::get('admission_invoice', [AdmissionController::class, 'admissionInvoice'])->name('admission_invoice');
                Route::get('imprimer/{id}', [AdmissionController::class, 'Impression'])->name('imprimer');
            });

            Route::prefix('examen')->name('examen.')->group(function () {
                Route::get('list', [ExamenController::class, 'list'])->name('list');
                Route::get('search', [ExamenController::class, 'search'])->name('search');
                Route::get('make', [ExamenController::class, 'make'])->name('make');
                Route::get('today', [ExamenController::class, 'today'])->name('today');
                Route::get('history', [ExamenController::class, 'history'])->name('history');
                Route::get('detail/{id}', [ExamenController::class, 'detail'])->name('detail');
                Route::post('add', [ExamenController::class, 'addAdmission'])->name('add');
                Route::post('searchadmission', [ExamenController::class, 'searchAdmission'])->name('searchadmission');
                Route::get('admission_invoice', [ExamenController::class, 'admissionInvoice'])->name('admission_invoice');

            });

            Route::prefix('consultation')->name('consultation.')->group(function () {
                Route::get('list', [ConsultationController::class, 'list'])->name('list');
                Route::get('search', [ConsultationController::class, 'search'])->name('search');
                Route::get('make', [ConsultationController::class, 'make'])->name('make');
                Route::get('today', [ConsultationController::class, 'today'])->name('today');
                Route::get('history', [ConsultationController::class, 'history'])->name('history');
                Route::get('detail/{id}', [ConsultationController::class, 'detail'])->name('detail');
                Route::post('add', [ConsultationController::class, 'addAdmission'])->name('add');
                Route::post('searchadmission', [ConsultationController::class, 'searchAdmission'])->name('searchadmission');
                Route::get('admission_invoice', [ConsultationController::class, 'admissionInvoice'])->name('admission_invoice');
            });

            Route::prefix('hospitalisation')->name('hospitalisation.')->group(function () {
                Route::get('in_progress', [HospitalisationController::class, 'in_progress'])->name('in_progress');
                Route::get('history', [HospitalisationController::class, 'history'])->name('history');
            });
        });
    });
});