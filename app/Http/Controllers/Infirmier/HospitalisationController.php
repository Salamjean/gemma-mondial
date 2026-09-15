<?php

namespace App\Http\Controllers\Infirmier;

use App\Http\Controllers\Controller;
use App\Repositories\Doctor\HospitalisationRepository;

class HospitalisationController extends Controller
{
    public function in_progress()
    {
        $hospitalisations = (new HospitalisationRepository())->in_progress();
        return view('users.infirmier.hospitalisation.list', [
            'hospitalisations' => $hospitalisations,
            'activeTab' => 'in_progress',
            'pageTitle' => 'Liste des hospitalisations en cours'
        ]);
    }

    public function history()
    {
        $hospitalisations = (new HospitalisationRepository())->history();
        return view('users.infirmier.hospitalisation.list', [
            'hospitalisations' => $hospitalisations,
            'activeTab' => 'history',
            'pageTitle' => 'Historique des hospitalisations'
        ]);
    }
}
