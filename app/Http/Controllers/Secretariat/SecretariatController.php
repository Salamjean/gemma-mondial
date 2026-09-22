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
use App\Services\AuditLogService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Eloquent\Builder;

class SecretariatController extends Controller
{
    /**
     * Vérifie si un médecin est actuellement disponible (jour de travail et créneau horaire actif).
     */
    public function isDoctorCurrentlyAvailable($doctor)
    {
        if (!$doctor || !$doctor->user) {
            return false;
        }

        $availability = $doctor->user->availability;
        if (!$availability || empty($availability->days)) {
            return false;
        }

        $days = json_decode($availability->days, true);
        if (!is_array($days) || empty($days)) {
            return false;
        }

        $days = array_map('strval', $days);

        $now = \Carbon\Carbon::now();
        $currentDayOfWeek = strval($now->dayOfWeek); // 0 (Dimanche) à 6 (Samedi)
        $currentDayOfWeekIso = strval($now->dayOfWeekIso); // 1 (Lundi) à 7 (Dimanche)
        $currentDayZeroMon = strval($now->dayOfWeekIso - 1); // 0 (Lundi) à 6 (Dimanche)

        $isToday = in_array($currentDayZeroMon, $days, true)
            || in_array($currentDayOfWeek, $days, true)
            || in_array($currentDayOfWeekIso, $days, true);

        if (!$isToday) {
            return false;
        }

        $dayIndex = intval($currentDayZeroMon); // 0 pour Lundi, ..., 6 pour Dimanche

        $startTimes = json_decode($availability->hour_start, true);
        $endTimes = json_decode($availability->hour_end, true);

        $startTime = '00:00';
        $endTime = '23:59';

        if (is_array($startTimes)) {
            if (isset($startTimes[$dayIndex]) && !empty($startTimes[$dayIndex])) {
                $startTime = $startTimes[$dayIndex];
            } elseif (isset($startTimes[0])) {
                $startTime = $startTimes[0];
            }
        } elseif (!empty($availability->hour_start)) {
            $startTime = $availability->hour_start;
        }

        if (is_array($endTimes)) {
            if (isset($endTimes[$dayIndex]) && !empty($endTimes[$dayIndex])) {
                $endTime = $endTimes[$dayIndex];
            } elseif (isset($endTimes[0])) {
                $endTime = $endTimes[0];
            }
        } elseif (!empty($availability->hour_end)) {
            $endTime = $availability->hour_end;
        }

        if (empty($startTime) || $startTime === '00:00') {
            $startTime = '00:00';
        }
        if (empty($endTime) || $endTime === '00:00') {
            $endTime = '23:59';
        }

        $currentTime = $now->format('H:i');
        $startFormatted = strlen($startTime) >= 5 ? substr($startTime, 0, 5) : '00:00';
        $endFormatted = strlen($endTime) >= 5 ? substr($endTime, 0, 5) : '23:59';

        // Marge de 30 minutes avant le début du créneau
        $startCarbon = \Carbon\Carbon::createFromFormat('H:i', $startFormatted)->subMinutes(30)->format('H:i');

        return ($currentTime >= $startCarbon && $currentTime <= $endFormatted);
    }

    private function getCurrentHospitalId()
    {
        return optional(Auth::user()->secretariat)->hospital_id 
            ?? optional(optional(Auth::user()->secretariat)->hospital)->id 
            ?? optional(Auth::user()->infirmier)->hospital_id 
            ?? optional(Auth::user()->doctor)->hospital_id 
            ?? optional(Auth::user()->hospital)->id 
            ?? Auth::user()->hospital_id 
            ?? (function_exists('getUserHospitalId') ? getUserHospitalId() : null);
    }

