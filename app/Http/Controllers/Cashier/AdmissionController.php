<?php

namespace App\Http\Controllers\Cashier;

use App\Repositories\Cashier\AdmissionRepository;
use Carbon\Carbon;
use App\Models\Admission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\CareRequested;
use App\Models\Payment;
use App\Models\TypeAssurance;
use App\Repositories\Cashier\AssuranceRepository;
use App\Repositories\Cashier\ConsultationRepository;
use App\Repositories\Cashier\ExamenRepository;
use App\Repositories\Cashier\HospitalisationRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class AdmissionController extends Controller
{


    public function repository()
    {
        return new AdmissionRepository();
    }

    public function consultation()
    {
        return new ConsultationRepository();
    }

    public function examen()
    {
        return new ExamenRepository();
    }

    public function assurance()
    {
        return new AssuranceRepository();
    }


    public function list()
    {
        $type = 'all';
        return view('users.cashier.admission.list', ['payments' => $this->repository()->payment(), 'title', 'Liste des paiements', 'type' => $type]);
    }

    public function admission()
    {
        $type = 'admission';

        return view('users.cashier.admission.list', ['payments' => $this->repository()->payment('admission'), 'title','Liste des paiements (Admission)', 'type' => $type]);
    }

    public function hospitalisation()
    {
        $type = 'hospitalisation';

        return view('users.cashier.admission.list', ['payments' => $this->repository()->payment('hospitalisation'), 'title','Liste des paiements (Hospitalisation)', 'type' => $type]);
    }

    public function show($id)
    {
        return view('users.cashier.admission.show', ['payment' => $this->repository()->show($id), 'assurances' => $this->assurance()->all()]);
    }

    public function details($id)
    {
        return view('users.cashier.admission.details', ['payment' => $this->repository()->show($id)]);
    }
    public function Impression($id)
    {
        $pdf =  Pdf::loadView('users.cashier.admission.pdf.admission', ['payment' => Payment::findOrFail($id)]);
        $pdf->setPaper('A4', 'portrait')->render();

        $response = new Response();
        $response->setContent($pdf->output())->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', "inline; filename=$id-" . date('dmY') . ".pdf");

        return $response;
    }

    public function validated(Request $request, $id)
    {

        //verification admission

        $verify = $this->repository()->verify($id);
        if ($verify['status'] == 'failure')
            return back()->withErrors('Paiement déja effectué.');


        // validation admission
        $res = $this->repository()->validated($id, $request);

        // dd($res);
        //type admission
        if ($res['type'] == 'admission') {

            //verification si soins infirmier ou prise de constante
            if ($res['prestation']['service_id'] == 5) {
                $careRequest = new CareRequested();
                $careRequest->type = $res['prestation']['libelle'];
                $careRequest->admission_id = $res['data']['id'];
                $careRequest->save();
            } else {
                $this->consultation()->store($res['data']);
            }
        }

        //type hospitalisation
        if ($res['type'] == 'hospitalisation') {
        }

        //verification si patient assure
        if ($request->type) {

            //mise à jour d'admission
            $res = $this->repository()->assure($id, $request);

            //cree une assurance
            $this->assurance()->store($res['data']);
        }

        return redirect()->route('dashboard')->with('success', 'Paiement validé avec succès');
    }


    public function indicate($day)
    {
        $targetDay = ($day == 'day' || empty($day)) ? date('Y-m-d') : $day;

        $payments = Payment::where('caissiere_id', Auth::user()->cashier->id)
            ->where('status', 'success')
            ->whereDate('created_at', $targetDay)
            ->with([
                'admission.patient.user',
                'admission.prestationHospital.prestationService',
                'hospitalisation.consultation.patient.user',
                'typeAssurance'
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        $cashier = Auth::user()->cashier;

        $pdf = Pdf::loadView('users.cashier.indicate', [
            'payments' => $payments,
            'cashier' => $cashier,
            'day' => $targetDay
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif'
        ]);

        return $pdf->stream('point-recette-' . $targetDay . '.pdf');
    }

    public function all()
    {
        $hospitalId = Auth::user()->cashier->hospital_id;
        $payments = Payment::where('status', 'pending')
            ->where('hospital_id', $hospitalId)
            ->with(['admission.patient.user', 'hospitalisation.consultation.patient.user'])
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('users.cashier.admission.all', compact('payments'));
    }
}
