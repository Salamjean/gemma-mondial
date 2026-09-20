<?php

namespace App\Repositories\Cashier;

use App\Models\Admission;
use App\Models\Caissiere;
use App\Models\Consultation;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecetteRepository
{
    public function __construct()
    {
        //
    }

    public function list()
    {
        $payments = Payment::where('caissiere_id', auth()->user()->cashier->id)
            ->where('status', 'success')
            ->select(
                DB::raw('DATE(created_at) as jour'),
                DB::raw('COUNT(*) as nb'),
                DB::raw('SUM(prix) as somme'),
                DB::raw('SUM(CASE WHEN mode_paiement = "espece" OR mode_paiement IS NULL THEN prix ELSE 0 END) as somme_espece'),
                DB::raw('SUM(CASE WHEN mode_paiement = "mobile_money" THEN prix ELSE 0 END) as somme_mobile_money'),
                DB::raw('COUNT(CASE WHEN mode_paiement = "espece" OR mode_paiement IS NULL THEN 1 END) as nb_espece'),
                DB::raw('COUNT(CASE WHEN mode_paiement = "mobile_money" THEN 1 END) as nb_mobile_money'),
                DB::raw('MAX(created_at) as latest_created_at')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc('jour')
            ->get();

        return $payments;
    }

    public function day($day)
    {
        $payments = Payment::where('caissiere_id', auth()->user()->cashier->id)
            ->where('status', 'success')
            ->whereDate('created_at', $day)
            ->with([
                'admission.patient.user',
                'admission.prestationHospital.prestationService',
                'hospitalisation.consultation.patient.user',
                'typeAssurance'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return $payments;
    }

    public function detail($day)
    {
        return Admission::where('caissiere_id', Auth::user()->cashier->id)
            ->whereDate('date_admission', $day ?? date('Y-m-d'))
            ->where('statut_paiement', 1)
            ->get();
    }
}
