<?php

namespace App\Http\Controllers\Api\Ministere;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Ministere;
use App\Models\Consultation;
use App\Models\Hospitalisation;


class MinistereController extends Controller
{
    /** Affichage dees données du jour **/

    public function countPatientToday()
    {
        $today = Carbon::today();
        $count = Patient::whereDate('created_at', $today)->count(); 
        return response()->json(['count' => $count]);
    }
    public function countConsultationToday()
    {
        $today = Carbon::today();
        $count = Consultation::whereDate('created_at', $today)->count(); 
        return response()->json(['count' => $count]);
    }
    public function countPaiementToday()
    {
        $today = Carbon::today();
        $count = Consultation::whereDate('date_consultation', $today)
            ->where('status', 1)
            ->sum('montant');
        return response()->json(['count' => $count]);
    }
    public function countPatientHospitaliseToday()
    {
        $today = Carbon::today();
        $count = Hospitalisation::whereDate('created_at', $today)->count(); 
        return response()->json(['count' => $count]);
    }
}


