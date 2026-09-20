<?php

namespace App\Http\Controllers\Secretariat;

use App\Http\Controllers\Controller;
use App\Models\Hospitalisation;
use App\Repositories\Doctor\HospitalisationRepository;
use Illuminate\Support\Facades\Auth;

class HospitalisationController extends Controller
{
    private function getHospitalId()
    {
        return optional(Auth::user()->secretariat)->hospital_id 
            ?? optional(Auth::user()->hospital)->id 
            ?? Auth::user()->hospital_id 
            ?? (function_exists('getUserHospitalId') ? getUserHospitalId() : null);
    }

    private function getBaseQuery()
    {
        try {
            (new HospitalisationRepository())->syncPendingHospitalisationsFromConsultations();
        } catch (\Throwable $e) {
            // Ignorer silencieusement si la table n'existe pas encore ou en cas d'erreur mineure
        }

        $hospitalId = $this->getHospitalId();

        $query = Hospitalisation::with([
            'consultation.patient.user',
            'consultation.patient.lieuNaissance',
            'consultation.patient.residenceActuelle',
            'consultation.admission.patient.user',
            'consultation.registre',
            'doctor.user',
            'daysHospitalisation.bed.bedroom',
        ])->orderByDesc('id');

        if ($hospitalId) {
            $query->where(function ($q) use ($hospitalId) {
                $q->whereHas('consultation', function ($q2) use ($hospitalId) {
                    $q2->where('hospital_id', $hospitalId);
                })->orWhereHas('consultation.admission', function ($q2) use ($hospitalId) {
                    $q2->where('hospital_id', $hospitalId);
                });
            });
        }

        return $query;
    }

    public function in_progress()
    {
        $hospitalId = $this->getHospitalId();

        $inProgressCount = $this->getBaseQuery()->where('status', 'in_progress')->count();
        $historyCount = $this->getBaseQuery()->where('status', '!=', 'in_progress')->count();

        $hospitalisations = $this->getBaseQuery()
            ->where('status', 'in_progress')
            ->get();

        return view('users.secretariat.hospitalisation.list', [
            'hospitalisations' => $hospitalisations,
            'activeTab' => 'in_progress',
            'inProgressCount' => $inProgressCount,
            'historyCount' => $historyCount,
            'pageTitle' => 'Liste des patients actuellement hospitalisés'
        ]);
    }

    public function history()
    {
        $inProgressCount = $this->getBaseQuery()->where('status', 'in_progress')->count();
        $historyCount = $this->getBaseQuery()->where('status', '!=', 'in_progress')->count();

        $hospitalisations = $this->getBaseQuery()
            ->where('status', '!=', 'in_progress')
            ->get();

        return view('users.secretariat.hospitalisation.list', [
            'hospitalisations' => $hospitalisations,
            'activeTab' => 'history',
            'inProgressCount' => $inProgressCount,
            'historyCount' => $historyCount,
            'pageTitle' => 'Historique des hospitalisations'
        ]);
    }
}
