<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Repositories\Hospital\RecetteRepository;
use Illuminate\Http\Request;

class RecetteController extends Controller
{
    public function list()
    {
        $consultations = (new RecetteRepository())->list();
        return view('users.hospital.recette.list', ['consultations' => $consultations]);
    }

    public function day($day)
    {
        $data = (new RecetteRepository())->day($day);
        return view('users.hospital.recette.day', [
            'payments' => $data['payments'],
            'caissieres' => $data['caissieres'],
            'admissions' => $data['caissieres'],
            'day' => $day
        ]);
    }

    public function detail($day, $id)
    {
        $data = (new RecetteRepository())->detail($day, $id);
        return view('users.hospital.recette.detail', [
            'cashier' => $data['cashier'],
            'payments' => $data['payments'],
            'day' => $day
        ]);
    }
}
