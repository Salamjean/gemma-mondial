<?php

namespace App\Http\Controllers\Secretariat;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Admission;
use App\Models\Secretaire;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\TypeAssurance;
use App\Models\TypeConsultation;
use App\Models\IssueConsultation;
use App\Http\Controllers\Controller;
use App\Models\TypeExamen;
use Illuminate\Support\Facades\Auth;

class ExamenController extends Controller
{
    public function list()
    {
        $admissions = Admission::with('patient.user', 'doctor.user')->get();
        //dd($admissions->doctor);
        return view('users.secretariat.admission.list', compact('admissions'));
    }
    public function today()
    {
        $today = Carbon::today();
        //dd($today);
        $admissions = Admission::with('patient.user', 'doctor.user', 'caissiere.user')->whereDate('created_at', $today)->get();
        //dd($admissions);
        return view('users.secretariat.examen.today', compact('admissions'));
    }
    public function history()
    {
        $admissions = Admission::with('patient.user', 'doctor.user', 'caissiere.user')->get();
        return view('users.secretariat.examen.history', compact('admissions'));
    }
    public function make()
    {
        $secretaire = Secretaire::where('user_id', auth()->user()->id)->first();
        $caissiere = \App\Models\Caissiere::where('user_id', auth()->user()->id)->first();
        $hospitalId = optional($secretaire)->hospital_id 
            ?? optional($caissiere)->hospital_id 
            ?? optional(auth()->user()->hospital)->id 
            ?? auth()->user()->hospital_id 
            ?? (function_exists('getUserHospitalId') ? getUserHospitalId() : null);

        $secretaryUsers = User::whereHas('secretariat', function ($q) use ($hospitalId) {
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })->where('role_as', 'secretariat')->get();

        $type_consultations = TypeExamen::get();
        $departements = Department::get();
        $issue_consultations = IssueConsultation::get();
        $type_assurances = TypeAssurance::get();

        $patients = Patient::where(function ($q) use ($hospitalId) {
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId)
                  ->orWhereHas('passage', function ($pq) use ($hospitalId) {
                      $pq->where('hospital_id', $hospitalId);
                  });
            }
        })->with('user')->get();

        $doctors = Doctor::where(function ($q) use ($hospitalId) {
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })->with('user')->get();

        return view('users.secretariat.examen.make', compact('type_consultations', 'departements', 'issue_consultations', 'type_assurances', 'patients', 'doctors', 'secretaryUsers'));
    }
    public function addAdmission(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'doctor_id' => 'required',
            'type_examen_id' => 'required',
            'motif_consultation' => 'required',
        ]);
        $secretaire = Secretaire::where('user_id', auth()->user()->id)->first();
        $caissiere = \App\Models\Caissiere::where('user_id', auth()->user()->id)->first();
        $hospitalId = optional($secretaire)->hospital_id 
            ?? optional($caissiere)->hospital_id 
            ?? optional(auth()->user()->hospital)->id 
            ?? auth()->user()->hospital_id 
            ?? (function_exists('getUserHospitalId') ? getUserHospitalId() : 1);

        $admission = Admission::create([
            'code_admission' => "ADM" . rand(000000, 999999),
            'date_admission' => Carbon::now()->format('Y-m-d H:i:s'),
            'secretaire_id' => optional($secretaire)->id,
            'hospital_id' => $hospitalId,
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'caissiere_id' => optional($caissiere)->id,
            'type_examen_id' => $request->type_examen_id,
            'type_admission' => $request->type_admission_id,
            'type_assurance_id' => $request->type_assurance_id,
            'no_assurance' => $request->no_assurance,
            'mode_entree' => $request->mode_entree,
            'montant' => $request->montant,
            'montant_normal' => $request->montant,
            'motif_consultation' => $request->motif_consultation,
        ]);

        // Audit Trail
        $pat = Patient::with('user')->find($request->patient_id);
        $patName = ($pat->user->name ?? '') . ' ' . ($pat->user->prenom ?? '');
        \App\Services\AuditLogService::log(
            'AFFECTATION_PATIENT',
            'SECRETARIAT',
            "Affectation du patient {$patName} [{$pat->code_patient}] pour Examen (N° Adm: {$admission->code_admission})",
            ['admission_id' => $admission->id, 'patient_id' => $request->patient_id, 'type_examen_id' => $request->type_examen_id],
            null,
            $hospitalId
        );

        return to_route('secretariat.admission.list')->with('success', 'Admission envoyée avec succès');
    }

}
