<?php

namespace App\Http\Controllers\Api;

use App\Models\Patient;
use App\Http\Controllers\Controller;

class PatientController extends Controller
{
    public function allPatients()
    {
        $patients = Patient::with('user')->with('mere')->with('secretariat')->with('doctor')->with('lieuNaissance')->get(); 
        return response()->json(['patients' => $patients]);
    }
    
}


