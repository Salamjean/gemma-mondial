<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Repositories\Hospital\PatientRepository;
use Illuminate\Http\Request;

class PatientController extends Controller
{

    public function instance()
    {
        return new PatientRepository();
    }

    public function index()
    {
        $title = "Liste des patients";
        return view('users.hospital.patient.index', ["title" => $title, 'patients' => $this->instance()->list()]);
    }

    public function show($id)
    {
        return view('users.hospital.patient.show', ['patient' => $this->instance()->show($id)]);
    }

    public function dossierMedical($id)
    {
        $patient = \App\Models\Patient::findOrFail($id);
        $consultations = \App\Models\Consultation::where('patient_id', $patient->id)->get();
        $consultation = \App\Models\Consultation::where('patient_id', $patient->id)->first();
        $ordonnance_interne = $consultation ? \App\Models\Ordonnance::where('type', 'interne')->where('consultation_id', $consultation->id)->first() : null;
        $ordonnance_externe = $consultation ? \App\Models\Ordonnance::where('type', 'externe')->where('consultation_id', $consultation->id)->first() : null;
        $registres = $consultation ? \App\Models\Registre::where('consultation_id', $consultation->id)->get() : collect();
        return view('users.doctor.patient.dossier_medical', compact('patient', 'consultations', 'consultation', 'ordonnance_interne', 'ordonnance_externe', 'registres'));
    }

    public function parcoursIntervention($id)
    {
        $consultation = \App\Models\Consultation::with([
            'patient.user',
            'patient.lieuNaissance',
            'patient.residenceActuelle',
            'doctor.user',
            'infirmier.user',
            'admission.infirmier.user',
            'admission.doctor.user',
            'admission.cashier.user',
            'admission.secretariat.user',
            'prestationHospital.prestationService.service',
            'registre.registreConsultationCurative',
            'registre.registreAccouchement',
            'registre.registreConsultationPreNatale',
            'registre.registreConsultationPostNatale',
            'ordonnances.prescriptions.drug',
            'ordonnances.prescriptions.drugHospital.drug',
            'examen',
            'arret',
            'declaration',
            'hospitalisation'
        ])->findOrFail($id);

        $patient = $consultation->patient;
        $ordonnance_interne = \App\Models\Ordonnance::where('type', 'interne')->where('consultation_id', $consultation->id)->first();
        $ordonnance_externe = \App\Models\Ordonnance::where('type', 'externe')->where('consultation_id', $consultation->id)->first();
        $registres = \App\Models\Registre::where('consultation_id', $consultation->id)->get();

        return view('users.patient.parcours_intervention', compact('consultation', 'patient', 'ordonnance_interne', 'ordonnance_externe', 'registres'));
    }
}
