<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function() {

    Route::middleware('isSuper')->group(function () {
        Route::prefix('super')->name('super.')->namespace('\App\Http\Controllers\Super')->group(function () {

            Route::get('profile', 'SuperController@index')->name('profile');
            Route::put('update', 'SuperController@update')->name('update');

            Route::prefix('hospital')->name('hospital.')->group(function () {

                Route::get('index', 'HospitalController@index')->name('index');
                Route::get('show/{id}', 'HospitalController@show')->name('show');
                Route::put('update/{id}', 'HospitalController@update')->name('update');
                Route::get('status/{id}', 'HospitalController@status')->name('status');
                Route::get('add', 'HospitalController@add')->name('add');
                Route::post('store', 'HospitalController@store')->name('store');
                Route::get('report/{id}', 'HospitalController@HospReport')->name('report');
                Route::get('activation/{id}', 'HospitalController@statusSce')->name('statusSce');
                Route::get('toggle-teleconsultation/{id}', 'HospitalController@toggleTeleconsultation')->name('toggle_teleconsultation');



            });

            Route::prefix('doctor')->name('doctor.')->group(function () {

                Route::get('index', 'DoctorController@index')->name('index');
                Route::get('search', 'DoctorController@search')->name('search');

            });

            Route::prefix('patient')->name('patient.')->group(function () {
                Route::get('index', 'PatientController@index')->name('index');
                Route::get('search', 'PatientController@search')->name('search');
            });

            Route::prefix('consultation')->name('consultation.')->group(function () {
                Route::get('index', 'ConsultationController@index')->name('index');
                Route::get('search', 'ConsultationController@search')->name('search');
            });

            Route::prefix('declaration')->name('declaration.')->group(function () {
                Route::get('death', 'DecesController@listDeces')->name('death');
                Route::get('birth', 'NaissanceController@listNaissance')->name('birth');
                Route::get('search/{type}', 'DeclarationController@search')->name('search');
            });

            Route::prefix('setting')->name('setting.')->group(function () {
                Route::get('icon', 'SettingController@icon')->name('icon');
                Route::post('icon', 'SettingController@logoIconUpdate')->name('updateIcon');
                Route::get('name', 'SettingController@name')->name('name');
                Route::get('bg', 'SettingController@bg')->name('bg');
            });

            Route::prefix('logs')->name('logs.')->group(function () {
                Route::get('/', 'LogController@index')->name('index');
                Route::post('clear-laravel', 'LogController@clearLaravelLogs')->name('clear_laravel');
                Route::get('download-laravel', 'LogController@downloadLaravelLog')->name('download_laravel');
                Route::get('export-audit-excel', 'LogController@exportAuditExcel')->name('export_audit_excel');
            });

        });
    });

});
