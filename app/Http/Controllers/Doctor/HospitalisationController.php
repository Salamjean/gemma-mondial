<?php

namespace App\Http\Controllers\Doctor;

use Illuminate\Http\Request;
use App\Models\Hospitalisation;
use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Bedroom;
use App\Models\Consultation;
use App\Models\Drug;
use App\Models\DrugHospital;
use App\Models\Infirmier;
use App\Models\SuivieHospitalisation;
use App\Repositories\Doctor\ConsultationRepository;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Doctor\HospitalisationRepository;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class HospitalisationController extends Controller
{

    public function pending_room()
    {
        $hospitalisations = (new HospitalisationRepository())->pending_room();
        return view('users.doctor.hospitalisation.list', [
            'hospitalisations' => $hospitalisations, 
            'activeTab' => 'pending_room',
            'pageTitle' => 'Liste des patients en attente de chambre'
        ]);
    }

    public function in_progress()
    {
        $hospitalisations = (new HospitalisationRepository())->in_progress();
        return view('users.doctor.hospitalisation.list', [
            'hospitalisations' => $hospitalisations, 
            'activeTab' => 'in_progress',
            'pageTitle' => 'Liste des patients en cours d\'hospitalisation'
        ]);
    }

    public function history()
    {
        $hospitalisations = (new HospitalisationRepository())->history();
        return view('users.doctor.hospitalisation.list', [
            'hospitalisations' => $hospitalisations, 
            'activeTab' => 'history',
            'pageTitle' => 'Historique des hospitalisations'
        ]);
    }

    public function detail($id)
    {
        $hospitalisation = (new HospitalisationRepository())->show($id);
        return view('users.doctor.hospitalisation.detail', ['hospitalisation' => $hospitalisation]);
    }

    public function manufacturing($id)
    {
        $hospitalisation = (new HospitalisationRepository())->show($id);
        return view('users.doctor.hospitalisation.detail', ['hospitalisation' => $hospitalisation]);
    }

    public function downloadFacture($id)
    {
        $hospitalisation = (new HospitalisationRepository())->show($id);
        $pdf = Pdf::loadView('users.doctor.hospitalisation.pdf_facture', compact('hospitalisation'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('facture_hospitalisation_' . ($hospitalisation->code ?? $hospitalisation->id) . '.pdf');
    }

    public function ordonnances($id){
        $hospitalisation = (new HospitalisationRepository())->show($id);
        $consultation = Consultation::find($hospitalisation->consultation_id);

        return view('users.doctor.hospitalisation.ordonnances', ['consultation' => $consultation]);

    }

    public function edit($id)
    {

        $hospitalisation = (new HospitalisationRepository())->show($id);

        return view('users.doctor.hospitalisation.edit', compact( 'hospitalisation'));
    }

    public function protocolPage($type, $id)
    {
        $hospitalisation = (new HospitalisationRepository())->show($id);

        if($type == 'externe')
            $drugs = Drug::all();
        else
            $drugs = DrugHospital::where('hospital_id', auth()->user()->doctor->hospital_id)->get();

        return view('users.doctor.hospitalisation.protocol.protocol', compact('drugs', 'hospitalisation', 'type'));
    }

    public function update_new_day(Request $request, $id)
    {

        $res = (new HospitalisationRepository())->store($request, $id);

        return redirect(route('doctor.hospitalisation.edit', $id))->with($res['status'], $res['message']);
    }

    public function update_already(Request $request, $id)
    {

        $res = (new HospitalisationRepository())->update($request, $id);

        return redirect(route('doctor.hospitalisation.edit', $id))->with($res['status'], $res['message']);

    }

    public function validated(Request $request, $id)
    {

        $res = (new HospitalisationRepository())->validated($request, $id);

        return back()->with($res['status'], $res['message']);
    }


    //ajex request
    public function bedroom($type)
    {
        $bedrooms = Bedroom::where('type', $type)
            ->where('hospital_id', Auth::user()->doctor->hospital_id)
            ->get();
        return response([
            'bedrooms' => $bedrooms
        ], 200);
    }

    public function bed($id)
    {

        $beds = Bed::where('bedroom_id', $id)->where('status_occupied', 'no_occupied')->get();

        return response([
            'beds' => $beds
        ], 200);
    }

    public function infirmiers()
    {

        $infirmier = Infirmier::where('service_hospital_id', Auth::user()->doctor->service_hospital_id)->with('user')->get();

        return response([
            'infirmiers' => $infirmier
        ], 200);
    }

    public function drugs()
    {

        $drugs = DrugHospital::where('hospital_id', Auth::user()->doctor->hospital_id)->with('drug')->get();

        return response([
            'drugs' => $drugs
        ], 200);
    }

    public function drugsData()
    {

        $drugs = Drug::all();

        return response([
            'drugs' => $drugs
        ], 200);
    }

}
