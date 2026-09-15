<?php

namespace App\Http\Controllers\Secretariat;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Region;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Hospital;
use App\Models\Infirmier;
use App\Models\Department;
use App\Models\TypeExamen;
use App\Models\Availability;
use Illuminate\Http\Request;
use App\Models\SubPrefecture;
use App\Models\ServiceHospital;
use App\Models\PrestationDoctor;
use App\Models\PrestationService;
use App\Models\PrestationHospital;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Eloquent\Builder;

class SecretariatController extends Controller
{
    public function getDoctors($prestations)
    {
        // dd($prestations);
        $hospital = Hospital::find(Auth::user()->secretariat->hospital_id);
        $doctors = PrestationDoctor::with('doctor.user', 'prestationHospital.prestationService')->where('prestation_hospital_id', $prestations)->get();
        return response()->json($doctors);
    }
    public function getPrestations($service)
    {
        $hospitalId = Auth::user()->secretariat->hospital_id ?? Auth::user()->secretariat->hospital->id ?? null;

        $prestations = PrestationHospital::where('status', 0)
            ->with(['prestationService', 'serviceHospital.service'])
            ->whereHas('serviceHospital', function (Builder $query) use ($hospitalId, $service) {
                $query->where('hospital_id', $hospitalId)
                      ->where(function (Builder $q) use ($service) {
                          $q->where('id', $service)
                            ->orWhere('service_id', $service)
                            ->orWhereHas('service', function (Builder $sq) use ($service) {
                                $sq->where('libelle', $service)->orWhere('id', $service);
                            });
                      });
            })->get();
        return response()->json($prestations);
    }
    public function getInfirmiers($service = null)
    {
        $hospitalId = Auth::user()->secretariat->hospital->id ?? Auth::user()->secretariat->hospital_id ?? null;
        $query = Infirmier::where('hospital_id', $hospitalId)->with('user');

        if ($service && $service !== 'all') {
            $query->where(function (Builder $outerQuery) use ($service) {
                $outerQuery->whereHas('serviceHospital', function (Builder $query) use ($service) {
                    $query->where(function (Builder $q) use ($service) {
                        $q->where('id', $service)
                          ->orWhere('service_id', $service)
                          ->orWhereHas('service', function (Builder $sq) use ($service) {
                              $sq->where('libelle', $service)->orWhere('id', $service);
                          });
                    });
                })->orWhereHas('services.serviceHospital', function (Builder $query) use ($service) {
                    $query->where(function (Builder $q) use ($service) {
                        $q->where('id', $service)
                          ->orWhere('service_id', $service)
                          ->orWhereHas('service', function (Builder $sq) use ($service) {
                              $sq->where('libelle', $service)->orWhere('id', $service);
                          });
                    });
                });
            });
        }

        $infirmiers = $query->get();
        return response()->json($infirmiers);
    }
    public function getPrestationServices()
    {
        $prestation_services = PrestationService::where('status', 0)->get();
        return response()->json(['prestation_services' => $prestation_services]);
    }
    public function getPrixPrestation(Request $request)
    {
        $prestationServiceId = $request->input('prestationServiceId');
        $prestationService = PrestationHospital::find($prestationServiceId);

        if ($prestationService) {
            return response()->json(['prix' => $prestationService->prix]);
        } else {
            return response()->json(['prix' => 'Type de consultation introuvable.']);
        }
    }
    public function listePatients()
    {
        $patients = Patient::where('status', 1)->with('user')->get();
        return response()->json(['patients' => $patients]);
    }


    /*****Function pour examen  ********/
    public function getTypeExamens()
    {
        $type_examens = TypeExamen::where('hospital_id', Auth::user()->secretariat->hospital->id)->where('status', 0)->where('hospital_id', Auth::user()->secretariat->hospital_id)->get();
        return response()->json(['type_examens' => $type_examens]);
    }

    public function getPrixExamen(Request $request)
    {
        $typeExamenId = $request->input('typeExamenId');
        $typeExamen = TypeExamen::find($typeExamenId);

        if ($typeExamen) {
            return response()->json(['prix' => $typeExamen->prix]);
        } else {
            return response()->json(['prix' => 'Prix introuvable.']);
        }
    }

