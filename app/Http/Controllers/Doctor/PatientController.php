<?php

namespace App\Http\Controllers\Doctor;

use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ordonnance;
use App\Models\Registre;
use App\Repositories\Doctor\PatientRepository;

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
        $patient =  Patient::find($id);
        $consultations = Consultation::where('patient_id', $patient->id)->get();
        $consultation = Consultation::where('patient_id', $patient->id)->first();
        $ordonnance_interne = Ordonnance::where('type', 'interne')->where('consultation_id', $consultation->id)->first();
        $ordonnance_externe = Ordonnance::where('type', 'externe')->where('consultation_id', $consultation->id)->first();
        $registres = Registre::where('consultation_id', $consultation->id)->get();
        return view('users.doctor.patient.dossier_medical', ['patient' => $patient, 'consultations' => $consultations,'consultation' => $consultation, 'ordonnance_interne' => $ordonnance_interne,'ordonnance_externe' => $ordonnance_externe, 'registres'=>$registres]);
    }

    public function parcoursIntervention($id)
    {
        $consultation = Consultation::with([
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
        $ordonnance_interne = Ordonnance::where('type', 'interne')->where('consultation_id', $consultation->id)->first();
        $ordonnance_externe = Ordonnance::where('type', 'externe')->where('consultation_id', $consultation->id)->first();
        $registres = Registre::where('consultation_id', $consultation->id)->get();

        return view('users.patient.parcours_intervention', compact('consultation', 'patient', 'ordonnance_interne', 'ordonnance_externe', 'registres'));
    }
}
