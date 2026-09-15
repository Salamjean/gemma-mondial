<?php

namespace App\Http\Controllers\Secretariat;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Admission;
use App\Models\Secretaire;
use App\Models\Departement;
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
        $hospitalId = $secretaire ? $secretaire->hospital_id : null;

        $secretaryUsers = User::whereHas('secretariat', function ($q) use ($hospitalId) {
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })->where('role_as', 'secretariat')->get();

        $type_consultations = TypeExamen::get();
        $departements = Departement::get();
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

        $admission = Admission::create([
            'code_admission' => "ADM" . rand(000000, 999999),
            'date_admission' => Carbon::now()->format('Y-m-d H:i:s'),
            'secretaire_id' => $secretaire->id,
            'hospital_id' => $secretaire->hospital_id,
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'caissiere_id' => null,
            'type_examen_id' => $request->type_examen_id,
            'type_admission' => $request->type_admission_id,
            'type_assurance_id' => $request->type_assurance_id,
            'no_assurance' => $request->no_assurance,
            'mode_entree' => $request->mode_entree,
            'montant' => $request->montant,
            'montant_normal' => $request->montant,
            'motif_consultation' => $request->motif_consultation,
        ]);

        return to_route('secretariat.admission.list')->with('success', 'Admission envoyée avec succès');
    }

}