    public function searchPatient(Request $request)
    {
        $name = explode(" ", $request->search, 2);
        if ($request->ajax()) {
            $nom = $name[0];
            $pnom = $name[1];

            $data = Patient::whereHas('user', function ($query) use ($nom, $pnom) {
                $query->where('name', 'like', '%' . $nom . '%')
                    ->where('prenom', 'like', '%' . $pnom . '%');
            })->limit(5)->get();

            //return response()->json(['data' => $data]);

            $output = '<div class="box"><div class="dropdown"><ul class="dropdown-menu" style="display:block; position:relative;width:100%;cursor:pointer;">';

            if (count($data) > 0) {
                foreach ($data as $row) {

                    $patientFullName = $row->user->name . ' ' . $row->user->prenom;
                    $output .= "<li><option class='dropdown-item' value='{$row->id}'>{$patientFullName}</option></li>";
                }
            } else {
                $output .= '<li>Patients introuvable !</li>';
            }
            $output .= '</ul></div></div>';

            return $output;
        }
    }

    public function getCommunes(Request $request)
    {
        $regionId = $request->input('region_id');
        $communes = Department::where('region_id', $regionId)->get();
        return response()->json($communes);
    }
    public function getSubPrefectures(Request $request)
    {
        $servceId = $request->input('servce_id');
        $subPrefectures = SubPrefecture::where('servce_id', $servceId)->get();
        return response()->json($subPrefectures);
    }
    public function getRegions()
    {
        $regions = Region::get();
        return response()->json($regions);
    }

    public function getHopitalServices()
    {
        $hospitalId = Auth::user()->secretariat->hospital_id ?? Auth::user()->secretariat->hospital->id ?? null;
        $services = ServiceHospital::where('hospital_id', $hospitalId)
            ->whereHas('service')
            ->with('service')
            ->where('status', 0)
            ->get();
        return response()->json($services);
    }

