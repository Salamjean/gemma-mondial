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
use App\Models\PrestationHospital;
use App\Models\CareRequested;
use App\Models\Consultation;
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
use App\Services\AuditLogService;

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

        $rawTelephone = $request->telephone ?? $request->telephone_up;
        if ($rawTelephone) {
            $cleanedNum = preg_replace('/\s+/', '', $rawTelephone);
            if (str_starts_with($cleanedNum, '+225') && strlen($cleanedNum) > 4) {
                $telephone = substr($cleanedNum, 4);
            } elseif (str_starts_with($cleanedNum, '00225') && strlen($cleanedNum) > 5) {
                $telephone = substr($cleanedNum, 5);
            } else {
                $telephone = $cleanedNum;
            }
        } else {
            $telephone = $patient->telephone;
        }

        $rawContact2 = $request->contact2 ?? $request->contact2_up;
        if ($rawContact2) {
            $cleanedNum2 = preg_replace('/\s+/', '', $rawContact2);
            if (str_starts_with($cleanedNum2, '+225') && strlen($cleanedNum2) > 4) {
                $contact2 = substr($cleanedNum2, 4);
            } elseif (str_starts_with($cleanedNum2, '00225') && strlen($cleanedNum2) > 5) {
                $contact2 = substr($cleanedNum2, 5);
            } else {
                $contact2 = $cleanedNum2;
            }
        } else {
            $contact2 = null;
        }

        $request->merge([
            'telephone' => $telephone,
            'contact2' => $contact2,
        ]);

        $rules = [
            'email_up' => ['nullable'],
            'residence_actuelle_up' => 'nullable',
            'situation_matrimoniale_up' => 'nullable',
            'type_piece_up' => 'nullable',
            'numero_identite' => ['nullable', Rule::unique('patients')->ignore($patient->id)],
            'telephone' => ['required', Rule::unique('patients')->ignore($patient->id)],
            'contact2' => ['nullable'],
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
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.unique' => 'Ce numéro de téléphone est déjà attribué à un autre patient.',
            'infirmier_id.required' => 'La sélection d\'un(e) infirmier(ère) est obligatoire.',
            'doctor_id.required' => 'La sélection d\'un médecin traitant est obligatoire pour ce service.',
            'prestation_service_id.required' => 'La sélection d\'une prestation médicale est obligatoire.',
        ];

        if ($request->filled('name_up')) {
            $user->name = strtoupper(trim($request->name_up));
        }
        if ($request->filled('prenom_up')) {
            $user->prenom = ucwords(trim($request->prenom_up));
        }
        if ($request->has('email_up')) {
            $user->email = $request->email_up;
        }
        if ($request->filled('lieu_de_naissance')) {
            $patient->lieu_de_naissance_id = $request->lieu_de_naissance;
        }
        if ($request->filled('no_assurance_up')) {
            $patient->no_assurance = $request->no_assurance_up;
        }
        if ($request->filled('profession_up')) {
            $patient->profession = $request->profession_up;
        }

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

        if ($telephone) {
            $patient->telephone = $telephone;
        }
        if ($request->has('contact2') || $request->has('contact2_up')) {
            $patient->contact2 = $contact2;
        }
        if ($request->has('num_cmu_up') || $request->has('num_cmu')) {
            $patient->num_cmu = $request->num_cmu_up ?? $request->num_cmu;
        }
        if ($request->filled('ethnie_up')) {
            $patient->ethnie = $request->ethnie_up;
        }
        if ($request->filled('type_piece_up')) {
            $patient->type_piece = $request->type_piece_up;
        }
        if ($request->filled('numero_identite_up')) {
            $patient->numero_identite = $request->numero_identite_up;
        }
        if ($request->filled('img_url')) {
            $patient->img_url = $request->img_url;
        }
        if ($request->filled('situation_matrimoniale_up')) {
            $patient->situation_matrimoniale = $request->situation_matrimoniale_up;
        }
        if ($request->filled('address_up')) {
            $patient->address = $request->address_up;
        }
        if ($request->has('nbre_enfant_up')) {
            $patient->nbre_enfant = $request->nbre_enfant_up ?? 0;
        }
        if ($request->filled('nom_personne_cas_urgence_up')) {
            $patient->nom_personne_cas_urgence = $request->nom_personne_cas_urgence_up;
        }
        if ($request->filled('telephone_personne_cas_urgence_up')) {
            $patient->telephone_personne_cas_urgence = $request->telephone_personne_cas_urgence_up;
        }
        if ($request->filled('lien_personne_cas_urgence_up')) {
            $patient->lien_personne_cas_urgence = $request->lien_personne_cas_urgence_up;
        }

        $user->save();
        $patient->save();

        $authUser = auth()->user();
        $rawHospitalId = optional(optional($authUser)->secretariat)->hospital_id 
            ?? optional(optional($authUser)->infirmier)->hospital_id 
            ?? optional(optional($authUser)->doctor)->hospital_id 
            ?? optional($authUser)->hospital_id 
            ?? (function_exists('getUserHospitalId') ? getUserHospitalId() : null);

        $hospitalId = (!empty($rawHospitalId) && is_numeric($rawHospitalId)) ? (int)$rawHospitalId : ((!empty($patient->hospital_id) && is_numeric($patient->hospital_id)) ? (int)$patient->hospital_id : 1);

        //specifié le passage du patient dans l'hopital
        if (!PassagePatient::where('hospital_id', $hospitalId)->where('patient_id', $patient->id)->exists()) {
            $passage = new PassagePatient();
            $passage->libelle = 'Passage compte';
            $passage->hospital_id = $hospitalId;
            $passage->patient_id = $patient->id;
            $passage->date = date('Y-m-d');
            $passage->save();
        }

        $secretaire = $authUser ? Secretaire::where('user_id', $authUser->id)->first() : null;
        $caissiere = $authUser ? Caissiere::where('user_id', $authUser->id)->first() : null;
        $admissionHospitalId = optional(optional($secretaire)->hospital)->id 
            ?? optional(optional($caissiere)->hospital)->id 
            ?? $hospitalId;

        //verifer l'admission
        if ($request->admission_patient_up == 'Oui') {
            $isGtc = ($request->has('gratuite') && in_array($request->gratuite, ['gratuit', '1', 'on', 'true', 'oui', 'GTC', 'gtc'])) 
                || ($request->input('is_gtc') == '1');

            $prestationHopital = PrestationHospital::with('prestationService')->find($request->prestation_service_id);
            $montantNormal = floatval($request->montant_normal ?? 0);
            if ($montantNormal <= 0 && $prestationHopital) {
                $montantNormal = floatval($prestationHopital->prix);
            }
            if ($montantNormal <= 0 && $request->filled('montant')) {
                $montantNormal = floatval($request->montant);
            }

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
                'type_admission' => $isGtc ? 'GTC (Gratuité)' : ($request->type_admission_id ?? 'Admission'),
                'mode_entree' => $request->mode_entree,
                'montant' => $isGtc ? 0 : ($request->montant ?? 0),
                'montant_normal' => $montantNormal,
                'statut_paiement' => $isGtc ? 1 : 0,
                'statut_validation' => $isGtc ? 1 : 0,
                'date_paiement' => $isGtc ? Carbon::now()->format('Y-m-d') : null,
                'mode_paiement' => $isGtc ? 'gtc' : null,
                'motif_consultation' => $request->motif_consultation,
            ]);

            //save payment
            $payment = new Payment();
            $payment->type = 'admission';
            $payment->date = Carbon::now()->format('Y-m-d');
            $payment->prix = $isGtc ? 0 : ($request->montant ?? 0);
            $payment->prix_normal = $montantNormal;
            $payment->hospital_id = $admissionHospitalId;
            $payment->admission_id = $admission->id;
            $payment->status = $isGtc ? 'success' : 'pending';
            $payment->mode_paiement = $isGtc ? 'gtc' : null;
            if ($caissiere) {
                $payment->caissiere_id = $caissiere->id;
            } else {
                $firstCashier = \App\Models\Caissiere::where('hospital_id', $admissionHospitalId)->first();
                if ($firstCashier) {
                    $payment->caissiere_id = $firstCashier->id;
                }
            }
            $payment->save();

            // Audit Trail
            $patName = ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '');
            $prestName = optional($prestationHopital->prestationService)->libelle ?? 'Prestation';
            AuditLogService::log('AFFECTATION_PATIENT', 'SECRETARIAT', "Affectation du patient {$patName} [{$patient->code_patient}] pour {$prestName} (N° Adm: {$admission->code_admission})", [
                'admission_id' => $admission->id,
                'patient_id' => $patient->id,
                'type' => $admission->type_admission,
                'montant' => $admission->montant
            ], null, $admissionHospitalId);

            // Si GTC (Gratuité Ciblée) : envoi direct à l'infirmerie sans passer à la caisse
            if ($isGtc) {
                $isSoinsInfirmiers = false;
                if ($prestationHopital && $prestationHopital->prestationService) {
                    $servName = $prestationHopital->prestationService->libelle ?? '';
                    $servId = $prestationHopital->prestationService->service_id ?? 0;
                    $isSoinsInfirmiers = ($servId == 5) || preg_match('/infirmier|soin|pansement/i', $servName);
                }

                if ($isSoinsInfirmiers) {
                    $careRequest = new \App\Models\CareRequested();
                    $careRequest->type = optional(optional($prestationHopital)->prestationService)->libelle ?? 'Soins infirmiers';
                    $careRequest->admission_id = $admission->id;
                    $careRequest->status = 'pending';
                    $careRequest->save();
                } else {
                    $nbConsult = \App\Models\Consultation::where('patient_id', $patient->id)->count();
                    $consultation = new \App\Models\Consultation();
                    $consultation->date_consultation = Carbon::now()->format('Y-m-d');
                    $consultation->code_consultation = 'CONSULT' . substr($patient->code_patient, 2) . ($nbConsult + 1);
                    $consultation->admission_id = $admission->id;
                    $consultation->hospital_id = $admissionHospitalId;
                    $consultation->patient_id = $patient->id;
                    if ($admission->doctor_id) {
                        $consultation->doctor_id = $admission->doctor_id;
                        $consultation->status_inf = 0;
                    }
                    $consultation->infirmier_id = $admission->infirmier_id;
                    $consultation->prestation_hospital_id = $admission->prestation_hopital_id;
                    $consultation->montant = 0;
                    $consultation->save();
                }

                try {
                    \App\Services\AccountingService::recordAdmissionEntry($admission);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Erreur enregistrement comptable GTC: " . $e->getMessage());
                }
            }
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
        $code_patient = $request->input('code_patient') ?? $request->input('dm') ?? $request->input('code_dm');
        $telephone = $request->input('telephone');
        $fullname = $request->input('fullname');
        $birth_date = $request->input('birth_date');
        $num_cmu = $request->input('num_cmu');

        $data = Patient::query();

        if (!empty($code_patient)) {
            $cleanCode = trim(str_replace(' ', '', $code_patient));
            $data->where('code_patient', 'like', '%' . $cleanCode . '%');
        }

        if (!empty($telephone)) {
            $rawTel = trim($telephone);
            $cleanTel = preg_replace('/[^0-9]/', '', $rawTel);
            if (str_starts_with($cleanTel, '225') && strlen($cleanTel) > 3) {
                $cleanTel = substr($cleanTel, 3);
            }
            $data->where(function ($q) use ($rawTel, $cleanTel) {
                // 1. Recherche par téléphone du patient
                $q->where('telephone', 'like', '%' . $rawTel . '%');
                if (!empty($cleanTel)) {
                    $q->orWhere('telephone', 'like', '%' . $cleanTel . '%');
                }
                // 2. Exception : Téléphone de la personne en cas d'urgence UNIQUEMENT pour les nouveau-nés
                $q->orWhere(function ($subQ) use ($rawTel, $cleanTel) {
                    $subQ->where(function ($isNewborn) {
                        $isNewborn->whereNotNull('mere_id')
                                  ->orWhereNotNull('registre_naissance_id')
                                  ->orWhereHas('user', function ($uq) {
                                      $uq->where('name', 'like', 'Bébé%')
                                         ->orWhere('name', 'like', 'Bebe%')
                                         ->orWhere('name', 'like', 'Enfant%');
                                  });
                    })->where(function ($tq) use ($rawTel, $cleanTel) {
                        $tq->where('telephone_personne_cas_urgence', 'like', '%' . $rawTel . '%');
                        if (!empty($cleanTel)) {
                            $tq->orWhere('telephone_personne_cas_urgence', 'like', '%' . $cleanTel . '%');
                        }
                    });
                });
            });
        }

        if (!empty($num_cmu)) {
            $data->where('num_cmu', 'like', '%' . trim($num_cmu) . '%');
        }

        if (!empty($fullname)) {
            $terms = array_values(array_filter(explode(" ", trim($fullname))));
            if (count($terms) >= 2) {
                $first = $terms[0];
                $second = implode(" ", array_slice($terms, 1));
                $data->where(function ($query) use ($first, $second) {
                    // 1. Recherche directe sur le nom / prénom du patient
                    $query->whereHas('user', function ($q) use ($first, $second) {
                        $q->where(function ($sub) use ($first, $second) {
                            $sub->where('name', 'like', '%' . $first . '%')
                                ->where('prenom', 'like', '%' . $second . '%');
                        })->orWhere(function ($sub) use ($first, $second) {
                            $sub->where('name', 'like', '%' . $second . '%')
                                ->where('prenom', 'like', '%' . $first . '%');
                        });
                    })
                    // 2. Exception : Nom de la personne d'urgence / Mère UNIQUEMENT pour les nouveau-nés
                    ->orWhere(function ($subQuery) use ($first, $second) {
                        $subQuery->where(function ($isNewborn) {
                            $isNewborn->whereNotNull('mere_id')
                                      ->orWhereNotNull('registre_naissance_id')
                                      ->orWhereHas('user', function ($uq) {
                                          $uq->where('name', 'like', 'Bébé%')
                                             ->orWhere('name', 'like', 'Bebe%')
                                             ->orWhere('name', 'like', 'Enfant%')
                                             ->orWhere('name', 'like', 'Nouveau-né%')
                                             ->orWhere('name', 'like', 'Nouveau ne%');
                                      });
                        })->where(function ($contactQuery) use ($first, $second) {
                            $contactQuery->where(function ($cq) use ($first, $second) {
                                $cq->where('nom_personne_cas_urgence', 'like', '%' . $first . '%')
                                   ->where('nom_personne_cas_urgence', 'like', '%' . $second . '%');
                            })->orWhereHas('mere.user', function ($mq) use ($first, $second) {
                                $mq->where(function ($subM) use ($first, $second) {
                                    $subM->where('name', 'like', '%' . $first . '%')
                                         ->where('prenom', 'like', '%' . $second . '%');
                                })->orWhere(function ($subM) use ($first, $second) {
                                    $subM->where('name', 'like', '%' . $second . '%')
                                         ->where('prenom', 'like', '%' . $first . '%');
                                });
                            });
                        });
                    });
                });
            } else if (count($terms) === 1) {
                $term = $terms[0];
                $data->where(function ($query) use ($term) {
                    // 1. Recherche directe sur le nom / prénom du patient
                    $query->whereHas('user', function ($q) use ($term) {
                        $q->where('name', 'like', '%' . $term . '%')
                            ->orWhere('prenom', 'like', '%' . $term . '%');
                    })
                    // 2. Exception : Nom de la personne d'urgence / Mère UNIQUEMENT pour les nouveau-nés
                    ->orWhere(function ($subQuery) use ($term) {
                        $subQuery->where(function ($isNewborn) {
                            $isNewborn->whereNotNull('mere_id')
                                      ->orWhereNotNull('registre_naissance_id')
                                      ->orWhereHas('user', function ($uq) {
                                          $uq->where('name', 'like', 'Bébé%')
                                             ->orWhere('name', 'like', 'Bebe%')
                                             ->orWhere('name', 'like', 'Enfant%')
                                             ->orWhere('name', 'like', 'Nouveau-né%')
                                             ->orWhere('name', 'like', 'Nouveau ne%');
                                      });
                        })->where(function ($contactQuery) use ($term) {
                            $contactQuery->where('nom_personne_cas_urgence', 'like', '%' . $term . '%')
                                         ->orWhereHas('mere.user', function ($mq) use ($term) {
                                             $mq->where('name', 'like', '%' . $term . '%')
                                                ->orWhere('prenom', 'like', '%' . $term . '%');
                                         });
                        });
                    });
                });
            }
        }

        if (!empty($birth_date)) {
            $data->where('birth_date', trim($birth_date));
        }

        $patients = $data->with(['user', 'mere.user', 'lieuNaissance', 'residenceActuelle', 'residenceHabituelle', 'declarationDeces.deces'])->get();

        // Audit Trail pour la recherche de patient
        $searchTerms = array_filter([
            'code' => $code_patient,
            'nom' => $fullname,
            'tel' => $telephone,
            'cmu' => $num_cmu,
            'date_naissance' => $birth_date
        ]);
        if (!empty($searchTerms)) {
            $descParts = [];
            foreach ($searchTerms as $k => $v) { $descParts[] = "$k: $v"; }
            AuditLogService::log(
                'RECHERCHE_PATIENT',
                'PATIENTS',
                "Recherche de patient(s) : " . implode(', ', $descParts) . " (" . $patients->count() . " résultat(s))",
                ['criteres' => $searchTerms, 'resultats_count' => $patients->count()]
            );
        }

        $patients->transform(function ($patient) {
            $isDeceased = ($patient->status === 0 || $patient->status === '0') || !is_null($patient->declarationDeces);
            $patient->is_deceased = $isDeceased;
            if ($patient->declarationDeces && $patient->declarationDeces->deces) {
                $deces = $patient->declarationDeces->deces;
                $patient->deces_date = $deces->date ? (\Carbon\Carbon::hasFormat($deces->date, 'Y-m-d') ? \Carbon\Carbon::parse($deces->date)->format('d/m/Y') : $deces->date) : null;
                $patient->deces_heure = $deces->heure;
                $patient->deces_lieu = $deces->lieu;
            } else {
                $patient->deces_date = null;
                $patient->deces_heure = null;
                $patient->deces_lieu = null;
            }
            return $patient;
        });

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
            $isGtc = ($request->has('gratuite') && in_array($request->gratuite, ['gratuit', '1', 'on', 'true', 'oui', 'GTC', 'gtc'])) 
                || ($request->input('is_gtc') == '1');

            $prestationHopital = \App\Models\PrestationHospital::with('prestationService')->find($request->prestation_service_id);
            $montantNormal = floatval($request->montant_normal ?? 0);
            if ($montantNormal <= 0 && $prestationHopital) {
                $montantNormal = floatval($prestationHopital->prix);
            }
            if ($montantNormal <= 0 && $request->filled('montant')) {
                $montantNormal = floatval($request->montant);
            }

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
                'type_admission' => $isGtc ? 'GTC (Gratuité)' : ($request->type_admission_id ?? 'Admission'),
                'mode_entree' => $request->mode_entree,
                'montant' => $isGtc ? 0 : ($request->montant ?? 0),
                'montant_normal' => $montantNormal,
                'statut_paiement' => $isGtc ? 1 : 0,
                'statut_validation' => $isGtc ? 1 : 0,
                'date_paiement' => $isGtc ? Carbon::now()->format('Y-m-d') : null,
                'mode_paiement' => $isGtc ? 'gtc' : null,
                'motif_consultation' => $request->motif_consultation,
            ]);

            // Enregistrement du paiement
            $payment = new Payment();
            $payment->type = 'admission';
            $payment->date = Carbon::now()->format('Y-m-d');
            $payment->prix = $isGtc ? 0 : ($request->montant ?? 0);
            $payment->prix_normal = $montantNormal;
            $payment->hospital_id = $hospitalId;
            $payment->admission_id = $admission->id;
            $payment->status = $isGtc ? 'success' : 'pending';
            $payment->mode_paiement = $isGtc ? 'gtc' : null;
            if ($caissiere) {
                $payment->caissiere_id = $caissiere->id;
            } else {
                $firstCashier = \App\Models\Caissiere::where('hospital_id', $hospitalId)->first();
                if ($firstCashier) {
                    $payment->caissiere_id = $firstCashier->id;
                }
            }
            $payment->save();

            // Audit Trail
            $patName = ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? '');
            $prestName = optional($prestationHopital->prestationService)->libelle ?? 'Prestation';
            AuditLogService::log('AFFECTATION_PATIENT', 'SECRETARIAT', "Affectation directe du patient {$patName} [{$patient->code_patient}] pour {$prestName} (N° Adm: {$admission->code_admission})", [
                'admission_id' => $admission->id,
                'patient_id' => $patient->id,
                'type' => $admission->type_admission,
                'montant' => $admission->montant
            ], null, $hospitalId);

            // Si GTC (Gratuité Ciblée) : envoi direct à l'infirmerie sans passer à la caisse
            if ($isGtc) {
                $isSoinsInfirmiers = false;
                if ($prestationHopital && $prestationHopital->prestationService) {
                    $servName = $prestationHopital->prestationService->libelle ?? '';
                    $servId = $prestationHopital->prestationService->service_id ?? 0;
                    $isSoinsInfirmiers = ($servId == 5) || preg_match('/infirmier|soin|pansement/i', $servName);
                }

                if ($isSoinsInfirmiers) {
                    $careRequest = new \App\Models\CareRequested();
                    $careRequest->type = optional(optional($prestationHopital)->prestationService)->libelle ?? 'Soins infirmiers';
                    $careRequest->admission_id = $admission->id;
                    $careRequest->status = 'pending';
                    $careRequest->save();
                } else {
                    $nbConsult = \App\Models\Consultation::where('patient_id', $patient->id)->count();
                    $consultation = new \App\Models\Consultation();
                    $consultation->date_consultation = Carbon::now()->format('Y-m-d');
                    $consultation->code_consultation = 'CONSULT' . substr($patient->code_patient, 2) . ($nbConsult + 1);
                    $consultation->admission_id = $admission->id;
                    $consultation->hospital_id = $hospitalId;
                    $consultation->patient_id = $patient->id;
                    if ($admission->doctor_id) {
                        $consultation->doctor_id = $admission->doctor_id;
                        $consultation->status_inf = 0;
                    }
                    $consultation->infirmier_id = $admission->infirmier_id;
                    $consultation->prestation_hospital_id = $admission->prestation_hopital_id;
                    $consultation->montant = 0;
                    $consultation->save();
                }

                try {
                    \App\Services\AccountingService::recordAdmissionEntry($admission);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Erreur enregistrement comptable GTC: " . $e->getMessage());
                }
            }
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
        $patientId = $request->input('patient_id') ?? $request->route('id');
        $patient = Patient::with(['user', 'lieuNaissance', 'residenceActuelle', 'residenceHabituelle', 'declarationDeces.deces'])->find($patientId);

        if ($patient) {
            $isDeceased = ($patient->status === 0 || $patient->status === '0') || !is_null($patient->declarationDeces);
            $patient->is_deceased = $isDeceased;
            if ($patient->declarationDeces && $patient->declarationDeces->deces) {
                $deces = $patient->declarationDeces->deces;
                $patient->deces_date = $deces->date ? (\Carbon\Carbon::hasFormat($deces->date, 'Y-m-d') ? \Carbon\Carbon::parse($deces->date)->format('d/m/Y') : $deces->date) : null;
                $patient->deces_heure = $deces->heure;
                $patient->deces_lieu = $deces->lieu;
            } else {
                $patient->deces_date = null;
                $patient->deces_heure = null;
                $patient->deces_lieu = null;
            }
        }

        return response()->json(['patient' => $patient]);
    }
}
