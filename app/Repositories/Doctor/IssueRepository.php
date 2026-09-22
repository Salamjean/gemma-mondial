<?php

namespace App\Repositories\Doctor;

use Carbon\Carbon;
use App\Models\Doctor;
use App\Models\Payment;
use App\Models\Registre;
use App\Models\Admission;
use App\Models\Pathologie;
use App\Models\Traitement;
use App\Models\Observation;
use App\Models\Consultation;
use Illuminate\Http\Request;
use App\Models\SoinsAdministre;



class IssueRepository
{
    public function __construct()
    {
        //
    }

    public function formulaireIssue($name)
    {
        $cleanName = strtolower(trim((string) $name));
        $title = 'Formulaire ';

        switch ($cleanName) {
            case 'declaration-naissance':
                $title .= 'de déclaration de naissance';
                break;
            case 'declaration-deces-patient':
            case 'décédé':
            case 'décédée':
            case 'decede':
            case 'decedee':
            case 'deces':
                $title .= 'de déclaration de décès de la patiente';
                break;
            case 'declaration-deces-enfant':
                $title .= 'de déclaration de décès du nouveau-né';
                break;
            case 'hospitalisation':
            case 'hospitalise':
            case 'hospitalisé':
            case 'hospitalisée':
                $title .= 'd\'hospitalisation';
                break;
            case 'observation':
                $title .= 'de mise en observation';
                break;
            case 'suite-couche':
            case 'suites-couches':
            case 'suites des couches':
                $title .= 'de suites de couches';
                break;
            case 'sortie':
            case 'domicile':
            case 'domiciles':
                $title .= 'de sortie de la patiente';
                break;
            case 'refere-interne':
            case 'refere_interne':
            case 'référée':
                $title .= 'de référence interne';
                break;
            case 'refere-externe':
            case 'refere_externe':
                $title .= 'de référence externe';
                break;
            case 'cas-presume-tb-resume':
                $title .= 'de cas présumé TB résumé';
                break;
            case 'a-revoir':
            case 'revoir':
                $title .= 'à revoir';
                break;
            case 'autre':
                $title .= 'complémentaire';
                break;
            case 'sortir-contre-avis-medical':
                $title .= 'de sortie contre avis médical';
                break;
            case 'grossesse-normale':
                $title .= 'de grossesse normale';
                break;
            case 'grossesse-risque':
                $title .= 'de grossesse à risque';
                break;

            default:
                $title .= 'de validation de sortie';
                break;
        }

        return $title;
    }

    public function storeJustification(Request $request)
    {
        $getScalar = function ($key, $default = null) use ($request) {
            $val = $request->input($key, $default);
            if (is_array($val)) {
                return count($val) > 0 ? (string) reset($val) : $default;
            }
            return $val ?? $default;
        };

        $admissionPatient = $getScalar('admission_patient', 'Non');
        $justification = $getScalar('justification', 'Sortie effectuée');
        $consultationId = $getScalar('consultation_id');

        $consultation = Consultation::find($consultationId);
        if (!$consultation) {
            return ['status' => 'error', 'message' => 'Consultation introuvable.'];
        }

        if ($admissionPatient == 'Oui' && $request->filled('prestation_service_id')) {
            $doctor = Doctor::where('user_id', auth()->user()->id)->first();
            $hospitalId = ($doctor && $doctor->hospital) ? $doctor->hospital->id : (auth()->user()->hospital_id ?? 1);
            $doctorId = $doctor ? $doctor->id : null;

            $admission = Admission::create([
                'code_admission' => codeAdmission(),
                'date_admission' => Carbon::now()->format('Y-m-d H:i:s'),
                'doctor_id' => $doctorId,
                'hospital_id' => $hospitalId,
                'patient_id' => $getScalar('patient_id', $consultation->patient_id),
                'infirmier_id' => $getScalar('infirmier_id'),
                'caissiere_id' => null,
                'prestation_hopital_id' => $getScalar('prestation_service_id'),
                'type_admission' => $getScalar('type_admission_id', 'Soins Infirmier'),
                'montant' => $getScalar('montant', 0),
                'montant_normal' => $getScalar('montant', 0),
                'motif_consultation' => $justification,
            ]);

            $payment = new Payment();
            $payment->date = Carbon::now()->format('Y-m-d');
            $payment->type = 'admission';
            $payment->prix = $getScalar('montant', 0);
            $payment->hospital_id = $hospitalId;
            $payment->admission_id = $admission->id;
            $payment->save();
        }

        if (Registre::where('consultation_id', $consultation->id)->exists()) {
            $registre = Registre::where('consultation_id', $consultation->id)->first();
        } else {
            $registre = Registre::create([
                "code" => codeRegistre($consultation->patient->code_patient, $consultation->patient->id),
                "type_consultation" => "consultation curative",
                "consultation_id" => $consultation->id,
                "issue_consultation" => "sortie",
            ]);
        }

        $registre->issue_consultation_justification = $justification;
        $registre->save();

        $consultation->status = 1;
        $consultation->save();

        return ['status' => 'success', 'message' => 'Issue de consultation validée.'];
    }

