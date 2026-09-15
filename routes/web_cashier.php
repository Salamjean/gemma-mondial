<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cashier\AdmissionController;
use App\Http\Controllers\Cashier\CashierController;
use App\Http\Controllers\Cashier\RecetteController;



Route::middleware(['auth'])->group(function() {

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    //Caisse routes
    Route::middleware('isCashier')->group(function () {
        Route::prefix('cashier')->name('cashier.')->namespace('\App\Http\Controllers\Cashier')->group(function () {

            Route::get('profile', [CashierController::class, 'profile'])->name('profile');
            Route::post('update', [CashierController::class, 'update'])->name('update');
            Route::get('planning', [CashierController::class, 'planning'])->name('planning');

            Route::prefix('admission')->name('admission.')->group(function () {
                Route::get('list', 'AdmissionController@list')->name('list');
                Route::get('admission', 'AdmissionController@admission')->name('admission');
                Route::get('hospitalisation', 'AdmissionController@hospitalisation')->name('hospitalisation');
                Route::get('all', 'AdmissionController@all')->name('all');
                Route::get('show/{id}', 'AdmissionController@show')->name('show');
                Route::get('details/{id}', 'AdmissionController@details')->name('details');

                Route::get('indicate/{day}', 'AdmissionController@indicate')->name('indicate');

            });

            Route::prefix('payment')->name('payment.')->group(function () {
                Route::put('validated/{id}', 'AdmissionController@validated')->name('validated');
                Route::get('today', 'PaymentController@today')->name('today');
                Route::get('valid', 'PaymentController@valid')->name('valid');
                Route::get('history', 'PaymentController@history')->name('history');
                Route::get('imprimer/{id}', [AdmissionController::class, 'Impression'])->name('imprimer');
            });

            Route::prefix('assurance')->name('assurance.')->group(function () {
                Route::get('today', 'AssuranceController@today')->name('today');
                Route::get('history', 'AssuranceController@history')->name('history');
                Route::get('show/{id}', 'AssuranceController@show')->name('show');
            });

            //recette
            Route::get('recette/{day}', 'RecetteController@list')->name('recette');

            Route::prefix('recette')->name('recette.')->group(function () {
                Route::get('list',[RecetteController::class, 'list'])->name('list');
                Route::get('day/{day}',[RecetteController::class, 'day'])->name('day');

                Route::get('print/{day}', [RecetteController::class, 'pdf'])->name('pdf');

            });

        });
    });

});
