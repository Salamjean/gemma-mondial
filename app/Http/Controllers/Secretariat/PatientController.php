<?php

namespace App\Http\Controllers\Secretariat;

use App\Repositories\SmsRepository;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Admission;
use App\Models\Secretaire;
use App\Models\Caissiere;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TypeAssurance;
use App\Models\PassagePatient;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Notifications\PatientRegistrationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PatientController extends Controller
{
    private static $ind = "225";

    public function list()
    {
        $hospital = optional(Auth::user()->secretariat)->hospital_id 
            ?? optional(Auth::user()->cashier)->hospital_id
            ?? optional(Auth::user()->infirmier)->hospital_id 
            ?? optional(Auth::user()->doctor)->hospital_id 
            ?? optional(Auth::user()->hospital)->id 
            ?? Auth::user()->hospital_id 
            ?? (function_exists('getUserHospitalId') ? getUserHospitalId() : null);

        $patientsQuery = Patient::where('status', 1)
            ->with('user')
            ->with('lieuNaissance')
            ->with('residenceActuelle')
            ->orderByDESC('created_at');

        if ($hospital) {
            $patientsQuery->where(function ($query) use ($hospital) {
                $query->where('hospital_id', $hospital)
                    ->orWhereHas('passage', function ($q) use ($hospital) {
                        $q->where('hospital_id', $hospital);
                    });
            });
        }

        $patients = $patientsQuery->get();

        return view("users.secretariat.patient.list", compact('patients'));
    }

    public function create()
    {
        return view("users.secretariat.patient.create");
    }

    public function updatePatient(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $user = User::findOrFail($patient->user->id);

        $isSoinsInfirmiers = false;
        if ($request->filled('service_id_up')) {
            $isSoinsInfirmiers = preg_match('/infirmier|soin/i', $request->service_id_up) || $request->service_id_up == '4';
        }

        $rules = [
            'email_up' => ['nullable'],
            'residence_actuelle_up' => 'nullable',
            'situation_matrimoniale_up' => 'nullable',
            'type_piece_up' => 'nullable',
            'numero_identite' => ['nullable', Rule::unique('patients')->ignore($patient->id)],
            'telephone' => ['required', Rule::unique('patients')->ignore($patient->id)],
            'contact2' => ['nullable', Rule::unique('patients')->ignore($patient->id)],
            'admission_patient_up' => 'nullable',
            'prestation_service_id' => 'nullable',
            'infirmier_id' => 'nullable',
            'doctor_id' => 'nullable',
            'motif_consultation' => 'nullable',
        ];

        if ($request->admission_patient_up == 'Oui') {
            $rules['prestation_service_id'] = 'required';
            $rules['infirmier_id'] = 'required';
            if (!$isSoinsInfirmiers) {
                $rules['doctor_id'] = 'required';
            }
        }

        $messages = [
            'infirmier_id.required' => 'La sélection d\'un(e) infirmier(ère) est obligatoire.',
            'doctor_id.required' => 'La sélection d\'un médecin traitant est obligatoire pour ce service.',
            'prestation_service_id.required' => 'La sélection d\'une prestation médicale est obligatoire.',
        ];

        $request->validate($rules, $messages);

        if ($request->filled('name_up')) {
            $user->name = $request->name_up;
        }
        if ($request->filled('prenom_up')) {
            $user->prenom = $request->prenom_up;
        }
        $user->email = $request->email_up;
        $patient->no_assurance = $request->no_assurance_up;
        $patient->profession = $request->profession_up;

        if ($request->filled('residence_habituelle_up')) {
            if (is_numeric($request->residence_habituelle_up)) {
                $patient->residence_habituelle_id = $request->residence_habituelle_up;
            } else {
                $sub = \App\Models\SubPrefecture::where('name', $request->residence_habituelle_up)->first();
                if ($sub) {
                    $patient->residence_habituelle_id = $sub->id;
                }
            }
        }

        if ($request->filled('residence_actuelle_up')) {
            if (is_numeric($request->residence_actuelle_up)) {
                $patient->residence_actuelle_id = $request->residence_actuelle_up;
            } else {
                $sub = \App\Models\SubPrefecture::where('name', $request->residence_actuelle_up)->first();
                if ($sub) {
                    $patient->residence_actuelle_id = $sub->id;
                }
            }
        }

        $patient->contact2 = $request->contact2;
        $patient->num_cmu = $request->num_cmu_up ?? $request->num_cmu;
        $patient->ethnie = $request->ethnie_up;
        $patient->type_piece = $request->type_piece_up;
        $patient->numero_identite = $request->numero_identite_up;
        $patient->img_url = $request->img_url;
        $patient->situation_matrimoniale = $request->situation_matrimoniale_up;
        $patient->telephone = $request->telephone;
        $patient->address = $request->address_up;
        $patient->nbre_enfant = $request->nbre_enfant_up ?? 0;
        $patient->nom_personne_cas_urgence = $request->nom_personne_cas_urgence_up;
        $patient->telephone_personne_cas_urgence = $request->telephone_personne_cas_urgence_up;
        $patient->lien_personne_cas_urgence = $request->lien_personne_cas_urgence_up;

        $user->save();
        $patient->save();

        $hospitalId = optional(auth()->user()->secretariat)->hospital_id 
            ?? optional(auth()->user()->infirmier)->hospital_id 
            ?? optional(auth()->user()->doctor)->hospital_id 
            ?? auth()->user()->hospital_id 
            ?? (function_exists('getUserHospitalId') ? getUserHospitalId() : 1);

        //specifié le passage du patient dans l'hopital
        if (!PassagePatient::where('hospital_id', $hospitalId)->where('patient_id', $patient->id)->exists()) {
            $passage = new PassagePatient();
            $passage->libelle = 'Passage compte';
            $passage->hospital_id = $hospitalId;
            $passage->patient_id = $patient->id;
            $passage->date = date('Y-m-d');
            $passage->save();
        }

        $secretaire = Secretaire::where('user_id', auth()->user()->id)->first();
        $caissiere = Caissiere::where('user_id', auth()->user()->id)->first();
        $admissionHospitalId = optional(optional($secretaire)->hospital)->id 
            ?? optional(optional($caissiere)->hospital)->id 
            ?? $hospitalId;

        //verifer l'admission
        if ($request->admission_patient_up == 'Oui') {
            $admission = Admission::create([
                'code_admission' => codeAdmission(),
                'date_admission' => Carbon::now()->format('Y-m-d H:i:s'),
                'secretaire_id' => optional($secretaire)->id,
                'hospital_id' => $admissionHospitalId,
                'patient_id' => $patient->id,
                'doctor_id' => $request->doctor_id ?? null,
                'infirmier_id' => $request->infirmier_id ?? null,
                'caissiere_id' => optional($caissiere)->id,
                'prestation_hopital_id' => $request->prestation_service_id,
                'type_admission' => $request->type_admission_id,
                'mode_entree' => $request->mode_entree,
                'montant' => $request->montant,
                'montant_normal' => $request->montant,
                'motif_consultation' => $request->motif_consultation,
            ]);

            //save payment
            $payment = new Payment();
            $payment->type = 'admission';
            $payment->date = Carbon::now()->format('Y-m-d');
            $payment->prix = $request->montant;
            $payment->prix_normal = $request->montant;
            $payment->hospital_id = $admissionHospitalId;
            $payment->admission_id = $admission->id;
            if ($caissiere) {
                $payment->caissiere_id = $caissiere->id;
            }
            $payment->save();
        }
        // Retourner une réponse JSON
        return response()->json(['success' => 'Patient mis à jour avec succès']);
    }
    public function detail($id)
    {
        $patient = Patient::find($id);
        $type_assurances = TypeAssurance::get();
        return view('users.secretariat.patient.detail', compact('patient', 'type_assurances'));
    }

    public function dossierMedical($id)
    {
        if (in_array(Auth::user()->role_as, ['secretariat', 'cashier'])) {
            return redirect()->route('secretariat.patient.detail', $id)->with('error', 'Accès non autorisé : la consultation du dossier médical est réservée au personnel soignant.');
        }

        $patient = Patient::findOrFail($id);
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

    public function edit($id)
    {
        $patient = Patient::with([
            'user',
            'lieuNaissance',
            'residenceActuelle',
            'residenceHabituelle',
            'admissions' => function ($query) {
                $query->latest()->with(['prestationHospital.serviceHospital.service', 'infirmier.user', 'doctor.user']);
            }
        ])->findOrFail($id);

        $latestAdmission = $patient->admissions->first();
        $type_assurances = TypeAssurance::get();

        return view('users.secretariat.patient.edit', compact('patient', 'latestAdmission', 'type_assurances'));
    }

    public function card(Request $request, $id)
    {
        $patient = Patient::with(['user', 'hospital'])->findOrFail($id);
        if ($request->ajax()) {
            return view('users.secretariat.patient.card_inner', compact('patient'));
        }
        return view('users.secretariat.patient.card', compact('patient'));
    }

    public function searchPatients(Request $request)
    {
        $telephone = $request->input('telephone');
        $fullname = $request->input('fullname');
        $birth_date = $request->input('birth_date');
        $num_cmu = $request->input('num_cmu');

        $data = Patient::query();

        if ($telephone) {
            $data->where('telephone', $telephone);
        }

        if ($num_cmu) {
            $data->where('num_cmu', 'like', '%' . $num_cmu . '%');
        }

        if ($fullname) {
            $terms = array_values(array_filter(explode(" ", trim($request->input('fullname')))));
            if (count($terms) >= 2) {
                $first = $terms[0];
                $second = implode(" ", array_slice($terms, 1));
                $data->whereHas('user', function ($q) use ($first, $second) {
                    $q->where(function ($sub) use ($first, $second) {
                        $sub->where('name', 'like', '%' . $first . '%')
                            ->where('prenom', 'like', '%' . $second . '%');
                    })->orWhere(function ($sub) use ($first, $second) {
                        $sub->where('name', 'like', '%' . $second . '%')
                            ->where('prenom', 'like', '%' . $first . '%');
                    });
                });
            } else if (count($terms) === 1) {
                $term = $terms[0];
                $data->whereHas('user', function ($q) use ($term) {
                    $q->where('name', 'like', '%' . $term . '%')
                        ->orWhere('prenom', 'like', '%' . $term . '%');
                });
            }
        }

        if ($birth_date) {
            $data->where('birth_date', $birth_date);
        }

        $patients = $data->with('user')->with('lieuNaissance')->with('residenceActuelle')->with('residenceHabituelle')->get();

        return response()->json(['patients' => $patients]);
    }
    public function addPatient(Request $request)
    {

        $isSoinsInfirmiers = false;
        if ($request->filled('service_id')) {
            $isSoinsInfirmiers = preg_match('/infirmier|soin/i', $request->service_id) || $request->service_id == '4';
        }

        $rules = [
            'name' => 'required',
            'prenom' => 'required',
            'email' => 'nullable|email|unique:users',
            'gender' => 'required',
            'birth_date' => 'required',
            'residence_actuelle' => 'required',
            'lieu_de_naissance' => 'required',
            'pays' => 'required',
            'autre_pays' => 'required_if:pays,autre',
            'situation_matrimoniale' => 'required',
            'type_piece' => 'required',
            'numero_identite' => 'nullable|required_with:type_piece|unique:patients',
            'telephone' => 'required|unique:patients',
            'contact2' => 'nullable|unique:patients',
            'admission_patient' => 'required',
            'prestation_service_id' => 'required_if:admission_patient,Oui',
            'infirmier_id' => 'required_if:admission_patient,Oui',
            'motif_consultation' => 'required_if:admission_patient,Oui',
            'img_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'profession' => 'nullable',
            'num_cmu' => 'nullable',
            'fingerprint_left_template' => 'nullable|string',
            'fingerprint_right_template' => 'nullable|string',
            'fingerprint_left_image' => 'nullable|string',
            'fingerprint_right_image' => 'nullable|string',
            'fingerprint_device' => 'nullable|string',
        ];

        if ($request->admission_patient == 'Oui' && !$isSoinsInfirmiers) {
            $rules['doctor_id'] = 'required';
        }

        $messages = [
            'infirmier_id.required_if' => 'La sélection d\'un(e) infirmier(ère) est obligatoire.',
            'infirmier_id.required' => 'La sélection d\'un(e) infirmier(ère) est obligatoire.',
            'doctor_id.required' => 'La sélection d\'un médecin traitant est obligatoire pour ce service.',
            'prestation_service_id.required_if' => 'La sélection d\'une prestation médicale est obligatoire.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $password = '1234';
        $user = User::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'role_as' => 'patient',
            'password' => Hash::make($password),
        ]);

        if ($request->hasFile('img_url')) {
            $file_name = Carbon::now()->timestamp . '.' . $request->img_url->extension();
            $request->img_url->storeAs('images/patients/', $file_name);
            $file_path = 'src-files/images/patients/' . $file_name;
        }

        $secretaire = Secretaire::where('user_id', auth()->user()->id)->first();
        $pays = ($request->pays == 'Côte d\'Ivoire') ? $request->pays : $request->autre_pays;
        $dataNaissRef = dateNaiss($request->birth_date);
        $countNaissRef = countNaiss($request->birth_date);
        $code = ($request->pays == 'Côte d\'Ivoire') ? 225 : 100;
        $codePatient = "DM$dataNaissRef$countNaissRef$code";

        $hospitalId = optional(optional($secretaire)->hospital)->id 
            ?? optional(auth()->user()->infirmier)->hospital_id 
            ?? optional(auth()->user()->doctor)->hospital_id 
            ?? auth()->user()->hospital_id 
            ?? (function_exists('getUserHospitalId') ? getUserHospitalId() : 1);

        $patient = Patient::create([
            'user_id' => $user->id,
            'secretaire_id' => optional($secretaire)->id,
            'hospital_id' => $hospitalId,
            'gender' => $request->gender,
            'no_assurance' => $request->no_assurance,
            'num_cmu' => $request->num_cmu,
            'profession' => $request->profession,
            'lieu_de_naissance_id' => $request->lieu_de_naissance,
            'birth_date' => $request->birth_date,
            'residence_habituelle_id' => $request->residence_habituelle,
            'residence_actuelle_id' => $request->residence_actuelle,
            'contact2' => $request->contact2,
            'type_piece' => $request->type_piece,
            'numero_identite' => $request->numero_identite,
            'img_url' => $file_path ?? null,
            'situation_matrimoniale' => $request->situation_matrimoniale,
            'ethnie' => $request->ethnie,
            'telephone' => $request->telephone,
            'country' => $pays,
            'code_patient' => $codePatient,
            'address' => $request->address,
            'nbre_enfant' => $request->nbre_enfant ?? 0,
            'nom_personne_cas_urgence' => $request->nom_personne_cas_urgence,
            'telephone_personne_cas_urgence' => $request->telephone_personne_cas_urgence,
            'lien_personne_cas_urgence' => $request->lien_personne_cas_urgence,
            'nom_personne2_cas_urgence' => $request->nom_personne2_cas_urgence,
            'telephone_personne2_cas_urgence' => $request->telephone_personne2_cas_urgence,
            'lien_personne2_cas_urgence' => $request->lien_personne2_cas_urgence,
            'status' => 1,
            'fingerprint_left_index' => $request->fingerprint_left_template,
            'fingerprint_right_index' => $request->fingerprint_right_template,
            'fingerprint_left_image' => $request->fingerprint_left_image,
            'fingerprint_right_image' => $request->fingerprint_right_image,
            'fingerprint_device' => $request->fingerprint_device ?? config('fingerprint.device'),
            'fingerprint_captured_at' => $request->fingerprint_left_template && $request->fingerprint_right_template ? now() : null,
            'fingerprint_verified' => (bool) ($request->fingerprint_left_template && $request->fingerprint_right_template),
        ]);

        $caissiere = Caissiere::where('user_id', auth()->user()->id)->first();

        if ($request->admission_patient == 'Oui') {
            $admission = Admission::create([
                'code_admission' => codeAdmission(),
                'date_admission' => Carbon::now()->format('Y-m-d H:i:s'),
                'secretaire_id' => optional($secretaire)->id,
                'hospital_id' => $hospitalId,
                'patient_id' => $patient->id,
                'doctor_id' => $request->doctor_id ?? null,
                'infirmier_id' => $request->infirmier_id ?? null,
                'caissiere_id' => optional($caissiere)->id,
                'prestation_hopital_id' => $request->prestation_service_id,
                'type_admission' => $request->type_admission_id,
                'mode_entree' => $request->mode_entree,
                'montant' => $request->montant,
                'montant_normal' => $request->montant,
                'motif_consultation' => $request->motif_consultation,
            ]);

            // Enregistrement du paiement
            $payment = new Payment();
            $payment->type = 'admission';
            $payment->date = Carbon::now()->format('Y-m-d');
            $payment->prix = $request->montant;
            $payment->prix_normal = $request->montant;
            $payment->hospital_id = $hospitalId;
            $payment->admission_id = $admission->id;
            if ($caissiere) {
                $payment->caissiere_id = $caissiere->id;
            }
            $payment->save();
        }

        // Enregistrement du passage patient

        if ($patient) {
            $passage = new PassagePatient();
            $passage->libelle = 'Création du compte';
            $passage->hospital_id = $hospitalId;
            $passage->date = date('Y-m-d');
            $passage->patient_id = $patient->id;
            $passage->save();

            $message = "Inscription confirmée pour M/Mme $user->name $user->prenom . Utilisez le DM: $patient->code_patient pour vous connecter.";

            (new SmsRepository($patient->telephone, $message))->send();

            if ($user->email) {
                try {
                    $user->notify(new PatientRegistrationNotification($patient, $user, $codePatient, $password));
                } catch (\Exception $e) {
                    // Logger l'erreur sans interrompre le processus
                    Log::error('Erreur lors de l\'envoi de l\'email patient : ' . $e->getMessage());
                    // Optionnel: ajouter un message pour l'administrateur
                }
            }
        }
        return response()->json(['success' => 'Patient enregistré avec succès'], 200);
    }

    public function getPatient(Request $request)
    {
        $patientId = $request->input('patient_id');
        $patient = Patient::with('user')->with('lieuNaissance')->with('residenceActuelle')->with('residenceHabituelle')->find($patientId);

        return response()->json(['patient' => $patient]);
    }
}
