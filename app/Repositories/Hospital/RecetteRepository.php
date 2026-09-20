<?php

namespace App\Repositories\Hospital;

use App\Models\Admission;
use App\Models\Caissiere;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class RecetteRepository
{
    public function __construct()
    {
        //
    }

    public function list()
    {
        $hospitalId = auth()->user()->hospital->id;

        $consultations = Payment::where('payments.hospital_id', $hospitalId)
            ->where('payments.status', 'success')
            ->select(
                DB::raw('DATE(payments.created_at) as jour'),
                DB::raw('COUNT(*) as nb'),
                DB::raw('SUM(payments.prix) as somme'),
                DB::raw('SUM(CASE WHEN payments.mode_paiement = "espece" OR payments.mode_paiement IS NULL THEN payments.prix ELSE 0 END) as somme_espece'),
                DB::raw('SUM(CASE WHEN payments.mode_paiement = "mobile_money" THEN payments.prix ELSE 0 END) as somme_mobile_money'),
                DB::raw('COUNT(CASE WHEN payments.mode_paiement = "espece" OR payments.mode_paiement IS NULL THEN 1 END) as nb_espece'),
                DB::raw('COUNT(CASE WHEN payments.mode_paiement = "mobile_money" THEN 1 END) as nb_mobile_money'),
                DB::raw('MAX(payments.created_at) as latest_created_at')
            )
            ->groupBy(DB::raw('DATE(payments.created_at)'))
            ->orderByDesc('jour')
            ->get();

        return $consultations;
    }

    public function day($day)
    {
        $hospitalId = auth()->user()->hospital->id;

        $payments = Payment::where('payments.hospital_id', $hospitalId)
            ->where('payments.status', 'success')
            ->whereDate('payments.created_at', $day)
            ->with([
                'cashier.user',
                'admission.patient.user',
                'admission.prestationHospital.prestationService',
                'hospitalisation.consultation.patient.user',
                'typeAssurance'
            ])
            ->orderBy('payments.created_at', 'desc')
            ->get();

        $caissieres = Payment::join('caissieres', 'payments.caissiere_id', '=', 'caissieres.id')
            ->join('users', 'caissieres.user_id', '=', 'users.id')
            ->where('payments.hospital_id', $hospitalId)
            ->where('payments.status', 'success')
            ->whereDate('payments.created_at', $day)
            ->groupBy('payments.caissiere_id', 'caissieres.id', 'caissieres.matricule', 'users.name', 'users.prenom')
            ->select(
                'caissieres.matricule',
                'caissieres.id as caissiere_id',
                'users.name',
                'users.prenom',
                DB::raw('COUNT(*) as nb'),
                DB::raw('SUM(payments.prix) as sum'),
                DB::raw('SUM(CASE WHEN payments.mode_paiement = "espece" OR payments.mode_paiement IS NULL THEN payments.prix ELSE 0 END) as sum_espece'),
                DB::raw('SUM(CASE WHEN payments.mode_paiement = "mobile_money" THEN payments.prix ELSE 0 END) as sum_mobile_money')
            )
            ->get();

        return [
            'payments' => $payments,
            'caissieres' => $caissieres
        ];
    }

    public function detail($day, $id)
    {
        $hospitalId = auth()->user()->hospital->id;

        $cashier = Caissiere::with('user')->find($id);

        $payments = Payment::where('payments.hospital_id', $hospitalId)
            ->where('payments.caissiere_id', $id)
            ->where('payments.status', 'success')
            ->whereDate('payments.created_at', $day)
            ->with([
                'cashier.user',
                'admission.patient.user',
                'admission.prestationHospital.prestationService',
                'hospitalisation.consultation.patient.user',
                'typeAssurance'
            ])
            ->orderBy('payments.created_at', 'desc')
            ->get();

        return [
            'cashier' => $cashier,
            'payments' => $payments
        ];
    }
}
