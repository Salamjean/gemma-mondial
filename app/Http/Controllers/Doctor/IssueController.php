<?php

namespace App\Http\Controllers\Doctor;

use App\Models\Registre;
use App\Models\Pathologie;
use App\Models\TherapeutiqueProtocol;
use App\Models\Traitement;
use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\Declaration;
use App\Models\Hospitalisation;
use App\Models\SoinsAdministre;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Doctor\IssueRepository;
use App\Repositories\Doctor\DeclarationRepository;
use App\Http\Requests\Doctor\DeclarationDecesRequest;
use App\Http\Requests\Doctor\DeclarationNaissanceRequest;
use App\Models\Bed;
use App\Models\DayHospitalisation;
use App\Models\DrugHospital;
use App\Models\DrugSale;
use App\Models\HospitalisationDrugRequested;
use App\Models\Operation;
use App\Models\ProtocolHourApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\TryCatch;

class IssueController extends Controller
{
    public function instance()
    {
        return new IssueRepository();
    }

    public function storeIssueDeces(DeclarationDecesRequest $request)
    {
        $instance = new DeclarationRepository();
        $res = $instance->storeDeces($request);

        if ($res['status'] == 'error')
            return response()->json(['status' => $res['status']], 200);

        return response()->json(['status' => $res['status'], 'id' => $res['id']], 200);
    }

    public function storeIssueNaissance(DeclarationNaissanceRequest $request)
    {

        $instance = new DeclarationRepository();
        $res = $instance->storeNaissance($request);

        if ($res['status'] == 'error')
            return response()->json(['status' => $res['status']], 200);

        return response()->json(['status' => $res['status']], 200);
    }

    public function storeIssueJustification(Request $request)
    {
        $res = $this->instance()->storeJustification($request);

        if ($res['status'] == 'error')
            return response()->json(['status' => $res['status']], 200);

        return response()->json(['status' => $res['status']], 200);
    }

