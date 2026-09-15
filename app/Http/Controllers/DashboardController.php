<?php

namespace App\Http\Controllers;

use App\Models\DrugSale;
use Carbon\Carbon;
use App\Models\Admission;
use App\Models\CareRequested;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Accountant\DashboardRepository;

use App\Repositories\DoctorRepository;
use App\Repositories\Super\DashboardSuperRepository;
use App\Repositories\Doctor\DashboardDoctorRepository;
use App\Repositories\Cashier\DashboardCashierRepository;
use App\Repositories\Hospital\DashboardHospitalRepository;
use App\Repositories\Infirmier\DashboardInfirmierRepository;
use App\Repositories\Patient\DashboardRepository as DashboardPatientRepository;
use Illuminate\Contracts\Database\Eloquent\Builder;

class DashboardController extends Controller
{

    public  function userAuth()
    {
        return ['user' => auth()->user()];
    }

    public function index()
    {

        if ($this->userAuth()['user']['role_as'] == 'super') {

            $dashboard = new DashboardSuperRepository();

            $dataBirth = $dashboard->birth();

            $dataDeath = $dashboard->death();

            $dataConsultation = $dashboard->consultation();

            return view('dashboard.index', compact('dataBirth', 'dataDeath', 'dataConsultation'));
        }

        if ($this->userAuth()['user']['role_as'] == 'hospital') {

            $dashboard = new DashboardHospitalRepository($this->userAuth()['user']['id']);

            $data = $dashboard->dabhboard();

            $dataBirth = $data['birth'];

            $dataDeath = $data['death'];

            $dataConsultation = $data['consultation'];

            return view('dashboard.index', compact('dataBirth', 'dataDeath', 'dataConsultation'));
        }

        if ($this->userAuth()['user']['role_as'] == 'cashier') {

            $today = Carbon::today();

            $dashboard = new DashboardCashierRepository($this->userAuth()['user']['id']);

            $dataAdminssion = $dashboard->admissionCashier();

            return view('dashboard.index', compact('dataAdminssion', 'today'));
        }

        if ($this->userAuth()['user']['role_as'] == 'secretariat') {

            $today = Carbon::today();
            $hospitalId = Auth::user()->secretariat->hospital_id ?? (Auth::user()->secretariat->hospital->id ?? null);

            $admissions = Admission::orderByDESC("created_at")
                ->where('secretaire_id', Auth::user()->secretariat->id)
                ->where('hospital_id', $hospitalId)
                ->with('patient.user', 'doctor.user')
                ->whereDate('created_at', $today)
                ->get();

            $totalPatients = Patient::where('hospital_id', $hospitalId)->count();
            $todayAdmissionsCount = $admissions->count();
            $totalAdmissionsCount = Admission::where('hospital_id', $hospitalId)->count();
            $monthPatientsCount = Patient::where('hospital_id', $hospitalId)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            // Personnel disponible aujourd'hui
            $todayDayOfWeek = Carbon::now()->dayOfWeek;
            $todayDayOfWeekIso = Carbon::now()->dayOfWeekIso;

            $availabilities = \App\Models\Availability::with([
                'user.doctor', 
                'user.infirmier', 
                'user.secretariat', 
                'user.cashier', 
                'user.accountant', 
                'user.patient'
            ])->get();
            $todayAvailablePersonnel = [];

            foreach ($availabilities as $avail) {
                if (!$avail->user) continue;
                $days = json_decode($avail->days) ?? [];
                $startTimes = json_decode($avail->hour_start) ?? [];
                $endTimes = json_decode($avail->hour_end) ?? [];

                foreach ($days as $idx => $dNum) {
                    if ($dNum == $todayDayOfWeek || $dNum == $todayDayOfWeekIso) {
                        $todayAvailablePersonnel[] = [
                            'id' => $avail->user->id,
                            'name' => trim(($avail->user->name ?? '') . ' ' . ($avail->user->prenom ?? '')),
                            'role' => $avail->user->role_as ?? 'Personnel',
                            'telephone' => $avail->user->telephone ?: 'Non renseigné',
                            'hour_start' => $startTimes[$idx] ?? '08:00',
                            'hour_end' => $endTimes[$idx] ?? '17:00',
                        ];
                        break;
                    }
                }
            }

            return view('dashboard.index', compact(
                'admissions', 'today', 'totalPatients', 
                'todayAdmissionsCount', 'totalAdmissionsCount', 
                'monthPatientsCount', 'todayAvailablePersonnel'
            ));
        }

        if ($this->userAuth()['user']['role_as'] == 'accountant') {

            $today = Carbon::today();
            $dashboard = new DashboardRepository($this->userAuth()['user']['id']);

            $dataMontantPercue = $dashboard->montant();

            $dataMontantNormal = $dashboard->montantN();

            $dataMontantAssurance = $dashboard->montantA();

            return view('dashboard.index', compact('dataMontantPercue', 'dataMontantNormal', 'dataMontantAssurance', 'today'));
        }

        if ($this->userAuth()['user']['role_as'] == 'doctor') {

            $dashboard = new DashboardDoctorRepository($this->userAuth()['user']['id']);

            $data = $dashboard->dashboard();

            return view('dashboard.index', compact('data'));
        }

        if ($this->userAuth()['user']['role_as'] == 'patient') {

            $dashboard = new DashboardPatientRepository($this->userAuth()['user']['id']);

            $patient = Patient::findOrFail(Auth::user()->patient->id);

            $data = $dashboard->dashboard();

            $declarations = $dashboard->declaration();

            $consultations = $dashboard->consultation();

            return view('users.patient.dashboard', compact('data', 'patient', 'declarations', 'consultations'));
        }

        if ($this->userAuth()['user']['role_as'] == 'pharmacy') {

            $hospitalId = Auth::user()->pharmacy->hospital_id;
            $sum = DrugSale::where('pharmacy_id', Auth::user()->pharmacy->id)->whereDate('created_at', date('Y-m-d'))->where('status', 'success')->sum('price');
            $payments_pending = DrugSale::whereDate('created_at', date('Y-m-d'))->with('careRequested.admission.patient.user', 'hospitalisationDrugRequested')->where('status', 'pending')
            ->where('hospital_id', $hospitalId)
            ->get();


            return view("dashboard.index", compact('payments_pending', 'sum'));
        }

        if ($this->userAuth()['user']['role_as'] == 'infirmier') {

            $infirmierId = Auth::user()->infirmier->id;

            if (Auth::user()->infirmier->serviceHospital->service->id == 5) {

                $pending_count = CareRequested::with('admission')->whereHas('admission', function ($query) use ($infirmierId) {
                    $query->where('infirmier_id', $infirmierId);
                })->where('status', 'pending')->whereDate('created_at', date('Y-m-d'))->count();

                $payment_pending_count = CareRequested::with('admission')->whereHas('admission', function ($query) use ($infirmierId) {
                    $query->where('infirmier_id', $infirmierId);
                })->where('status', 'payment_pending')->whereDate('created_at', date('Y-m-d'))->count();

                $payment_success_count = CareRequested::with('admission')->whereHas('admission', function ($query) use ($infirmierId) {
                    $query->where('infirmier_id', $infirmierId);
                })->where('status', 'payment_success')->whereDate('created_at', date('Y-m-d'))->count();

                $success_count = CareRequested::with('admission')->whereHas('admission', function ($query) use ($infirmierId) {
                    $query->where('infirmier_id', $infirmierId);
                })->where('status', 'success')->whereDate('created_at', date('Y-m-d'))->count();

                $count = [
                    'pending_count' => $pending_count,
                    'success_count' => $success_count,
                    'payment_pending_count' => $payment_pending_count,
                    'payment_success_count' => $payment_success_count
                ];

                $cares = CareRequested::with('admission')->whereHas('admission', function ($query) use ($infirmierId) {
                    $query->where('infirmier_id', $infirmierId);
                })->where('status', '!=', 'success')->whereDate('created_at', date('Y-m-d'))->get();

                return view('dashboard.index', compact('cares', 'count'));
            }

            $dashboard = new DashboardInfirmierRepository($this->userAuth()['user']['id']);

            $data = $dashboard->consultation();

            return view('dashboard.index', compact('data'));

        }
    }
}