    public function getMedecins(Request $request)
    {
        $hospitalId = Auth::user()->secretariat->hospital_id ?? null;
        $query = Doctor::with('user');
        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }
        $medecins = $query->get();
        return response()->json(['medecins' => $medecins]);
    }

    public function getLieuNaissance()
    {
        $lieu_naissance = SubPrefecture::get();
        return response()->json($lieu_naissance);
    }
    public function getResidenceActuelle()
    {
        $residence_actuelle = SubPrefecture::get();
        return response()->json($residence_actuelle);
    }
    public function getAvailabilities()
    {
        $availabilities = Availability::with([
            'user.doctor', 
            'user.infirmier', 
            'user.secretariat', 
            'user.cashier', 
            'user.accountant', 
            'user.patient'
        ])->get();
        $events = [];
        $doctorsCount = 0;
        $infirmiersCount = 0;
        $processedUsers = [];

        foreach ($availabilities as $availability) {
            $user = $availability->user;
            if (!$user) continue;

            if (!isset($processedUsers[$user->id])) {
                $processedUsers[$user->id] = true;
                $role = strtolower($user->role_as ?? '');
                if (str_contains($role, 'doctor') || str_contains($role, 'medecin') || str_contains($role, 'docteur')) {
                    $doctorsCount++;
                } else if (str_contains($role, 'infirmier') || str_contains($role, 'nurse')) {
                    $infirmiersCount++;
                }
            }

            $days = json_decode($availability->days) ?? [];
            $start_times = json_decode($availability->hour_start) ?? [];
            $end_times = json_decode($availability->hour_end) ?? [];

            $startRange = Carbon::now()->startOfMonth()->subWeeks(2);
            $endRange = Carbon::now()->endOfMonth()->addWeeks(4);

            foreach ($days as $index => $dayNum) {
                $startTime = $start_times[$index] ?? '08:00';
                $endTime = $end_times[$index] ?? '17:00';

                $current = $startRange->copy();
                while ($current->lte($endRange)) {
                    if ($current->dayOfWeek == $dayNum || $current->dayOfWeekIso == $dayNum) {
                        $roleName = $user->role_as ?? 'Personnel médical';
                        $isDoctor = str_contains(strtolower($roleName), 'doctor') || str_contains(strtolower($roleName), 'medecin');
                        $isInfirmier = str_contains(strtolower($roleName), 'infirmier');

                        $color = $isDoctor ? '#4f46e5' : ($isInfirmier ? '#059669' : '#d97706');
                        $badgeClass = $isDoctor ? 'bg-primary' : ($isInfirmier ? 'bg-success' : 'bg-warning');

                        $fullName = trim(($user->name ?? '') . ' ' . ($user->prenom ?? ''));

                        $events[] = [
                            'id' => 'avail_' . $availability->id . '_' . $current->format('Y-m-d') . '_' . $index,
                            'title' => $fullName,
                            'start' => $current->format('Y-m-d') . 'T' . $startTime,
                            'end' => $current->format('Y-m-d') . 'T' . $endTime,
                            'backgroundColor' => $color,
                            'borderColor' => $color,
                            'textColor' => '#ffffff',
                            'extendedProps' => [
                                'type' => 'availability',
                                'user_id' => $user->id,
                                'full_name' => $fullName,
                                'role' => ucfirst($user->role_as ?? 'Personnel'),
                                'role_key' => $isDoctor ? 'doctor' : ($isInfirmier ? 'infirmier' : 'autre'),
                                'telephone' => $user->telephone ?? 'Non renseigné',
                                'email' => $user->email ?? 'Non renseigné',
                                'start_time' => $startTime,
                                'end_time' => $endTime,
                                'badge_class' => $badgeClass
                            ]
                        ];
                    }
                    $current->addDay();
                }
            }
        }

        // Rendez-vous enregistrés
        $rendezVousList = \App\Models\RendezVous::with(['patient.user', 'doctor.user'])->get();
        foreach ($rendezVousList as $rdv) {
            if ($rdv->date) {
                $docName = $rdv->doctor && $rdv->doctor->user ? $rdv->doctor->user->name : 'Médecin';
                $patName = $rdv->patient && $rdv->patient->user ? trim($rdv->patient->user->name . ' ' . ($rdv->patient->user->prenom ?? '')) : 'Patient';
                $startTime = $rdv->heure ?? '09:00';

                $events[] = [
                    'id' => 'rdv_' . $rdv->id,
                    'title' => 'RDV: ' . $patName,
                    'start' => $rdv->date . 'T' . $startTime,
                    'backgroundColor' => '#e11d48',
                    'borderColor' => '#e11d48',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'rdv',
                        'role' => 'Rendez-vous Patient',
                        'role_key' => 'rdv',
                        'full_name' => $patName,
                        'doctor_name' => $docName,
                        'motif' => $rdv->motif ?? 'Consultation',
                        'start_time' => $startTime,
                        'badge_class' => 'bg-danger'
                    ]
                ];
            }
        }

        return view('users.secretariat.agenda', [
            'title' => 'Agenda & Planning du Personnel',
            'events' => $events,
            'totalAvailabilities' => count($availabilities),
            'doctorsCount' => $doctorsCount,
            'infirmiersCount' => $infirmiersCount,
            'rdvCount' => count($rendezVousList)
        ]);
    }
    
    public function getPatientHospitalisation(Request $request) {
        $name = explode(" ", $request->search, 2);
        if ($request->ajax()) {
            $nom = $name[0];
            $pnom = $name[1];

            $data = Patient::whereHas('user', function ($query) use ($nom, $pnom) {
                $query->where('name', 'like', '%' . $nom . '%')
                    ->where('prenom', 'like', '%' . $pnom . '%');
            })->limit(5)->get();

            //return response()->json(['data' => $data]);

            $output = '<div class="box"><div class="dropdown"><ul class="dropdown-menu" style="display:block; position:relative;width:100%;cursor:pointer;">';

            if (count($data) > 0) {
                foreach ($data as $row) {

                    $patientFullName = $row->user->name . ' ' . $row->user->prenom;
                    $output .= "<li><option class='dropdown-item' value='{$row->id}'>{$patientFullName}</option></li>";
                }
            } else {
                $output .= '<li>Patients introuvable !</li>';
            }
            $output .= '</ul></div></div>';

            return $output;
        }
    }
    public function searchHospitalisation()
    {
        return view('users.secretariat.search_hospitalisation', ['title' => 'Vérification du status du patient']);
    }
         
}
