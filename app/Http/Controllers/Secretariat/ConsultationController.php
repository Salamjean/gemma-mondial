<?php

namespace App\Http\Controllers\Secretariat;

use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Admission;
use App\Models\Secretaire;
use App\Models\Department;
use App\Models\PassagePatient;
use Illuminate\Http\Request;
use App\Models\TypeConsultation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{
   
    public function addAdmission(Request $request)
    {
        $rules = [
            'patient_id' => 'required',
            'type_consultation_id' => 'required',
            'departement_id' => 'required',
            'doctor_id' => 'required',
            'motif_consultation' => 'required',

        ];
        /***Messages de validation hospitalisation ****/
        $messages = [
            'patient_id.required' => 'Selectionner un patient svp!',
            'type_consultation_id.required' => 'Selectionner le motif de la visite svp!',
            'doctor_id.required' => 'Selectionner un medecin svp!',
            'departement_id.required' => 'Selectionner un patient svp!',
            'motif_consultation.required' => 'Décrivez en quelques mots la raison de la visite svp!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            return redirect()->back()->withErrors($errorMessages)->withInput();
        }

        $secretaire = Secretaire::where('user_id', auth()->user()->id)->first();
        $caissiere = \App\Models\Caissiere::where('user_id', auth()->user()->id)->first();
        $hospitalId = optional($secretaire)->hospital_id 
            ?? optional($caissiere)->hospital_id 
            ?? optional(auth()->user()->hospital)->id 
            ?? auth()->user()->hospital_id 
            ?? (function_exists('getUserHospitalId') ? getUserHospitalId() : 1);

        $admission = Admission::create([
            'code_admission' => codeAdmission(),
            'date_admission' => Carbon::now()->format('Y-m-d H:i:s'),
            'secretaire_id' => optional($secretaire)->id,
            'hospital_id' => $hospitalId,
            'patient_id' => $request->patient_id,
            'doctor_id' => $request->doctor_id,
            'caissiere_id' => optional($caissiere)->id,
            'type_consultation_id' => $request->type_consultation_id,
            'type_admission' => $request->type_admission_id,
            'type_assurance_id' => $request->type_assurance_id,
            'no_assurance' => $request->no_assurance,
            'mode_entree' => $request->mode_entree,
            'montant' => $request->montant,
            'montant_normal' => $request->montant,
            'motif_consultation' => $request->motif_consultation,
        ]);

        if(!PassagePatient::where('hospital_id', $hospitalId)->where('patient_id', $request->patient_id)->exists())
        {
            $passage = new PassagePatient();
            $passage->libelle = 'Passage compte';
            $passage->hospital_id = $hospitalId;
            $passage->patient_id = $request->patient_id;
            $passage->date = date('Y-m-d');
            $passage->save();
        }

        return to_route('secretariat.admission.list')->with('success', 'Admission envoyée avec succès');
    }
}
