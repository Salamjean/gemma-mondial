<?php

namespace App\Http\Controllers\Secretariat;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Admission;
use Illuminate\Http\Request;
use App\Models\TypeConsultation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdmissionController extends Controller
{
    private function getHospitalId()
    {
        return optional(Auth::user()->secretariat)->hospital_id 
            ?? optional(optional(Auth::user()->secretariat)->hospital)->id 
            ?? optional(Auth::user()->cashier)->hospital_id 
            ?? optional(optional(Auth::user()->cashier)->hospital)->id 
            ?? optional(Auth::user()->infirmier)->hospital_id 
            ?? optional(Auth::user()->doctor)->hospital_id 
            ?? optional(Auth::user()->hospital)->id 
            ?? Auth::user()->hospital_id 
            ?? (function_exists('getUserHospitalId') ? getUserHospitalId() : null);
    }

    public function list()
    {
        $hospitalId = $this->getHospitalId();
        $query = Admission::orderByDESC("created_at")->with('patient.user', 'doctor.user', 'typeExamen');

        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        if (Auth::user()->role_as == 'secretariat' && Auth::user()->secretariat) {
            $query->where('secretaire_id', Auth::user()->secretariat->id);
        }

        $admissions = $query->get();
        return view('users.secretariat.admission.list', compact('admissions'));
    }

    public function today()
    {
        $today = Carbon::today();
        $hospitalId = $this->getHospitalId();
        $query = Admission::orderByDESC("created_at")
            ->whereDate('created_at', $today)
            ->with('patient.user', 'doctor.user', 'cashier.user');

        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        if (Auth::user()->role_as == 'secretariat' && Auth::user()->secretariat) {
            $query->where('secretaire_id', Auth::user()->secretariat->id);
        }

        $admissions = $query->get();
        return view('users.secretariat.admission.today', compact('admissions'));
    }

    public function history()
    {
        $hospitalId = $this->getHospitalId();
        $query = Admission::orderByDESC("created_at")->with('patient.user', 'doctor.user', 'cashier.user');

        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }

        if (Auth::user()->role_as == 'secretariat' && Auth::user()->secretariat) {
            $query->where('secretaire_id', Auth::user()->secretariat->id);
        }

        $admissions = $query->get();
        return view('users.secretariat.admission.history', compact('admissions'));
    }

    public function detail($id)
    {
        $admission = Admission::find($id);
        return view('users.secretariat.admission.detail', compact('admission'));
    }

    public function Impression($id)
    {
        $pdf =  Pdf::loadView('users.secretariat.admission.pdf.admission', ['admission' => Admission::findOrFail($id)]);
        $pdf->setPaper('A4', 'portrait')->render();

        $response = new Response();
        $response->setContent($pdf->output())->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', "inline; filename=$id-" . date('dmY') . ".pdf");

        return $response;
    }
}
