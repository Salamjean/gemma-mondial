<?php

namespace App\Repositories\Accountant;

use App\Models\Admission;
use App\Models\Caissiere;
use App\Models\Consultation;
use Illuminate\Support\Facades\DB;

class RecetteRepository

{
    public function __construct()
    {
        //
    }

    public function list()
    {

        $consultations = Consultation::where('hospital_id', auth()->user()->accountant->hospital_id)
            ->select(DB::raw('DATE(date_consultation) as jour'), DB::raw('COUNT(*) as nb'), DB::raw('SUM(montant) as somme'), DB::raw('MAX(created_at) as latest_created_at'))
            ->groupBy(DB::raw('DATE(date_consultation)'))
            ->orderByDesc('latest_created_at')
            ->paginate(10);

        return $consultations;
    }

    public function today()
    {
        $consultations = Consultation::where('hospital_id', auth()->user()->accountant->hospital_id)
            ->select(DB::raw('DATE(date_consultation) as jour'), DB::raw('COUNT(*) as nb'), DB::raw('SUM(montant) as somme'), DB::raw('MAX(created_at) as latest_created_at'))
            ->groupBy(DB::raw('DATE(date_consultation)'))
            ->whereDate('date_consultation', today()) 
            ->orderByDesc('latest_created_at')
            ->get();

        return $consultations;
    }

    public function day($day)
    {
        $admissions = Admission::join('caissieres', 'admissions.caissiere_id', 'caissieres.id')
            ->join('users', 'caissieres.user_id', 'users.id')
            ->where('admissions.hospital_id', auth()->user()->accountant->hospital_id)
            ->whereDate('admissions.created_at', $day)
            ->where('admissions.statut_paiement', 1)
            ->groupBy('admissions.caissiere_id', 'caissieres.id', 'caissieres.matricule', 'users.id', 'users.name')
            ->selectRaw('COUNT(*) AS nb, SUM(admissions.montant) AS sum,  SUM(admissions.montant_normal) AS sum_normal, caissieres.matricule, users.name')
            ->get();
        return $admissions;
    }
    public function detail($day, $id)
    {
        return Caissiere::whereId($id)->whereHas('admissions', function ($query) use ($day) {
            $query->where('date_paiement', $day)->where('statut_paiement', 1);
        })->first();
    }
}
