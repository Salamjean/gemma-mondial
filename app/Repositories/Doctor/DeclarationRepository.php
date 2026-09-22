<?php

namespace App\Repositories\Doctor;

use App\Http\Requests\Doctor\DeclarationDecesRequest;
use App\Http\Requests\Doctor\DeclarationNaissanceRequest;
use App\Models\Declaration;
use App\Models\DeclarationDeces;
use App\Models\DeclarationNaissance;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeclarationRepository
{
    public function __construct()
    {
        //
    }

    public function search(Request $request)
    {
    }

    public function indexDeces($search = null)
    {
        $hospitalId = Auth::user()->doctor->hospital_id ?? Auth::user()->hospital_id;
        $query = Declaration::with(['patient.user', 'deces', 'doctor.user'])
            ->where('type', 'death')
            ->where('hospital_id', $hospitalId)
            ->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('patient.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%");
                  })
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('code_patient', 'like', "%{$search}%");
                  });
            });
        }

        return $query->paginate(10);
    }

    public function storeDirectDeces(Request $request)
    {
        $request->validate([
            'patient_mode' => 'required|in:existing,new',
            'date' => 'required',
            'heure' => 'required',
            'cause_initiale' => 'required|string',
            'cause_directe' => 'required|string',
        ]);

        $hospitalId = Auth::user()->doctor->hospital_id ?? Auth::user()->hospital_id;
        $doctorId = Auth::user()->doctor->id ?? null;
        $hospital = optional(Auth::user()->doctor)->hospital ?? \App\Models\Hospital::find($hospitalId);

        // Détermination du lieu de décès
        $lieu = '';
        if ($request->lieu_type === 'hospital') {
            $lieu = $hospital->label ?? 'Centre Hospitalier';
            if (!empty($request->service_hospital)) {
                $lieu .= ' - ' . trim($request->service_hospital);
            }
        } elseif ($request->lieu_type === 'hors') {
            $lieu = $request->lieu_autre ?? $request->lieu ?? 'Hors établissement';
        } else {
            $lieu = $request->lieu ?? ($hospital->label ?? 'Centre Hospitalier');
        }

        // 1. Détermination ou Création du Patient
        if ($request->patient_mode === 'new') {
            $request->validate([
                'nom' => 'required|string|max:100',
                'genre' => 'required|in:masculin,feminin',
            ]);

            $user = new User();
            $user->name = strtoupper(trim($request->nom));
            $user->prenom = ucwords(trim($request->prenom ?? ''));
            $user->email = 'deces_' . time() . '_' . rand(100, 999) . '@hopital.loc';
            $user->password = Hash::make('deces' . rand(1000, 9999));
            $user->role_as = 'patient';
            $user->save();

            // Date de naissance
            $birthDate = null;
            if (!empty($request->birth_date)) {
                if (str_contains($request->birth_date, '-')) {
                    $birthDate = Carbon::parse($request->birth_date)->format('d/m/Y');
                } else {
                    $birthDate = $request->birth_date;
                }
            } elseif (!empty($request->age_estime)) {
                $birthDate = Carbon::now()->subYears((int) $request->age_estime)->format('d/m/Y');
            }

            $patientCount = Patient::count() + 1;
            $codePatient = 'DM' . date('Ymd') . $patientCount;

            $patient = new Patient();
            $patient->user_id = $user->id;
            $patient->code_patient = $codePatient;
            $patient->hospital_id = $hospitalId;
            $patient->doctor_id = $doctorId;
            $patient->gender = $request->genre;
            $patient->birth_date = $birthDate;
            $patient->telephone = $request->telephone ?? null;
            $patient->profession = $request->profession ?? null;
            $patient->address = $request->lieu_residence ?? null;
            $patient->nbre_enfant = 0;
            $patient->status = 0; // Défunt
            $patient->save();

        } else {
            $request->validate([
                'patient_id' => 'required|exists:patients,id',
            ]);

            $patient = Patient::with('user')->find($request->patient_id);
            if (!$patient) {
                return ['status' => 'error', 'message' => 'Patient introuvable.'];
            }

            $person = $request->person ?? 'patient';
            $exist = Declaration::where('patient_id', $patient->id)
                ->where('type', 'death')
                ->whereHas('deces', function (Builder $query) use ($person) {
                    $query->where('person', $person);
                })
                ->first();

            if ($exist && $person === 'patient') {
                return ['status' => 'error', 'message' => 'Ce patient a déjà été déclaré décédé.'];
            }
        }

        // 2. Formatage de la date de décès
        $dateDeces = null;
        if (str_contains($request->date, '/')) {
            $parts = explode('/', $request->date);
            if (count($parts) === 3) {
                $dateDeces = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            }
        } elseif (str_contains($request->date, '-')) {
            $dateDeces = $request->date;
        } else {
            $dateDeces = date('Y-m-d');
        }

        // 3. Calcul de l'âge au décès
        $age = 0;
        if ($request->person === 'enfant') {
            $age = 0;
        } elseif (!empty($request->age_estime)) {
            $age = (int) $request->age_estime;
        } elseif (!empty($patient->birth_date)) {
            try {
                if (str_contains($patient->birth_date, '/')) {
                    $age = Carbon::createFromFormat('d/m/Y', $patient->birth_date)->diffInYears(Carbon::now());
                } else {
                    $age = Carbon::parse($patient->birth_date)->diffInYears(Carbon::now());
                }
            } catch (\Exception $e) {
                $age = 0;
            }
        }

        // 4. Création de la Déclaration
        $counter = Declaration::where('hospital_id', $hospitalId)->where('type', 'death')->count();
        $codeSub = $patient->code_patient ? substr($patient->code_patient, 2) : ('P' . $patient->id);

        $declaration = new Declaration();
        $declaration->type = 'death';
        $declaration->reference = 'CMD' . $codeSub . $hospitalId . ($counter + 1);
        $declaration->hospital_id = $hospitalId;
        $declaration->doctor_id = $doctorId ?? 1;
        $declaration->consultation_id = $request->consultation_id ?? null;
        $declaration->patient_id = $patient->id;
        $declaration->save();

        // 5. Création du détail de décès
        $death = new DeclarationDeces();
        $death->reference = 'DD' . $codeSub . $hospitalId . ($counter + 1);
        $death->numero_declaration = $counter + 1;
        $death->declaration_id = $declaration->id;
        $death->person = $request->person ?? 'patient';
        $death->date = $dateDeces;
        $death->heure = $request->heure;
        $death->lieu = $lieu;
        $death->nombre = 1;
        $death->observations = $request->observation ?? $request->observations ?? null;
        $death->milieu_residence = $request->milieu_residence ?? 'Urbain';
        $death->cause_initiale = $request->cause_initiale;
        $death->cause_directe = $request->cause_directe;
        $death->deces_maternel = $request->deces_maternel ?? 'non';
        $death->genre = $request->genre ?? $patient->gender ?? 'masculin';
        $death->age = $age;
        $death->save();

        // Si c'est le patient lui-même qui est décédé, marquer son statut à 0
        if (($request->person ?? 'patient') === 'patient') {
            $patient->status = 0;
            $patient->save();
        }

        return [
            'status' => 'success',
            'message' => 'La déclaration de décès a été enregistrée avec succès.',
            'declaration_id' => $declaration->id,
        ];
    }

    public function showDeces($id)
    {
        return Declaration::findOrFail($id);
    }

    public function storeDeces(DeclarationDecesRequest $request)
    {
        $request->validated();

        if ($request->person != 'enfant' && $request->person != 'patient')
            return ['status' => 'error', 'message' => 'Erreur sur l\'individu concerné.'];

        $patient = Patient::find($request->patient_id);
        if (!$patient)
            return ['status' => 'error', 'message' => 'Patient introuvable.'];

        if ($patient->gender != 'feminin' && $request->person == 'enfant')
            return ['status' => 'error', 'message' => 'Un homme ne peut pas enfanté. Veuillez choisir un patient qui répond au exigence.'];

        $person = $request->person;

        $exist = Declaration::where('patient_id', $request->patient_id)->where('type', 'death')->whereHas('deces', function (Builder $query) use ($person) {
            $query->where('person', $person);
        })->with('deces')->first();
        if ($exist) {
            if ($exist->deces->person === 'patient')
                return ['status' => 'error', 'message' => 'Ce patient a déjà été déclaré décédé.'];
        }

        //formatage date
        $jour = substr($request->date, 0, 2);
        $mois = substr($request->date, 3, 2);
        $annee = substr($request->date, 8, 2);


        $counter = Declaration::where('hospital_id', Auth::user()->doctor->hospital_id)->where('type', 'death')->count();

        $declaration = new Declaration();
        $declaration->type = 'death';
        $declaration->reference = 'CMD' . substr($patient->code_patient, 2) . Auth::user()->doctor->hospital_id . $counter + 1;
        $declaration->hospital_id = Auth::user()->doctor->hospital_id;
        $declaration->doctor_id = Auth::user()->doctor->id;
        $declaration->consultation_id = $request->consultation_id ?? null;
        $declaration->patient_id = $request->patient_id;

        $declaration->save();

        if (!empty($request->consultation_id)) {
            $consultation = \App\Models\Consultation::find($request->consultation_id);
            if ($consultation) {
                $consultation->status = 1;
                $consultation->save();
            }
        }

        $death = new DeclarationDeces();
        //reference : dd-hospital_id, date, (counter + 1)
        $death->reference = 'DD' . substr($patient->code_patient, 2) . Auth::user()->doctor->hospital_id . $counter + 1;
        $death->numero_declaration = $counter + 1;
        $death->declaration_id = $declaration->id;
        $death->person = $request->person;
        $death->date = $annee . '-' . $mois . '-' . $jour;
        $death->heure = $request->heure;
        $death->lieu = $request->lieu;
        $death->nombre = $request->nombre;
        $death->observations = $request->observation;
        $death->milieu_residence = $request->milieu_residence;
        $death->cause_initiale = $request->cause_initiale;
        $death->cause_directe = $request->cause_directe;
        $death->deces_maternel = $request->deces_maternel ?? 'oui';

        if ($request->person == 'enfant') {

            if ($patient->gender != 'feminin')
                return ['status' => 'error', 'message' => 'Un homme ne peut pas enfanté. Veillez choisir un patient qui répond au exigence.'];

            $death->genre = $request->genre;
            $death->age = 0;
        }

        if ($request->person == 'patient') {
            $death->genre = $patient->gender;
            $age = Carbon::parse($patient->birth_date)->diffInYears(Carbon::now());
            $death->age = $age;
            $patient->status = 0;
            $patient->save();
        }

        $death->save();

                return ['status' => 'success', 'message' => 'Déclaration de décès enregistrée avec succès.', 'id' => $declaration->id];
    }



    //naissance
    public function indexNaissance()
    {
        return Declaration::with('patient.user')->with('naissance')->where('type', 'birth')->where('hospital_id', Auth::user()->doctor->hospital_id)->get();
    }

    public function showNaissance($id)
    {
        return Declaration::findOrFail($id);
    }

    public function storeNaissance(DeclarationNaissanceRequest $request)
    {
        $request->validated();

        $patient = Patient::find($request->patient_id);
        if (!$patient)
            return ['status' => 'error', 'message' => 'Patient introuvable.'];

        if ($patient->gender === 'masculin')
            return ['status' => 'error', 'message' => 'Un homme ne peut pas enfanté. Veuillez choisir un patient qui répond au exigence.'];

        $response = $this->birthHelpers($request);
        if($response['status'] == 'error')
            return ['status' => 'error', 'message' => 'Erreur dans les données renseignées.'];


        //formatage date
        $jour = substr($request->date, 0, 2);
        $mois = substr($request->date, 3, 2);
        $annee = substr($request->date, 8, 2);

        $i = 0;
        while ($i < $request->nombre) {

            // +1 enfant de la mère
            $patient->nbre_enfant = $patient->nbre_enfant + $i + 1;
            $patient->save();

            //create user
            $user = User::create([
                "name" => "NOUVEAU-NÉ DE " . strtoupper($patient->user->name ?? ''),
                "prenom" => $patient->user->prenom ?? '',
                "mere_id" => $patient->id,
                "email" => strtolower("$patient->code_patient$patient->nbre_enfant@patient.com"),
                "password" => Hash::make('1234'),
                "role_as" => 'patient',
            ]);

            $data = $request->validate([
                "genre" . $i + 1 => 'required',
            ]);
            $genre = $data['genre' . $i + 1];


            $dataNaissRef = dateNaiss($request->date);
            $countNaissRef = countNaiss($request->date);

            $enfant = Patient::create([
                'user_id' => $user->id,
                'mere_id' => $patient->id,
                'doctor_id' => Auth()->user()->doctor->id,
                'hospital_id' => Auth()->user()->doctor->hospital->id,
                'type_assurance_id' =>  null,
                "gender" => $genre,
                'code_patient' => "DM$dataNaissRef$countNaissRef"."225",
                'birth_date' => $request->date,
                'lieu_de_naissance_id' =>  Auth()->user()->doctor->hospital->localite,
                'telephone' => $patient->telephone,
                'nbre_enfant' => 0,
                'nom_personne_cas_urgence' => ($patient->user->name ?? '') . ' ' . ($patient->user->prenom ?? ''),
                'telephone_personne_cas_urgence' => $patient->telephone,
                'lien_personne_cas_urgence' => "Mère",
                'status' => 1,
            ]);

            $i++;
        }


        $counter = Declaration::where('hospital_id', Auth::user()->doctor->hospital_id)->where('type', 'birth')->count();

        $declaration = new Declaration();
        $declaration->type = 'birth';
        $declaration->reference = 'CMN' . substr($patient->code_patient, 2) . Auth::user()->doctor->hospital_id . $counter + 1;
        $declaration->hospital_id = Auth::user()->doctor->hospital_id;
        $declaration->doctor_id = Auth::user()->doctor->id;
        $declaration->consultation_id = $request->consultation_id ?? null;
        $declaration->patient_id = $patient->id;
        $declaration->save();

        //declaration de naissance
        $birth = new DeclarationNaissance();
        //reference : dd-hospital_id, date, (counter + 1)
        $birth->reference = 'DN' . substr($patient->code_patient, 2) . Auth::user()->doctor->hospital_id . $counter + 1;
        $birth->numero_declaration = $counter + 1;

        $birth->declaration_id = $declaration->id;
        $birth->date = $annee . '-' . $mois . '-' . $jour;
        $birth->heure = $response['data']['hour'];
        $birth->lieu = $request->lieu;
        $birth->enfant_id = $enfant->id;
        $birth->nombre = $request->nombre;
        $birth->observations = $request->observation;
        $birth->genre = $response['data']['gender'];
        $birth->nee = $response['data']['born'];
        $birth->save();

        return ['status' => 'success', 'message' => 'Déclaration de naissance  enregistrée avec succès.'];
    }

    private function birthHelpers(DeclarationNaissanceRequest $request)
    {
        $request->validated();

        if (intval($request->nombre) > 0) {
            for ($i = 0; $i < $request->nombre; $i++) {

                $data = $request->validate([
                    "genre" . $i + 1 => 'required',
                    "nee" . $i + 1 => 'required',
                    "heure" . $i + 1 => 'required',
                ]);

                if ($i == 0) {
                    $gender = $data['genre' . $i + 1];
                    $born = $data['nee' . $i + 1];
                    $hour = $data['heure' . $i + 1];
                } else {
                    $gender .= ', ' . $data['genre' . $i + 1];
                    $born .= ', ' . $data['nee' . $i + 1];
                    $hour .= ', ' . $data['heure' . $i + 1];
                }
            }

            return ['status' => 'success', 'data' => ['gender' => $gender, 'born' => $born, 'hour' => $hour]];
        } else {
            return ['status' => 'error'];
        }
    }
}
