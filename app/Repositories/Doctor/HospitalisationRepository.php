<?php

namespace App\Repositories\Doctor;

use App\Models\AccessDoctorHospitalisation;
use App\Models\Bed;
use App\Models\Consultation;
use App\Models\DayHospitalisation;
use App\Models\Drug;
use App\Models\DrugHospital;
use App\Models\DrugSale;
use App\Models\Hospitalisation;
use App\Models\HospitalisationDrugRequested;
use App\Models\Operation;
use App\Models\Ordonnance;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\ProtocolHourApplication;
use App\Models\TherapeutiqueProtocol;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class HospitalisationRepository
{
    public function __construct()
    {
        //
    }

    public function model()
    {
        return Hospitalisation::class;
    }

    public function show($id)
    {

        $hospitalisation = Hospitalisation::where('id', $id)
            ->with(['daysHospitalisation' => function ($query) {
                $query->orderByDESC('created_at')->with(['therapeutiqueProtocols' => function ($query) {
                    $query->with(['protocolHourApplications' => function ($query) {
                        $query->orderByDESC('id');
                    }]);
                }]);
            }])
            ->first();

        return $hospitalisation;
    }

    public function syncPendingHospitalisationsFromConsultations()
    {
        try {
            $consultationsWithHosp = Consultation::whereHas('registre', function ($q) {
                $q->where('issue_consultation', 'LIKE', '%hospit%')
                    ->orWhere('issue_consultation_justification', 'LIKE', '%hospit%');
            })->get();

            foreach ($consultationsWithHosp as $consultation) {
                if (!Hospitalisation::where('consultation_id', $consultation->id)->exists()) {
                    $hosp = new Hospitalisation();
                    $hosp->code = noDossierHospitalisation();
                    $hosp->consultation_id = $consultation->id;
                    $hosp->doctor_id = $consultation->doctor_id ?? (auth()->check() && auth()->user()->doctor ? auth()->user()->doctor->id : 1);
                    $hosp->type = "hospitalisation";
                    $hosp->date = date('Y-m-d');
                    $hosp->status = "in_progress";
                    $hosp->save();
                }
            }
        } catch (\Throwable $e) {
            // Ignorer silencieusement si la table n'existe pas encore ou en cas d'erreur mineure
        }
    }

    public function all()
    {
        $this->syncPendingHospitalisationsFromConsultations();
        return $this->model()::orderByDESC('id')->get();
    }

    public function in_progress()
    {
        $this->syncPendingHospitalisationsFromConsultations();
        $doctor = auth()->check() ? auth()->user()->doctor : null;

        $hospitalisations = $this->model()::orderByDESC('id')
            ->where('status', 'in_progress')
            ->whereHas('daysHospitalisation', function ($q) {
                $q->whereNotNull('bed_id');
            })
            ->get();

        return $hospitalisations;
    }

    public function pending_room()
    {
        $this->syncPendingHospitalisationsFromConsultations();
        $doctor = auth()->check() ? auth()->user()->doctor : null;

        $hospitalisations = $this->model()::orderByDESC('id')
            ->where('status', 'in_progress')
            ->where(function ($query) {
                $query->whereDoesntHave('daysHospitalisation')
                    ->orWhereHas('daysHospitalisation', function ($q) {
                        $q->whereNull('bed_id');
                    });
            })
            ->get();

        return $hospitalisations;
    }

    public function history()
    {
        $this->syncPendingHospitalisationsFromConsultations();
        $doctor = auth()->check() ? auth()->user()->doctor : null;
        $hospitalisations = $this->model()::orderByDESC('id')->where('status', '!=', 'in_progress')->get();

        return $hospitalisations;
    }

    public function store(Request $request, $hospitalisation)
    {

        $request->validate([
            'infirmier_id' => 'required',
            'consultation_id' => 'required',
            'bed' => 'nullable|integer',
            'quantity' => 'required|array',
            'quantity.*' => 'integer',
            'drug' => 'required|array',
            'drug.*' => 'integer',
            'bedcheck' => 'nullable'
        ]);

        try {

            $hospitalisation = Hospitalisation::find($hospitalisation);

            $hospitalisationDay = DayHospitalisation::where('hospitalisation_id', $hospitalisation->id)->whereDate('day', date('Y-m-d'))->first();

            if (!$hospitalisation)
                return ['status' => 'error', 'message' => 'Hospitalisation not found.'];

            if ($hospitalisationDay)
                return ['status' => 'error', 'message' => 'Day Hospitalisation is already.'];

            $lastHospitalisationDay = DayHospitalisation::orderByDESC('day')->where('hospitalisation_id', $hospitalisation->id)->first();

            if ($request->bedcheck || !$lastHospitalisationDay || !$lastHospitalisationDay->bed_id) {

                $bed = Bed::find($request->bed);
                if ($bed && $bed->status_occupied != 'no_occupied')
                    return ['status' => 'warning', 'message' => 'Bed of the bedroom occupied.'];
                if ($bed) {
                    $bed->status_occupied = 'occupied';
                    $bed->save();
                }
            } else
                $bed = Bed::find($lastHospitalisationDay->bed_id);

            if ($lastHospitalisationDay) {
                $diffInDays = Carbon::parse($lastHospitalisationDay->day)->diffInDays(Carbon::now());
                $lastHospitalisationDay->number_days = $diffInDays;
                $lastHospitalisationDay->end_date = date('Y-m-d');
                $lastHospitalisationDay->save();
            }

            if ($request->operation) {

                $request->validate(['date_operation' => 'required|date']);

                $operation = new Operation();
                $operation->hospitalisation_id = $hospitalisation->id;
                $operation->date = $request->date_operation;
                $operation->save();
            }

            if ($request->regime) {

                $request->validate(['description_regime' => 'required|string']);

                $hospitalisation->regime = $request->description_regime;
                $hospitalisation->save();
            }


            $hospitalisationDay = new DayHospitalisation();
            $hospitalisationDay->hospitalisation_id = $hospitalisation->id;
            $hospitalisationDay->doctor_id = Auth::user()->doctor->id;
            $hospitalisationDay->infirmier_id = $request->infirmier_id;
            $hospitalisationDay->day = date('Y-m-d');
            $hospitalisationDay->bed_id = $bed->id;
            $hospitalisationDay->save();

            if (isset($request->drug)) {
                if (count($request->drug) > 0) {

                    $hospitalisationDrugs = new HospitalisationDrugRequested();
                    $hospitalisationDrugs->hospitalisation_id = $hospitalisation->id;
                    $hospitalisationDrugs->save();

                    if ($request->protocol_type == 'interne') {
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


                            //consultation
                            $consultation = Consultation::findOrFail($request->consultation_id);

                            //ordonnance
                            $ordonnance = Ordonnance::create([
                                "reference" => codeOrdonnance($consultation->patient->code_patient, $consultation->patient->id),
                                "type" => 'interne',
                                "consultation_id" => $request->consultation_id,
                                "date" => date('Y-m-d')
                            ]);

                            //save prescriptions
                            foreach ($request->drug as $key => $drug) {

                                $drug = DrugHospital::find($request->drug[$key]);

                                $prescription = new Prescription();
                                $prescription->ordonnance_id = $ordonnance->id;
                                $prescription->drug_id = $drug->id;
                                $prescription->quantity = $request->quantity[$key] ?? 1;
                                $prescription->dosage = $request->dosage[$key] ?? $drug->posology;
                                $prescription->route_administration = $request->voie_admission[$key] ?? null;
                                $prescription->save();

                            }

                        }

                        $drugSale = new DrugSale();
                        $drugSale->type = 'hospitalisation';
                        $drugSale->hospitalisation_drug_requested_id = $hospitalisationDrugs->id;
                        $drugSale->hospital_id = Auth::user()->doctor->hospital->id;
                        $drugSale->price = $price;
                        $drugSale->save();
                        
                    } elseif ($request->protocol_type == 'externe') {

                        foreach ($request->drug as $key => $drug) {

                            $drug = Drug::find($request->drug[$key]);

                            $protocol = new TherapeutiqueProtocol();
                            $protocol->protocol_type = 'external';
                            $protocol->day_hospitalisation_id = $hospitalisationDay->id;
                            $protocol->hospitalisation_drug_requested_id = $hospitalisationDrugs->id;
                            $protocol->drug_hospital_id = $drug->id;
                            $protocol->quantity = $request->quantity[$key] ?? 1;
                            $protocol->dosage = $request->dosage[$key] ?? null;
                            $protocol->voie_admission = $request->voie_admission[$key] ?? null;

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

                        }

                        //consultation
                        $consultation = Consultation::findOrFail($hospitalisation->consultation_id);

                        //ordonnance
                        $ordonnance = Ordonnance::create([
                                "reference" => codeOrdonnance($consultation->patient->code_patient, $consultation->patient->id),
                                "type" => 'externe',
                                "consultation_id" => $consultation->id,
                                "status" => 1,
                                "date" => date('Y-m-d')
                            ]);

                        //save prescriptions
                        foreach ($request->drug as $key => $drug) {

                            $drug = Drug::find($request->drug[$key]);

                            $prescription = new Prescription();
                            $prescription->ordonnance_id = $ordonnance->id;
                            $prescription->drug_id = $request->drug[$key];
                            $prescription->quantity = $request->quantity[$key] ?? 1 ?? 1;
                            $prescription->dosage = $request->dosage[$key] ?? $drug->posology;
                            $prescription->route_administration = $request->voie_admission[$key] ?? null;
                            $prescription->save();
                        }


                    } else {
                        return ['status' => 'error', 'message' => 'No type protocole selected.'];
                    }
                } else
                    return ['status' => 'error', 'message' => 'No drug selected.'];
            }

            return ['status' => 'success', 'message' => 'Protocols add successfully.'];
        } catch (Throwable $err) {
            dd($err);
        }
    }

    public function update(Request $request, $hospitalisation)
    {

        $request->validate([
            'quantity' => 'required|array',
            'bed' => 'nullable|integer',
            'bedcheck' => 'nullable',

            'quantity.*' => 'integer',
            'drug' => 'required|array',
            'drug.*' => 'integer',
        ]);

        try {

            $hospitalisation = Hospitalisation::find($hospitalisation);

            $hospitalisationDay = DayHospitalisation::where('hospitalisation_id', $hospitalisation->id)->whereDate('day', date('Y-m-d'))->first();

            if (!$hospitalisation)
                return ['status' => 'error', 'message' => 'Hospitalisation not found.'];

            if (!$hospitalisationDay)
                return ['status' => 'error', 'message' => 'Day Hospitalisation not found.'];

            if ($hospitalisationDay->doctor_id != Auth::user()->doctor->id)
                return ['status' => 'warning', 'message' => "Doctor of the day in charge of the patient: " . $hospitalisationDay->doctor->user->name . " " . $hospitalisationDay->doctor->user->prenom . "."];

            if ($request->bedcheck) {

                $bed = Bed::find($request->bed);
                if ($bed->status_occupied != 'no_occupied')
                    return ['status' => 'warning', 'message' => 'Bed of the bedroom occupied.'];

                $hospitalisationDay->bed_id = $bed->id;
                $hospitalisationDay->save();
            }

            if (isset($request->drug)) {

                if (count($request->drug) > 0) {

                    $hospitalisationDrugs = new HospitalisationDrugRequested();
                    $hospitalisationDrugs->hospitalisation_id = $hospitalisation->id;
                    $hospitalisationDrugs->save();

                    if ($request->protocol_type == 'interne') {
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

                        //consultation
                        $consultation = Consultation::findOrFail($request->consultation_id);

                        //ordonnance
                        $ordonnance = Ordonnance::create([
                            "reference" => codeOrdonnance($consultation->patient->code_patient, $consultation->patient->id),
                            "type" => 'interne',
                            "consultation_id" => $request->consultation_id,
                            "date" => date('Y-m-d')
                        ]);

                        //save prescriptions
                        foreach ($request->drug as $key => $drug) {

                            $drug = DrugHospital::find($request->drug[$key]);

                            $prescription = new Prescription();
                            $prescription->ordonnance_id = $ordonnance->id;
                            $prescription->drug_id = $drug->id;
                            $prescription->quantity = $request->quantity[$key] ?? 1;
                            $prescription->dosage = $request->dosage[$key] ?? $drug->posology;
                            $prescription->route_administration = $request->voie_admission[$key] ?? null;
                            $prescription->save();
                        }

                        $drugSale = new DrugSale();
                        $drugSale->type = 'hospitalisation';
                        $drugSale->hospitalisation_drug_requested_id = $hospitalisationDrugs->id;
                        $drugSale->hospital_id = Auth::user()->doctor->hospital->id;
                        $drugSale->price = $price;
                        $drugSale->save();

                    } elseif ($request->protocol_type == 'externe') {

                        foreach ($request->drug as $key => $drug) {

                            $drug = Drug::find($request->drug[$key]);

                            $protocol = new TherapeutiqueProtocol();
                            $protocol->protocol_type = 'external';
                            $protocol->day_hospitalisation_id = $hospitalisationDay->id;
                            $protocol->hospitalisation_drug_requested_id = $hospitalisationDrugs->id;
                            $protocol->drug_hospital_id = $drug->id;
                            $protocol->quantity = $request->quantity[$key] ?? 1;
                            $protocol->dosage = $request->dosage[$key] ?? null;
                            $protocol->voie_admission = $request->voie_admission[$key] ?? null;

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

                        }

                        //consultation
                        $consultation = Consultation::findOrFail($hospitalisation->consultation_id);

                        //ordonnance
                        $ordonnance = Ordonnance::create([
                            "reference" => codeOrdonnance($consultation->patient->code_patient, $consultation->patient->id),
                            "type" => 'externe',
                            "consultation_id" => $consultation->id,
                            "status" => 1,
                            "date" => date('Y-m-d')
                        ]);

                        //save prescriptions
                        foreach ($request->drug as $key => $drug) {

                            $drug = Drug::find($request->drug[$key]);

                            $prescription = new Prescription();
                            $prescription->ordonnance_id = $ordonnance->id;
                            $prescription->drug_id = $request->drug[$key];
                            $prescription->quantity = $request->quantity[$key] ?? 1 ?? 1;
                            $prescription->dosage = $request->dosage[$key] ?? $drug->posology;
                            $prescription->route_administration = $request->voie_admission[$key] ?? null;
                            $prescription->save();
                        }

                    } else {
                        return ['status' => 'error', 'message' => 'No type protocole selected.'];
                    }
                }
            }

            return ['status' => 'success', 'message' => 'Protocoles ajoutés avec succès.'];
        } catch (Throwable $err) {
            dd($err);
        }
    }

    public function validated(Request $request, $hosp)
    {

        try {

            $hospitalisation = Hospitalisation::find($hosp);


            if ($hospitalisation->status !== 'in_progress')
                return ['status' => 'error', 'message' => 'Hospitalisation already finished.'];

            $total = 0;
            $number_days = 0;

            //update last days hospitalisation
            $lastHospitalisationDay = DayHospitalisation::orderByDESC('day')->where('hospitalisation_id', $hospitalisation->id)->first();

            $lastHospitalisationDay->end_date = date('Y-m-d');
            $diff = Carbon::parse($lastHospitalisationDay->created_at)->diffInDays(Carbon::now());
            $lastHospitalisationDay->number_days = $diff != 0 ? $diff : 1;
            $lastHospitalisationDay->save();

            foreach ($hospitalisation->daysHospitalisation as $key => $dayH) {

                //find day hospitalisation
                $day = DayHospitalisation::find($dayH->id);

                if (!$day->end_date)
                    $day->end_date = date('Y-m-d');

                $day->status = 'terminé';

                //find bed price
                if ($day->bed_id) {
                    $bed = Bed::find($day->bed_id);
                    if ($bed) {
                        $bed->status_occupied = 'no_occupied';
                        $bed->save();
                        $day->price = ($bed->price * $day->number_days);
                    }
                }

                $day->save();

                $total += $day->price;
                $number_days += $day->number_days;
            }

            if ($request->mot_sortie) {
                $hospitalisation->mot_sortie = $request->mot_sortie;
            }

            //update hospitalisation
            $hospitalisation->price = $total;
            $hospitalisation->end_date = date('Y-m-d');
            $hospitalisation->status = 'finished';
            $hospitalisation->number_days = $number_days;
            $hospitalisation->save();

            $payment = new Payment();
            $payment->type = 'hospitalisation';
            $payment->date = Carbon::now()->format('Y-m-d');
            $payment->prix = $total;
            $payment->prix_normal = $total;
            $payment->hospital_id = Auth::user()->doctor->hospital_id;
            $payment->hospitalisation_id = $hospitalisation->id;
            $payment->save();
        } catch (Throwable $err) {
            dd($err);
        }

        return ['status' => 'success', 'message' => 'Hospitalisation successfully.'];
    }
}
