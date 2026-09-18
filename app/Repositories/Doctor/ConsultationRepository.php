<?php

namespace App\Repositories\Doctor;

use App\Http\Requests\Doctor\ConsultationPostNataleRequest;
use App\Http\Requests\Doctor\ConsultationPreNataleRequest;
use App\Http\Requests\Doctor\ConsultationAccouchementRequest;
use App\Models\Registre;
use App\Models\Consultation;
use App\Models\OneSignalToken;
use App\Models\PrestationService;
use App\Models\RegistreAccouchement;
use App\Models\RegistreConsultationPostNatale;
use App\Models\RegistreConsultationPreNatale;
use App\Models\RendezVous;
use App\Repositories\SmsRepository;
use App\Services\OneSignalNotificationSender;
use Illuminate\Support\Facades\Http;

class ConsultationRepository
{
    public function __construct()
    {
        //
    }
    private static $ind = "225";

    public function model()
    {
        return  Consultation::class;
    }

    public function today()
    {
        return Consultation::where('doctor_id', auth()->user()->doctor->id)
            ->where('date_consultation', date('Y-m-d'))
            ->where('status_inf', 1)
            ->whereNull('call_channel')
            ->where('status', 0)
            ->where(function ($q) {
                $q->whereNull('orientation_infirmier')
                  ->orWhere('orientation_infirmier', '');
            })
            ->get();
    }

    public function history()
    {
        $doctorId = auth()->user()->doctor->id;
        return Consultation::orderByDESC("updated_at")
            ->where('doctor_id', $doctorId)
            ->withCount(['ordonnances', 'arret', "examen", "declaration", "registre"])
            ->with(["ordonnances", "arret", "examen", "declaration", "registre", "patient.user", "prestationHospital.prestationService"])
            ->get();
    }

    public function show($id)
    {
        $consultation = Consultation::findOrFail($id);

        return $consultation;
    }

    public function formulaireMotif($id)
    {

        $type = PrestationService::findOrFail($id);

        $title = 'Formulaire ';
        $section = 'consultation';
        switch ($type->libelle) {

            case 'Consultation':
                $title .= 'de consultation';
                $section = 'curative';
                break;
            case 'Consultation prénatale':
                $title .= 'de consultation prénatale';
                $section = 'consultation-pre-natale';
                break;

            case 'Consultation postnatale':
                $title .= 'de consultation postnatale';
                $section = 'consultation-post-natale';
                break;

            case 'Accouchement':
                $title .= 'd\'accouchement';
                $section = 'accouchement';
                break;

            default:
                $title .= 'de consultation';
                $section = 'curative';

                return [$section, $title];
        }

        return [$section, $title];
    }

    public function formulairePostConsultation($name)
    {
        $title = 'Formulaire ';
        switch ($name) {

            case 'arret-travail':
                $title .= 'd\'arret de travail';
                break;
            case 'ordonnance':
                $title .= 'd\'ordonnance';
                break;
            case 'examen':
                $title .= 'd\'examen';
                break;

            default:
                $title = 'introuvable';
                return  $title;
        }

        return $title;
    }