    public function getDoctors($prestations)
    {
        $hospitalId = $this->getCurrentHospitalId();
        $doctors = PrestationDoctor::whereHas('doctor', function ($q) use ($hospitalId) {
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })->with(['doctor.user.availability', 'prestationHospital.prestationService'])
        ->where('prestation_hospital_id', $prestations)
        ->get()
        ->filter(function ($pd) {
            return $this->isDoctorCurrentlyAvailable($pd->doctor);
        })->values();
        return response()->json($doctors);
    }
    public function getPrestations($service)
    {
        $hospitalId = $this->getCurrentHospitalId();

        $prestations = PrestationHospital::where('status', 0)
            ->with(['prestationService', 'serviceHospital.service'])
            ->whereHas('serviceHospital', function (Builder $query) use ($hospitalId, $service) {
                if ($hospitalId) {
                    $query->where('hospital_id', $hospitalId);
                }
                $query->where(function (Builder $q) use ($service) {
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
        $hospitalId = $this->getCurrentHospitalId();
        $query = Infirmier::with('user');
        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

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
    public function getServiceHospital()
    {
        $hospitalId = $this->getCurrentHospitalId();
        $query = ServiceHospital::whereHas('service')
            ->with('service')
            ->where('status', 0);
        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }
        $services = $query->get();
        return response()->json($services);
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
        $hospitalId = $this->getCurrentHospitalId();
        $query = TypeExamen::where('status', 0);
        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }
        $type_examens = $query->get();
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
        $search = trim($request->search);
        if ($request->ajax()) {
            $data = Patient::with(['user', 'declarationDeces.deces'])
                ->where(function ($q) use ($search) {
                    $q->where('code_patient', 'like', '%' . $search . '%')
                      ->orWhere('telephone', 'like', '%' . $search . '%')
                      ->orWhere('num_cmu', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($query) use ($search) {
                          $parts = array_values(array_filter(explode(" ", $search)));
                          if (count($parts) >= 2) {
                              $nom = $parts[0];
                              $pnom = implode(" ", array_slice($parts, 1));
                              $query->where(function ($sub) use ($nom, $pnom) {
                                  $sub->where('name', 'like', '%' . $nom . '%')
                                      ->where('prenom', 'like', '%' . $pnom . '%');
                              })->orWhere(function ($sub) use ($nom, $pnom) {
                                  $sub->where('name', 'like', '%' . $pnom . '%')
                                      ->where('prenom', 'like', '%' . $nom . '%');
                              });
                          } else {
                              $query->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('prenom', 'like', '%' . $search . '%');
                          }
                      });
                })->limit(10)->get();

            if (!empty($search) && strlen($search) >= 2) {
                AuditLogService::log(
                    'RECHERCHE_PATIENT',
                    'SECRETARIAT',
                    "Recherche rapide patient pour affectation : \"{$search}\" (" . count($data) . " résultat(s))",
                    ['terme_recherche' => $search, 'resultats_count' => count($data)]
                );
            }

            $output = '<div class="box"><div class="dropdown"><ul class="dropdown-menu" style="display:block; position:relative;width:100%;cursor:pointer;">';

            if (count($data) > 0) {
                foreach ($data as $row) {
                    $patientFullName = ($row->user->name ?? '') . ' ' . ($row->user->prenom ?? '');
                    $codeDm = $row->code_patient ? " [{$row->code_patient}]" : "";
                    $isDeceased = ($row->status === 0 || $row->status === '0') || !is_null($row->declarationDeces);
                    if ($isDeceased) {
                        $output .= "<li class='bg-danger-subtle'><option class='dropdown-item text-danger fw-bold' value='{$row->id}' data-deceased='1'><i class='fa-solid fa-skull-crossbones me-1'></i> {$patientFullName}{$codeDm} (DÉCÉDÉ)</option></li>";
                    } else {
                        $output .= "<li><option class='dropdown-item' value='{$row->id}' data-deceased='0'>{$patientFullName}{$codeDm}</option></li>";
                    }
                }
            } else {
                $output .= '<li class="p-2 text-muted">Patient introuvable !</li>';
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
        $hospitalId = $this->getCurrentHospitalId();
        $query = ServiceHospital::whereHas('service')
            ->with('service')
            ->where('status', 0);
        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }
        $services = $query->get();
        return response()->json($services);
    }

    public function getMedecins(Request $request)
    {
        $hospitalId = $this->getCurrentHospitalId();
        $query = Doctor::with(['user.availability', 'serviceHospital.service']);
        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        if ($request->filled('service_name')) {
            $serviceName = $request->service_name;
            // Si le service est Soins infirmier, aucun médecin n'intervient
            if (preg_match('/infirmier|soin/i', $serviceName)) {
                return response()->json(['medecins' => []]);
            }
            $query->where(function ($q) use ($serviceName) {
                $q->whereHas('serviceHospital.service', function ($sq) use ($serviceName) {
                    $sq->where('libelle', $serviceName)->orWhere('id', $serviceName);
                })->orWhereHas('prestationDoctors.prestationHospital.serviceHospital.service', function ($sq) use ($serviceName) {
                    $sq->where('libelle', $serviceName)->orWhere('id', $serviceName);
                });
            });
        }

        $medecins = $query->get()->filter(function ($doctor) {
            return $this->isDoctorCurrentlyAvailable($doctor);
        })->values();

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
        $hospitalId = $this->getCurrentHospitalId();

        $availabilities = Availability::whereHas('user', function ($q) use ($hospitalId) {
            if ($hospitalId) {
                $q->whereHas('doctor', function ($d) use ($hospitalId) {
                    $d->where('hospital_id', $hospitalId);
                })->orWhereHas('infirmier', function ($i) use ($hospitalId) {
                    $i->where('hospital_id', $hospitalId);
                })->orWhereHas('secretariat', function ($s) use ($hospitalId) {
                    $s->where('hospital_id', $hospitalId);
                })->orWhereHas('cashier', function ($c) use ($hospitalId) {
                    $c->where('hospital_id', $hospitalId);
                })->orWhereHas('accountant', function ($a) use ($hospitalId) {
                    $a->where('hospital_id', $hospitalId);
                });
            }
        })->with([
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

        // Rendez-vous enregistrés de cet hôpital
        $rendezVousList = \App\Models\RendezVous::whereHas('doctor', function ($q) use ($hospitalId) {
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })->orWhereHas('patient', function ($q) use ($hospitalId) {
            if ($hospitalId) {
                $q->where('hospital_id', $hospitalId);
            }
        })->with(['patient.user', 'doctor.user'])->get();
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
        $search = trim($request->search);
        if ($request->ajax()) {
            $data = Patient::with(['user', 'declarationDeces.deces'])
                ->where(function ($q) use ($search) {
                    $q->where('code_patient', 'like', '%' . $search . '%')
                      ->orWhere('telephone', 'like', '%' . $search . '%')
                      ->orWhere('num_cmu', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function ($query) use ($search) {
                          $parts = array_values(array_filter(explode(" ", $search)));
                          if (count($parts) >= 2) {
                              $nom = $parts[0];
                              $pnom = implode(" ", array_slice($parts, 1));
                              $query->where(function ($sub) use ($nom, $pnom) {
                                  $sub->where('name', 'like', '%' . $nom . '%')
                                      ->where('prenom', 'like', '%' . $pnom . '%');
                              })->orWhere(function ($sub) use ($nom, $pnom) {
                                  $sub->where('name', 'like', '%' . $pnom . '%')
                                      ->where('prenom', 'like', '%' . $nom . '%');
                              });
                          } else {
                              $query->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('prenom', 'like', '%' . $search . '%');
                          }
                      });
                })->limit(10)->get();

            if (!empty($search) && strlen($search) >= 2) {
                AuditLogService::log(
                    'RECHERCHE_PATIENT',
                    'SECRETARIAT',
                    "Recherche patient hospitalisation : \"{$search}\" (" . count($data) . " résultat(s))",
                    ['terme_recherche' => $search, 'resultats_count' => count($data)]
                );
            }

            $output = '<div class="box"><div class="dropdown"><ul class="dropdown-menu" style="display:block; position:relative;width:100%;cursor:pointer;">';

            if (count($data) > 0) {
                foreach ($data as $row) {
                    $patientFullName = ($row->user->name ?? '') . ' ' . ($row->user->prenom ?? '');
                    $codeDm = $row->code_patient ? " [{$row->code_patient}]" : "";
                    $isDeceased = ($row->status === 0 || $row->status === '0') || !is_null($row->declarationDeces);
                    if ($isDeceased) {
                        $output .= "<li class='bg-danger-subtle'><option class='dropdown-item text-danger fw-bold' value='{$row->id}' data-deceased='1'><i class='fa-solid fa-skull-crossbones me-1'></i> {$patientFullName}{$codeDm} (DÉCÉDÉ)</option></li>";
                    } else {
                        $output .= "<li><option class='dropdown-item' value='{$row->id}' data-deceased='0'>{$patientFullName}{$codeDm}</option></li>";
                    }
                }
            } else {
                $output .= '<li class="p-2 text-muted">Patient introuvable !</li>';
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
