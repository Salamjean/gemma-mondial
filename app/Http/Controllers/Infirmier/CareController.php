<?php

namespace App\Http\Controllers\Infirmier;

use App\Models\Care;
use App\Models\Bandage;
use App\Models\DrugSale;
use App\Models\Injection;
use App\Models\DrugHospital;
use Illuminate\Http\Request;
use App\Models\CareRequested;
use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Drug;
use App\Models\Ordonnance;
use App\Models\Prescription;
use App\Repositories\Infirmier\CareRepository;
use Illuminate\Support\Facades\Auth;

class CareController extends Controller
{
    public function new()
    {
        $infirmierId = Auth::user()->infirmier->id;
        $cares = CareRequested::with('admission')->whereHas('admission', function ($query) use ($infirmierId) {
            $query->where('infirmier_id', $infirmierId);
        })->where('status', '!=', 'success')->whereDate('created_at', date('Y-m-d'))->get();

        return view('users.infirmier.care.new', compact('cares'));
    }

    public function allCares()
    {
        $infirmierId = Auth::user()->infirmier->id;
        $cares = CareRequested::with('admission')->whereHas('admission', function ($query) use ($infirmierId) {
            $query->where('infirmier_id', $infirmierId)->orWhereNull('infirmier_id');
        })->where('status', '!=', 'success')->orderByDESC('created_at')->get();

        return view('users.infirmier.care.all', compact('cares'));
    }

    public function history()
    {
        $infirmierId = Auth::user()->infirmier->id;
        $cares = CareRequested::with('admission')->whereHas('admission', function ($query) use ($infirmierId) {
            $query->where('infirmier_id', $infirmierId);
        })->get();

        return view('users.infirmier.care.history', compact('cares'));
    }

    public function formulaire($id)
    {

        $care = CareRequested::findOrFail($id);
        $bandages = Bandage::all();
        $injections = Injection::all();
        $soins = Care::all();
        $drugs = DrugHospital::where('hospital_id', Auth::user()->infirmier->hospital_id)->where('status', 0)->get();

        return view('users.infirmier.care.formulaire', compact('care', 'soins', 'injections', 'bandages', "drugs"));
    }

    public function getHopitalPansementsCourant(){
        $hopital_pansements = Bandage::where('type', 'pansement courants')->get();
        return response()->json($hopital_pansements);
    }
    public function getHopitalPansementsComplexe(){
        $hopital_pansements = Bandage::where('type', 'pansement lourd et complexe')->get();
        return response()->json($hopital_pansements);
    }

    public function getHopitalSoinsRespiratoire(){
        $hopital_soins = Care::where('type', 'soins portant sur l\'appareil respiratoire')->get();
        return response()->json($hopital_soins);
    }
    public function getHopitalSoinsDigestif(){
        $hopital_soins = Care::where('type', 'soins portant sur l\'appareil digestif')->get();
        return response()->json($hopital_soins);
    }
    public function getHopitalSoinsGenitoUrinaire(){
        $hopital_soins = Care::where('type', 'soins portant sur l\'appareil genito-urinaire')->get();
        return response()->json($hopital_soins);
    }

    public function store(Request $request, $id)
    {

        $care = CareRequested::find($id);

        $repository = new CareRepository($care);

        if ($care->type == 'Pansement')
        $res = $repository->storeBandage($request);

        if ($care->type == 'Injection')
        $res = $repository->storeInjection($request);

        if ($care->type == 'Soins')
        $res = $repository->storeCare($request);

        if ($res['status'] != 'success')
            return response(['message' => 'Erreur'], 403);

        //payment pending
        $care -> status = 'payment_pending';
        $care -> save();

        //pharmacy notifications
        $drugSale = new DrugSale();
        $drugSale -> type = 'care_requested';
        $drugSale -> care_requested_id = $care -> id;
        $drugSale -> hospital_id = Auth::user()->infirmier->hospital->id;
        $drugSale -> price = $care -> price;
        $drugSale -> save();

        return redirect()->route('dashboard')->with('success', 'Votre patient est en attente à la pharmacie.');

    }

    public function payment_success ($id)
    {

        $care = CareRequested::find($id);

        $drugs = DrugHospital::with('drug')->where('hospital_id', Auth::user()->infirmier->hospital_id)->get();

        return view('users.infirmier.care.payment_success', compact('care', 'drugs'));
    }

    public function impression($id)
    {
        dd($id);
    }

    public function issue(Request $request)
    {
        $request->validate([
            'justification' => 'required|string',
            'care_id' => 'required|integer',
        ]);

        $care = CareRequested::find($request->care_id);

        //consultation
        $consultation = new Consultation();
        $consultation -> code_consultation = 'CONSULT' . rand(000000000000,11111111111);
        $consultation -> admission_id = $care->admission_id;
        $consultation -> date_consultation = date('Y-m-d');
        $consultation -> prestation_hospital_id = $care->admission->prestation_hopital_id;
        $consultation -> hospital_id = Auth::user()->infirmier->hospital_id;
        $consultation -> montant = $care->admission->montant + $care->price;
        $consultation -> patient_id = $care->admission->patient_id;
        $consultation -> status = 1;
        $consultation -> status_inf = 1;
        $consultation -> observation_infirmiere = $request->justification;
        $consultation -> infirmier_id = Auth::user()->infirmier->id;
        $consultation -> save();

        // update careRequested
        $care -> status = 'success';
        $care -> consultation_id = $consultation->id;
        $care -> save();

        return response()->json(['status' => 'success'], 200);

    }

    public function ordonnance(Request $request, $_id)
    {

        try {

            //care
            $care = CareRequested::find($_id);

            //consultation
            $consultation = Consultation::findOrFail($care->consultation_id);

            if (Ordonnance::where('type', 'externe')->where('consultation_id', $care->consultation_id)->exists()) {
                $exist = Ordonnance::where('consultation_id', $care->consultation_id)->first();
                $exist->prescriptions()->delete();
                $exist->delete();
            }

            //ordonnance
            $ordonnance = Ordonnance::create([
                "reference" => codeOrdonnance($consultation->patient->code_patient, $consultation->patient->id),
                "type" => 'externe',
                "consultation_id" => $consultation->id,
                "status" => 1,
                "date" => date('Y-m-d')
            ]);

            //save prescriptions
            foreach ($request->medicamentCode as $index => $item) {

                $drug = Drug::find($item);

                $prescription = new Prescription();
                $prescription->ordonnance_id = $ordonnance->id;
                $prescription->drug_id = $item;
                $prescription->quantity = $request->medicamentQte[$index] ?? 1;
                $prescription->dosage = $request->medicamentPosologie[$index] ?? $drug->posology;
                $prescription->save();
            }

            return ['status' => 'success', 'message' => 'Ordonnance enregistrée avec succès.', 'id' => $ordonnance->id];
        } catch (\Throwable $err) {
            return ['status' => 'error', 'message' => $err->getMessage()];
        }
    }

    public function detail($id)
    {
        $care = CareRequested::find($id);

        return view('users.infirmier.care.detail', compact('care'));
    }
}