    public function storePostNatales(ConsultationPostNataleRequest $request)
    {
        $data = $request->except(['consultation_id', '_token', 'mode_sortie']);

        $consultation = Consultation::findOrFail($request->consultation_id);
        if ($request->motif_consultation)
            $consultation->motif_consultation = $request->motif_consultation;

        // $prescriptions = $data['prescription'];
        // $prescriptions != null ? explode(",", $prescriptions) : null;

        if (Registre::where('consultation_id', $request->consultation_id)->exists()) {
            $registre = Registre::where('consultation_id', $request->consultation_id)->first();

            $registre->issue_consultation = $request->mode_sortie;
            $registre->save();

            RegistreConsultationPostNatale::where('registre_id', $registre->id)->update($data);

            // return ['status' => 'success', 'message' => 'Données de régistre de consultation post natale modifiée avec succès.'];
        } else {
            $registre = new Registre();
            $registre->code = codeRegistre($consultation->patient->code_patient, $consultation->patient->id);
            $registre->type_consultation = 'consultation postnatale';
            $registre->consultation_id = $consultation->id;
            $registre->issue_consultation = $request->mode_sortie;
            $registre->save();

            $data['registre_id'] = $registre->id;

            RegistreConsultationPostNatale::create($data);
        }

        //consultation
        $consultation->tension_arterielle = $request->tension_arterielle ?? '';
        $consultation->temperature = $request->temperature ?? '';
        $consultation->saturation_oxygene = $request->saturation_oxygene ?? '';
        $consultation->taille = $request->taille ?? '';
        $consultation->poids = $request->poids ?? '';
        $consultation->imc = $request->imc ?? '';
        $consultation->pouls = $request->pouls ?? '';
        $consultation->gly_a_jeun = $request->gly_a_jeun ?? '';
        $consultation->gly_nn_jeun = $request->gly_nn_jeun ?? '';
        $consultation->perimetre_brach = $request->perimetre_brach ?? '';
        $consultation->status = 1;
        $consultation->save();

        if ($request->date_prochain_rdv) {

            if ($rd = RendezVous::where('consultation_id', $consultation->id)->first())
                $rd->delete();

            $rdv = new RendezVous();
            $rdv->consultation_id = $consultation->id;
            $rdv->doctor_id = $consultation->doctor_id;
            $rdv->patient_id = $consultation->patient_id;
            $rdv->image = rdvImageGenerator();
            $rdv->title = 'Prochain consultation prévu pour le ' . $request->date_prochain_rdv;;
            $rdv->date = $request->date_prochain_rdv;
            $rdv->save();

            $patientId = OneSignalToken::where('patient_id', $consultation->patient_id)->pluck('token')->toArray();
            if (count($patientId) > 0) {
                $notificationSender = new OneSignalNotificationSender($patientId, "Votre prochain RDV est prevu pour le $request->date_prochain_rdv!");
                $notificationSender->sendNotification();
            }

            $message =  "Consultation le " . dateNumberFr($consultation->date_consultation) . " à l'hôpital Saint Marie. Rendez-vous avec le Dr. " . $consultation->patient->user->name . ' ' . $consultation->patient->user->prenom . " le " . dateNumberFr($request->date_prochain_rdv) . " pour la prochaine consultation.";
            $res = (new SmsRepository($consultation->patient->telephone, $message))->send();
        }


        return ['status' => 'success', 'message' => 'Régistre de consultation post-natale enregistré avec succès.'];
    }

    public function storePreNatales(ConsultationPreNataleRequest $request)
    {
        $data = $request->except(['consultation_id', '_token', 'mode_sortie']);
        $consultation = Consultation::find($request->consultation_id);

        if ($request->motif_consultation)
            $consultation->motif_consultation = $request->motif_consultation;

        if (Registre::where('consultation_id', $request->consultation_id)->exists()) {
            $registre = Registre::where('consultation_id', $request->consultation_id)->first();

            $registre->issue_consultation = $request->mode_sortie ?? $request->resultat_consultation ?? null;
            $registre->save();

            RegistreConsultationPreNatale::where('registre_id', $registre->id)->update($data);

            // return ['status' => 'success', 'message' => 'Régistre de consultation pré-natale modifié avec succès.'];
        } else {
            $registre = new Registre();
            $registre->code = codeRegistre($consultation->patient->code_patient, $consultation->patient->id);
            $registre->type_consultation = 'consultation prenatale';
            $registre->consultation_id = $consultation->id;
            $registre->issue_consultation = $request->mode_sortie ?? $request->resultat_consultation ?? null;
            $registre->save();

            $data['registre_id'] = $registre->id;

            RegistreConsultationPreNatale::create($data);
        }

        $consultation->status = 1;
        $consultation->save();

        if ($request->date_prochain_rdv) {

            if ($rd = RendezVous::where('consultation_id', $consultation->id)->first())
                $rd->delete();

            $rdv = new RendezVous();
            $rdv->consultation_id = $consultation->id;
            $rdv->doctor_id = $consultation->doctor_id;
            $rdv->patient_id = $consultation->patient_id;
            $rdv->image = rdvImageGenerator();
            $rdv->title = 'Prochain consultation prévu pour le ' . $request->date_prochain_rdv;;
            $rdv->date = $request->date_prochain_rdv;
            $rdv->save();

            $patientId = OneSignalToken::where('patient_id', $consultation->patient_id)->pluck('token')->toArray();
            if (count($patientId) > 0) {
                $notificationSender = new OneSignalNotificationSender($patientId, "Votre prochain RDV est prevu pour le $request->date_prochain_rdv!");
                $notificationSender->sendNotification();
            }

            $message =  "Consultation le " . dateNumberFr($consultation->date_consultation) . " à l'hôpital Saint Marie. Rendez-vous avec le Dr. " . $consultation->patient->user->name . $consultation->patient->user->prenom . " le " . $request->date_prochain_rdv . " pour la prochaine consultation.";
            $res = (new SmsRepository($consultation->patient->telephone, $message))->send();
        }

        return ['status' => 'success', 'message' => 'Régistre de consultation pré-natale enregistré avec succès.'];
    }

