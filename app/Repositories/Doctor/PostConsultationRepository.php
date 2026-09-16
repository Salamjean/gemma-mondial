<?php

namespace App\Repositories\Doctor;

use App\Models\DrugSale;
use App\Models\Ordonnance;
use App\Models\Prescription;
use App\Models\Registre;
use App\Models\ArretTravail;
use App\Models\Consultation;
use App\Models\Drug;
use App\Models\DrugHospital;
use Illuminate\Support\Facades\Auth;

class PostConsultationRepository
{
    public function __construct()
    {
        //
    }

    //ORDONNANCE externe
    public function storeOrdonnance($request)
    {
        $request->validate([
            'consultation_id' => 'required',
        ]);

        try {

            if (Ordonnance::where('type', 'externe')->where('consultation_id', $request->consultation_id)->exists()) {
                $exist = Ordonnance::where('consultation_id', $request->consultation_id)->first();
                $exist->prescriptions()->delete();
                $exist->delete();
            }

            //consultation
            $consultation = Consultation::findOrFail($request->consultation_id);

            //ordonnance
            $ordonnance = Ordonnance::create([
                "reference" => codeOrdonnance($consultation->patient->code_patient, $consultation->patient->id),
                "type" => 'externe',
                "consultation_id" => $request->consultation_id,
                "status" => 1,
                "date" => date('Y-m-d')
            ]);

            //save prescriptions
            $medicamentCodes = is_array($request->medicamentCode) ? $request->medicamentCode : ($request->filled('medicamentCode') ? [$request->medicamentCode] : []);
            $quantities = is_array($request->medicamentQte) ? $request->medicamentQte : ($request->filled('medicamentQte') ? [$request->medicamentQte] : []);
            $posologies = is_array($request->medicamentPosologie) ? $request->medicamentPosologie : ($request->filled('medicamentPosologie') ? [$request->medicamentPosologie] : []);
            $routes = is_array($request->routeAdministration) ? $request->routeAdministration : ($request->filled('routeAdministration') ? [$request->routeAdministration] : []);
            $durations = is_array($request->duration) ? $request->duration : ($request->filled('duration') ? [$request->duration] : []);
            $advices = is_array($request->healthDieteticAdvice) ? $request->healthDieteticAdvice : ($request->filled('healthDieteticAdvice') ? [$request->healthDieteticAdvice] : []);

            if (!empty($medicamentCodes)) {
                foreach ($medicamentCodes as $index => $item) {
                    if (empty($item)) continue;
                    $drug = Drug::find($item);

                    $prescription = new Prescription();
                    $prescription->ordonnance_id = $ordonnance->id;
                    $prescription->drug_id = $item;
                    $prescription->quantity = $quantities[$index] ?? 1;
                    $prescription->dosage = $posologies[$index] ?? ($drug ? $drug->posology : null);
                    $prescription->route_administration = $routes[$index] ?? null;
                    $prescription->duration = $durations[$index] ?? null;
                    $prescription->health_dietetic_advice = $advices[$index] ?? null;
                    $prescription->save();
                }
            }

            return ['status' => 'success', 'message' => 'Ordonnance enregistrée avec succès.', 'id' => $ordonnance->id];
        } catch (\Throwable $err) {
            return ['status' => 'error', 'message' => $err->getMessage()];
        }
    }