    public function storeIssueHospitalisation(Request $request, $consultation)
    {

        $request->validate([
            'infirmier_id' => 'nullable',
            'bed' => 'nullable|integer',
            'quantity' => 'nullable|array',
            'quantity.*' => 'nullable|integer',
            'drug' => 'required|array|min:1',
            'drug.*' => 'required',
        ], [
            'drug.required' => 'Veuillez ajouter au moins un protocole thérapeutique avant d\'enregistrer.',
            'drug.min' => 'Veuillez ajouter au moins un protocole thérapeutique avant d\'enregistrer.',
            'drug.*.required' => 'Veuillez sélectionner le produit pour chaque protocole.',
        ]);

        try {

            $consultation = Consultation::find($consultation);

            $bed = null;
            if ($request->bed) {
                $bed = Bed::find($request->bed);
                if ($bed && $bed->status_occupied == 'occupied')
                    return redirect()->back()->with('warning', 'Bed of the bedroom is occupied.');

                if ($bed) {
                    $bed->status_occupied = 'occupied';
                    $bed->save();
                }
            }

            if ($registre = Registre::where('consultation_id', $consultation->id)->first()) {
                $registre->issue_consultation = "Hospitalisation";
                $registre->save();
            }

            $consultation->status = 1;
            $consultation->save();

            if ($hospitalisation = Hospitalisation::where('consultation_id', $consultation->id)->first()) {

                $hospitalisation->diagnostic = $request->diagnostic ?? $hospitalisation->diagnostic;
                $hospitalisation->sonde = $request->sonde ?? $hospitalisation->sonde;
                $hospitalisation->analyse_medical = $request->analyse_medical ?? $hospitalisation->analyse_medical;
                $hospitalisation->oxygenotherapie = $request->oxygenotherapie ?? $hospitalisation->oxygenotherapie;
                $hospitalisation->remark = $request->remark ?? $hospitalisation->remark;
                $hospitalisation->volume = $request->volume ?? $hospitalisation->volume;
                $hospitalisation->save();
            } else {

                $hospitalisation = new Hospitalisation();
                $hospitalisation->code = noDossierHospitalisation();
                $hospitalisation->consultation_id = $consultation->id;
                $hospitalisation->doctor_id = $consultation->doctor_id;
                $hospitalisation->type = "hospitalisation";
                $hospitalisation->date = date('Y-m-d');
                $hospitalisation->diagnostic = $request->diagnostic ?? null;
                $hospitalisation->sonde = $request->sonde ?? null;
                $hospitalisation->analyse_medical = $request->analyse_medical ?? null;
                $hospitalisation->oxygenotherapie = $request->oxygenotherapie ?? null;
                $hospitalisation->remark = $request->remark ?? null;
                $hospitalisation->volume = $request->volume ?? null;
                $hospitalisation->save();
            }

            if ($request->operation) {

                $request->validate(['date_operation' => 'required|date']);

                if ($operation = Operation::where('hospitalisation_id', $hospitalisation->id)->first()) {

                    $operation->date = $request->date_operation;
                    $operation->save();
                } else {

                    $operation = new Operation();
                    $operation->hospitalisation_id = $hospitalisation->id;
                    $operation->date = $request->date_operation;
                    $operation->save();
                }
            }

            if ($request->regime) {

                $request->validate(['description_regime' => 'required|string']);

                $hospitalisation->regime = $request->description_regime;
                $hospitalisation->save();
            }

            if ($hospitalisationDay = DayHospitalisation::where('hospitalisation_id', $hospitalisation->id)->whereDate('day', date('Y-m-d'))->first()) {
                if ($request->infirmier_id) {
                    $hospitalisationDay->infirmier_id = $request->infirmier_id;
                }
                if ($bed) {
                    $hospitalisationDay->bed_id = $bed->id;
                    $hospitalisationDay->price = $bed->price;
                }
                $hospitalisationDay->save();
            } else {
                $hospitalisationDay = new DayHospitalisation();
                $hospitalisationDay->hospitalisation_id = $hospitalisation->id;
                $hospitalisationDay->doctor_id = $hospitalisation->doctor_id;
                $hospitalisationDay->infirmier_id = $request->infirmier_id ?? null;
                $hospitalisationDay->day = date('Y-m-d');
                $hospitalisationDay->bed_id = $bed ? $bed->id : null;
                $hospitalisationDay->price = $bed ? $bed->price : 0;
                $hospitalisationDay->save();
            }

            if (isset($request->drug)) {
                if (count($request->drug) > 0) {

                    $hospitalisationDrugs = new HospitalisationDrugRequested();
                    $hospitalisationDrugs->hospitalisation_id = $hospitalisation->id;
                    $hospitalisationDrugs->save();

                    $price = 0;
                    foreach ($request->drug as $key => $drug) {

                        $drug = DrugHospital::find($request->drug[$key]);

                        $protocol = new TherapeutiqueProtocol();
                        $protocol->protocol_type = 'internal';
                        $protocol->day_hospitalisation_id = $hospitalisationDay->id;
                        $protocol->hospitalisation_drug_requested_id = $hospitalisationDrugs->id;
                        $protocol->drug_hospital_id = $drug->id;
                        $protocol->quantity = $request->quantity[$key] ?? 1;
                        $protocol->dosage = $request->dosage[$key] ?? null;
                        $protocol->voie_admission = $request->voie_admission[$key] ?? null;
                        $protocol->price = $drug->price;
                        $protocol->total = $drug->price * ($request->quantity[$key] ?? 1);
                        $protocol->save();

                        $hours = $request->validate(['hour' . $key + 1 => 'nullable|array']);
                        if (isset($hours['hour' . $key + 1])) {
                            if (count($hours['hour' . $key + 1])) {
                                foreach ($hours['hour' . $key + 1] as $keyy => $hour) {
                                    $protocolApplication = new ProtocolHourApplication();
                                    $protocolApplication->therapeutique_protocol_id = $protocol->id;
                                    $protocolApplication->hour = $hour;
                                    $protocolApplication->save();
                                }
                            }
                        } 

                        $price += $protocol->total;
                    }

                    $drugSale = new DrugSale();
                    $drugSale->type = 'hospitalisation';
                    $drugSale->hospitalisation_drug_requested_id = $hospitalisationDrugs->id;
                    $drugSale->hospital_id = Auth::user()->doctor->hospital->id;
                    $drugSale->price = $price;
                    $drugSale->save();
                }
            }

            if ($bed) {
                return redirect()->route('doctor.hospitalisation.in_progress')->with('success', 'Patient mis sous observations dans votre service.');
            } else {
                return redirect()->route('doctor.hospitalisation.pending_room')->with('success', 'Patient mis en hospitalisation (En attente d\'attribution de chambre).');
            }
        } catch (\Throwable $err) {
            dd($err);
        }
    }

    public function storeIssueObservation(Request $request)
    {
        $res = $this->instance()->storeObservation($request);

        if ($res['status'] == 'error')
            return response()->json(['status' => $res['status']], 200);

        return response()->json(['status' => $res['status']], 200);
    }
}
