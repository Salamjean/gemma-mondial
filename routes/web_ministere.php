<?php

use Illuminate\Support\Facades\Route;

// Routes publiques / Projection Grand Écran & TV Salle de Contrôle 24/7 (ne s'expire jamais)
Route::namespace('\App\Http\Controllers\Ministere')->group(function () {
    Route::get('/ministere/live', 'MinistereDashboardController@live')->name('ministere.live');
    Route::get('/live-ministere', 'MinistereDashboardController@live')->name('ministere.live.shortcut');
    Route::get('/live-sante', 'MinistereDashboardController@live')->name('ministere.live.sante');
    Route::get('/live-ministere/data', 'MinistereDashboardController@ajaxStats')->name('ministere.live.data');
});

Route::middleware(['auth'])->group(function() {

    Route::middleware('isMinistere')->group(function () {
        Route::prefix('ministere')->name('ministere.')->namespace('\App\Http\Controllers\Ministere')->group(function () {

            // Tableau de bord principal avec graphiques statistiques
            Route::get('/', 'MinistereDashboardController@dashboard')->name('dashboard');
            Route::get('/ajax-stats', 'MinistereDashboardController@ajaxStats')->name('ajax_stats');

            // Registres nationaux
            Route::get('/naissances', 'MinistereDashboardController@naissances')->name('naissances');
            Route::get('/deces', 'MinistereDashboardController@deces')->name('deces');

            // Hôpitaux et centres déclarants
            Route::get('/hopitaux', 'MinistereDashboardController@hospitals')->name('hopitaux');

        });
    });

});