    //ORDONANCE Interne
    public function storeOrdonnanceI($request)
    {
        $request->validate([
            'consultation_id' => 'required',
        ]);


        try {

            if (Ordonnance::where('type', 'interne')->where('consultation_id', $request->consultation_id)->exists()) {
                $exist = Ordonnance::where('consultation_id', $request->consultation_id)->first();
                $exist->prescriptions()->delete();
                $exist->delete();
            }

            //consultation
            $consultation = Consultation::findOrFail($request->consultation_id);

            //ordonnance
            $ordonnance = Ordonnance::create([
                "reference" => codeOrdonnance($consultation->patient->code_patient, $consultation->patient->id),
                "type" => 'interne',
                "consultation_id" => $request->consultation_id,
                "date" => date('Y-m-d')
            ]);

            $price = 0;
            //save prescriptions
            $medicamentCodesI = is_array($request->medicamentCodeI) ? $request->medicamentCodeI : ($request->filled('medicamentCodeI') ? [$request->medicamentCodeI] : []);
            $quantitiesI = is_array($request->medicamentQteI) ? $request->medicamentQteI : ($request->filled('medicamentQteI') ? [$request->medicamentQteI] : []);
            $posologiesI = is_array($request->medicamentPosologieI) ? $request->medicamentPosologieI : ($request->filled('medicamentPosologieI') ? [$request->medicamentPosologieI] : []);
            $routesI = is_array($request->routeAdministrationI) ? $request->routeAdministrationI : ($request->filled('routeAdministrationI') ? [$request->routeAdministrationI] : []);
            $durationsI = is_array($request->durationI) ? $request->durationI : ($request->filled('durationI') ? [$request->durationI] : []);
            $advicesI = is_array($request->healthDieteticAdviceI) ? $request->healthDieteticAdviceI : ($request->filled('healthDieteticAdviceI') ? [$request->healthDieteticAdviceI] : []);

            if (!empty($medicamentCodesI)) {
                foreach ($medicamentCodesI as $index => $item) {
                    if (empty($item)) continue;
                    $drug = DrugHospital::find($item);

                    $prescription = new Prescription();
                    $prescription->ordonnance_id = $ordonnance->id;
                    $prescription->drug_id = $item;
                    $prescription->quantity = $quantitiesI[$index] ?? 1;
                    $prescription->dosage = $posologiesI[$index] ?? ($drug ? $drug->posology : null);
                    $prescription->route_administration = $routesI[$index] ?? null;
                    $prescription->duration = $durationsI[$index] ?? null;
                    $prescription->health_dietetic_advice = $advicesI[$index] ?? null;
                    $prescription->save();

                    $price += ($quantitiesI[$index] ?? 1) * ($drug ? $drug->price : 0);
                }
            }

            $hospitalId = (Auth::check() && Auth::user()->doctor) ? Auth::user()->doctor->hospital_id : (Auth::user()->hospital_id ?? 1);
            $drugSale = new DrugSale();
            $drugSale->type = 'ordonnance';
            $drugSale->hospital_id = $hospitalId;
            $drugSale->ordonnance_id = $ordonnance->id;
            $drugSale->price = $price;
            $drugSale->save();  

            return ['status' => 'success', 'message' => 'Ordonnance enregistrée avec succès.', 'id' => $ordonnance->id];
        } catch (\Throwable $err) {
            return ['status' => 'error', 'message' => $err->getMessage()];
        }
    }

    public function storeArretTravail($request)
    {
        if (!$request->filled('date_debut')) {
            $request->merge(['date_debut' => date('Y-m-d')]);
        }
        if (!$request->filled('date_fin')) {
            $request->merge(['date_fin' => date('Y-m-d', strtotime('+3 days'))]);
        }
        if (!$request->filled('nb_jour')) {
            $request->merge(['nb_jour' => 3]);
        }

        $request->validate([
            'consultation_id' => 'required',
            'date_debut' => 'required',
            'date_fin' => 'required',
            'nb_jour' => 'required',
        ]);

        //consultation
        $consultation = Consultation::findOrFail($request->consultation_id);

        if (ArretTravail::where('consultation_id', $request->consultation_id)->exists()) {
            $exist = ArretTravail::where('consultation_id', $request->consultation_id)->first();
            $exist->delete();
        }

        $arretTravail = new ArretTravail();
        $arretTravail->code = codeArret($consultation->patient->code_patient, $consultation->patient->id);
        $arretTravail->consultation_id = $request->consultation_id;
        $arretTravail->date_debut = $request->date_debut;
        $arretTravail->date_fin = $request->date_fin;
        $arretTravail->nb_jour = $request->nb_jour;
        $arretTravail->save();

        return ['status' => 'success', 'message' => 'Arrêt de travail a été enregistré avec succès.', 'id' => $arretTravail->id];
    }
}