    public function storeObservation(Request $request)
    {

        $request->validate([
            'consultation_id' => 'required',
            'duree' => 'required',
        ]);

        $consultation = Consultation::find($request->consultation_id);
        if (!$consultation) {
            return ['status' => 'error', 'message' => 'Consultation introuvable.'];
        }

        if (!Registre::where('consultation_id', $consultation->id)->exists()) {
            Registre::create([
                "code" => codeRegistre($consultation->patient->code_patient, $consultation->patient->id),
                "type_consultation" => "consultation curative",
                "consultation_id" => $consultation->id,
                "issue_consultation" => "observation",
            ]);
        }

        //patologie
        parse_str($request->nom_pathologie, $PNom);
        parse_str($request->date_pathologie, $PDate);

        //traitements
        parse_str($request->nom_traitement, $TNom);
        parse_str($request->date_traitement, $TDate);

        //soins
        parse_str($request->nom_soins, $SNom);
        parse_str($request->date_soins, $SDate);

        $consultation = Consultation::find($request->consultation_id);

        $date_fin = new Carbon(date('Y-m-d H:i:s'));

        $heure = explode(":", $request->duree);

        if ($observation = Observation::where('consultation_id', $request->consultation_id)->first()) {

            $observation->update([
                "date_observation" => $request->date_Observation,
                "observations" => $request->observations,
                "diagnostic" => $request->diagnostic,
                "duree" => $request->duree,
                "date_debut" => date('Y-m-d H:i:s'),
                "date_fin" => $date_fin->addHours($heure[0])->addMinutes($heure[1]),
            ]);

            if ($observation->pathologies->count() > 0)
                $observation->pathologies->delete();

            if ($observation->traitements->count() > 0)
                $observation->traitements->delete();

            if ($observation->soins->count() > 0)
                $observation->soins->delete();
        } else {
            $observation = Observation::create([
                "code_observation" => codeObservation($consultation->patient->code_patient, $consultation->patient->id),
                "consultation_id" => $request->consultation_id,
                "date_observation" => $request->date_Observation,
                "observations" => $request->observations,
                "diagnostic" => $request->diagnostic,
                "duree" => $request->duree,
                "date_debut" => date('Y-m-d H:i:s'),
                "date_fin" => $date_fin->addHours($heure[0])->addMinutes($heure[1]),
            ]);
        }


        if ($PNom['nom_pathologie'][0] !== '') {
            for ($i = 0; $i < count($PNom['nom_pathologie']); $i++) {
                Pathologie::create([
                    "observation_id" => $observation->id,
                    "libelle" => $PNom['nom_pathologie'][$i],
                    "date" => $PDate['date_pathologie'][$i],
                ]);
            }
        }

        if ($TNom['nom_traitement'][0] !== '') {
            for ($i = 0; $i < count($TNom['nom_traitement']); $i++) {
                Traitement::create([
                    "observation_id" => $observation->id,
                    "libelle" => $TNom['nom_traitement'][$i],
                    "date" => $TDate['date_traitement'][$i],
                ]);
            }
        }

        if ($SNom['nom_soins'][0] !== '') {
            for ($i = 0; $i < count($SNom['nom_soins']); $i++) {
                SoinsAdministre::create([
                    "observation_id" => $observation->id,
                    "libelle" => $SNom['nom_soins'][$i],
                    "date" => $SDate['date_soins'][$i],
                ]);
            }
        }

        $consultation->status = 1;
        $consultation->save();

        return ['status' => 'success', 'message' => 'Patient mise en observation'];
    }
}