    public function storeAccouchements(ConsultationAccouchementRequest $request)
    {
        $data = $request->except(['consultation_id', '_token', 'mode_sortie']);
        $consultation = Consultation::findOrFail($request->consultation_id);

        if ($request->motif_consultation)
            $consultation->motif_consultation = $request->motif_consultation;

        if (Registre::where('consultation_id', $request->consultation_id)->exists()) {
            $registre = Registre::where('consultation_id', $request->consultation_id)->first();

            $registre->issue_consultation = $request->mode_sortie;
            $registre->save();

            RegistreAccouchement::where('registre_id', $registre->id)->update($data);

            // return ['status' => 'success', 'message' => 'Régistre accouchement modifié avec succès.'];
        } else {

            $registre = new Registre();
            $registre->code = codeRegistre($consultation->patient->code_patient, $consultation->patient->id);
            $registre->type_consultation = 'accouchement';
            $registre->consultation_id = $consultation->id;
            $registre->issue_consultation = $request->mode_sortie;

            // dd($registre->issue_consultation = $request->mode_sortie);
            $registre->save();

            $data['registre_id'] = $registre->id;

            RegistreAccouchement::create($data);
        }



        $consultation->status = 1;
        $consultation->save();

        if ($request->date_prochain_rdv) {

            if ($rd = RendezVous::where('consultation_id', $consultation->id)->first())
                $rd->delete();

            $rdv = new RendezVous();
            $rdv->consultation_id = $consultation->id;
            $rdv->doctor_id = $consultation->doctor_id;
            $rdv->patient_id = $consultation->patient_id;
            $rdv->image = rdvImageGenerator();
            $rdv->title = 'Prochain consultation prévu pour le ' . $request->date_prochain_rdv;;
            $rdv->date = $request->date_prochain_rdv;
            $rdv->save();


            $patientId = OneSignalToken::where('patient_id', $consultation->patient_id)->pluck('token')->toArray();
            if (count($patientId) > 0) {
                $notificationSender = new OneSignalNotificationSender($patientId, "Votre prochain RDV est prevu pour le $request->date_prochain_rdv!");
                $notificationSender->sendNotification();
            }

            $message =  "Consultation le " . dateNumberFr($consultation->date_consultation) . " à l'hôpital Saint Marie. Rendez-vous avec le Dr. " . $consultation->patient->user->name . $consultation->patient->user->prenom . " le " . $request->date_prochain_rdv . " pour la prochaine consultation.";

            $res = (new SmsRepository($consultation->patient->telephone, $message))->send();
        }

        return ['status' => 'success', 'message' => 'Régistre accouchement enregistré avec succès.'];
    }

    public function detail($id)
    {

        $type = PrestationService::findOrFail($id);

        $title = 'Détail ';
        $section = '';
        switch ($type->libelle) {
            case 'Consultation':
                $title .= 'de consultation';
                $section = 'curative';
                break;
            case 'Consultation prénatale':
                $title .= 'de consultation pre natale';
                $section = 'pre-natale';

                break;
            case 'Consultation postnatale':
                $title .= 'de consultation post natale';
                $section = 'post-natale';

                break;
            case 'Accouchement':
                $title .= 'd\'accouchement';
                $section = 'accouchement';

                break;
            default:
                $title .= 'de consultation';
                $section = 'curative';
                return [$section, $title];
        }

        return [$section, $title];
    }
}
