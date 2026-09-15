<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patient\PatientRequest;
use App\Models\ArretTravail;
use App\Models\BulletinExamen;
use App\Models\Declaration;
use App\Models\Ordonnance;
use App\Repositories\Patient\PatientRepository;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PatientController extends Controller
{
    public function instance()
    {
        return new PatientRepository();
    }

    public function setting()
    {
        return view('users.patient.profile');
    }

    public function update(PatientRequest $request)
    {
        $request->validated();


        $res = $this->instance()->update($request);

        if ($res['status'] == 'error')
            return response()->json(['status' => $res['status'], 'message' => $res['message']], 400);

        return response()->json(['status' => $res['status'], 'message' => $res['message']], 200);
    }

    public function consultations()
    {
        return view('users.patient.consultations', ['consultations' => $this->instance()->consultations()]);
    }

    public function declarations()
    {
        return view('users.patient.declarations', ['declarations' => $this->instance()->declarations()]);
    }

    public function rendezVous()
    {
        return view('users.patient.rendez-vous', ['rendez' => $this->instance()->rendezVous()]);
    }

    public function Impression($post, $id)
    {
        set_time_limit(120);
        if ($post == 'ordonnance')
        $pdf =  Pdf::loadView('users.doctor.consultation.formulaire.post-consultation.pdf.ordonnance', ['ordonnance' => Ordonnance::findOrFail($id)]);
        elseif ($post == 'examen')
        $pdf =  Pdf::loadView('users.doctor.consultation.formulaire.post-consultation.pdf.examen', ['bulletin' => BulletinExamen::findOrFail($id)]);
        elseif ($post == 'arret')
        $pdf =  Pdf::loadView('users.doctor.consultation.formulaire.post-consultation.pdf.arret', ['arret' => ArretTravail::findOrFail($id)]);
        elseif ($post == 'death')
        $pdf =  Pdf::loadView('users.doctor.declaration.pdf.deces', ['declaration' => Declaration::findOrFail($id)]);
        elseif ($post == 'birth')
        $pdf =  Pdf::loadView('users.doctor.declaration.pdf.naissance', ['declaration' => Declaration::findOrFail($id)]);
        else
            return redirect()->back();

        $pdf->setPaper('A4', 'portrait')->render();

        $response = new Response();
        $response->setContent($pdf->output())->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', "inline; filename=$post$id-" . date('dmY') . ".pdf");

        return $response;
    }
}
