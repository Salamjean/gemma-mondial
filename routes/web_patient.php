<?php

use App\Http\Controllers\Patient\AuthPatient;
use App\Http\Controllers\Patient\PatientDashboard;
use Illuminate\Support\Facades\Route;

Route::prefix('patient')->group(function(){
    Route::get('/login',[AuthPatient::class,'login'])->name('patient.login');
});

Route::middleware(['auth'])->group(function() {

    //Patient routes
    Route::middleware('isPatient')->group(function () {
        Route::prefix('patient')->name('patient.')->namespace('\App\Http\Controllers\Patient')->group(function () {

            Route::get('/dashboard', [PatientDashboard::class,'dashboard'])->name('patient.dashboard');
            Route::get('setting', 'PatientController@setting')->name('setting');
            Route::post('update', 'PatientController@update')->name('update');
            Route::get('consultations', 'PatientController@consultations')->name('consultations');
            Route::get('parcours/{id}', '\App\Http\Controllers\Doctor\PatientController@parcoursIntervention')->name('parcours');
            Route::get('declarations', 'PatientController@declarations')->name('declarations');
            Route::get('rendez-vous', 'PatientController@rendezVous')->name('rendez.vous');

            Route::get('impression/{type}/{id}', 'PatientController@Impression')->name('impression');

        });
    });

});
