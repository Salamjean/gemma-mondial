<?php

namespace App\Repositories\Cashier;

use App\Http\Requests\ConsultationRequest;
use App\Models\Consultation;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;

class ConsultationRepository
{
    public function model()
    {
        return Consultation::class;
    }

    /**
     * @return array
     * doctor
     * consultation status 0 of today
     */
    public function today()
    {
        return Consultation::orderByDESC('created_at')->where('doctor_id', Auth::user()->doctor->id)->where('date_consultation', date('Y-m-d'))->where('status', '0')->get();
    }

    public function history()
    {
        return Consultation::orderByDESC('created_at')->where('doctor_id', Auth::user()->doctor->id)->where('date_consultation', '<' ,date('Y-m-d'))->get();
    }

    public function store($admission)
    {
        $patientId = is_array($admission) ? $admission['patient_id'] : $admission->patient_id;
        $patient = Patient::find($patientId);

        if (!$patient) {
            return ['status' => 'error'];
        }

        $nb = $patient->consultations->count();
        $admissionId = is_array($admission) ? $admission['id'] : $admission->id;
        $hospitalId = is_array($admission) ? $admission['hospital_id'] : $admission->hospital_id;
        $doctorId = is_array($admission) ? ($admission['doctor_id'] ?? null) : $admission->doctor_id;
        $infirmierId = is_array($admission) ? ($admission['infirmier_id'] ?? null) : $admission->infirmier_id;
        $prestationHopitalId = is_array($admission) ? ($admission['prestation_hopital_id'] ?? null) : $admission->prestation_hopital_id;
        $montant = is_array($admission) ? ($admission['montant'] ?? 0) : $admission->montant;
        $motif = is_array($admission) ? ($admission['motif_consultation'] ?? null) : $admission->motif_consultation;

        $consultation = new Consultation();
        $consultation->date_consultation = date('Y-m-d');
        $consultation->code_consultation = 'CONSULT' . substr($patient->code_patient, 2) . ($nb + 1);
        $consultation->admission_id = $admissionId;
        $consultation->hospital_id = $hospitalId;
        $consultation->patient_id = $patientId;
        $consultation->doctor_id = $doctorId;
        $consultation->infirmier_id = $infirmierId;
        $consultation->prestation_hospital_id = $prestationHopitalId;
        $consultation->montant = $montant;
        $consultation->motif_consultation = $motif;

        $consultation->status_inf = 0;
        $consultation->status = 0;
        $consultation->save();

        return ['status' => 'success'];
    }

}
