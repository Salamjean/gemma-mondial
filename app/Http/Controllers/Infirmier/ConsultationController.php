<?php

namespace App\Http\Controllers\Infirmier;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\Drug;
use App\Models\Infirmier;
use App\Models\User;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{

    public function today()
    {
        return view('users.infirmier.consultation.today', ['consultations' => Consultation::where('infirmier_id', auth()->user()->infirmier->id)->withCount('ordonnances', 'arret', 'examen')->where('date_consultation', date('Y-m-d'))->get()]);
    }

    public function allPatients()
    {
        $infirmierId = auth()->user()->infirmier->id;
        $consultations = Consultation::orderByDESC('created_at')
            ->where(function ($q) use ($infirmierId) {
                $q->where('infirmier_id', $infirmierId)
                    ->orWhereNull('infirmier_id');
            })
            ->where('status_inf', 0)
            ->get();

        return view('users.infirmier.consultation.all', compact('consultations'));
    }

    public function history()
    {
        return view('users.infirmier.consultation.history', ['consultations' => Consultation::orderByDESC("date_consultation")->withCount(['ordonnances', 'arret', "examen", "declaration", "registre"])->with("ordonnances", "arret", "examen", "declaration", "registre")->where('infirmier_id', auth()->user()->infirmier->id)->where('date_consultation', '<', date('Y-m-d'))->orWhere('status_inf', 1)->get()]);
    }

    public function store(Request $request)
    {

        $data = $request->validate([
            'consultation_id' => "required",
            'tension_arterielle' => 'nullable',
            'temperature' => 'nullable',
            //'saturation_oxygene' => 'nullable',
            'taille' => 'nullable',
            'poids' => 'nullable',
            'imc' => 'nullable',
            'pouls' => 'nullable',
            //'gly_a_jeun' => 'nullable',
            //'gly_nn_jeun' => 'nullable',
            // 'perimetre_brach' => 'nullable',
            'issue_consultation' => 'nullable',
            'doctor_id' => 'nullable|integer',
            'observation_infirmiere' => 'nullable'
        ]);

        $consultation = Consultation::findOrFail($data["consultation_id"]);
        $consultation->infirmier_id = auth()->user()->infirmier->id;
        $consultation->tension_arterielle = $data["tension_arterielle"] ?? '';
        $consultation->temperature = $request->temperature ?? '';
        //$consultation->saturation_oxygene = $data["saturation_oxygene"] ?? '';
        $consultation->taille = $data["taille"] ?? '';
        $consultation->poids = $data["poids"] ?? '';
        $consultation->imc = $data["imc"] ?? '';
        $consultation->pouls = $data["pouls"] ?? '';
        //$consultation->gly_a_jeun = $data["gly_a_jeun"] ?? '';
        //$consultation->gly_nn_jeun = $data["gly_nn_jeun"] ?? '';
        //$consultation->perimetre_brach = $data["perimetre_brach"] ?? '';

        // Assigner issue_consultation_id si fourni, sinon mettre une valeur par défaut
        $consultation->issue_consultation_id = $data["issue_consultation"] ?? null;

        // Si observation_infirmiere est fournie, l'assigner
        if (isset($data["observation_infirmiere"])) {
            $consultation->observation_infirmiere = $data["observation_infirmiere"];
        }

        $consultation->status_inf = 1;
        $consultation->save();


        return redirect()->route('dashboard')->with('success', 'Votre patient est en attente chez le medecin.');

    }

    public function formulaire($id)
    {
        $consultation = Consultation::findOrFail($id);
        $doctors = Doctor::with('user')->where('service_hospital_id', auth()->user()->infirmier->service_hospital_id)->get();
        return view('users.infirmier.consultation.formulaire', ['consultation' => $consultation, 'doctors' => $doctors]);
    }

    public function formulaireIssue($consultation, $title = 'Issue de la consultation')
    {
        $consultation = Consultation::find($consultation);

        $drugs = Drug::with('drugSale')->where('hospital_id', auth()->user()->infirmier->hospital_id)->get();

        return view('users.infirmier.consultation.issue', ['title' => $title, 'consultation' => $consultation, 'drugs' => $drugs]);
    }

    public function detail($id)
    {
        $consultation = Consultation::findOrFail($id);
        return view('users.infirmier.consultation.detail', compact('consultation'));
    }

    public function patientCard(Request $request, $id)
    {
        $patient = \App\Models\Patient::with(['user', 'hospital'])->findOrFail($id);
        if ($request->ajax()) {
            return view('users.secretariat.patient.card_inner', compact('patient'));
        }
        return view('users.secretariat.patient.card', compact('patient'));
    }
}