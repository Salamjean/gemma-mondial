<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Doctor\DeclarationDecesRequest;
use App\Http\Requests\Doctor\DeclarationNaissanceRequest;
use App\Models\Declaration;
use App\Models\Patient;
use App\Repositories\Doctor\DeclarationRepository;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;


class DeclarationController extends Controller
{
    public function instance()
    {
        return new DeclarationRepository();
    }

    public function searchPatient(Request $request)
    {
        switch ($request->searchBy) {
            case 'reference':
                $patient = Patient::with('user')
                    ->where('code_patient', 'like', '%' . $request->search . '%')->take(20)->get();
                if ($patient->count() > 0)
                    return response()->json(['patients' => $patient, 'status' => 'success'], 200);
                else
                    return response()->json(['patients' => $patient, 'status' => 'error'], 200);

            case 'date':

                $patient = Patient::with('user')
                    ->where('birth_date', date('d/m/Y', strtotime($request->search)))->take(20)->get();

                if ($patient->count() > 0)
                    return response()->json(['patients' => $patient, 'status' => 'success'], 200);
                else
                    return response()->json(['patients' => $patient, 'status' => 'error'], 200);

            case 'name':
                // dd(explode(" ", $request->search, 2));
                $name = explode(" ", $request->search, 2);
                if (count($name) == 2) {
                    $nom = $name[0];
                    $pnom = $name[1];
                    $patient = Patient::with('user')
                        ->whereHas('user', function (Builder $query) use ($nom, $pnom) {
                            $query->where('name', 'like', '%' . $nom . '%')->where('prenom', 'like', '%' . $pnom . '%');
                        })
                        ->take(20)->get();
                } else {
                    $nom = $name[0];
                    $patient = Patient::with('user')
                        ->whereHas('user', function (Builder $query) use ($nom) {
                            $query->where('name', 'like', '%' . $nom . '%');
                        })
                        ->take(20)->get();
                }

                if ($patient->count() > 0)
                    return response()->json(['patients' => $patient, 'status' => 'success'], 200);
                else
                    return response()->json(['patients' => $patient, 'status' => 'error'], 200);

            default:
                return response()->json(['status' => 'error'], 200);
        }
    }

    //liste de deces
    public function listDeces(Request $request)
    {
        $search = $request->get('search');
        $declarations = $this->instance()->indexDeces($search);
        return view('users.doctor.declaration.deces.list', compact('declarations', 'search'));
    }

    public function directDeces()
    {
        $hospitalId = auth()->user()->doctor->hospital_id ?? auth()->user()->hospital_id;
        $hospital = optional(auth()->user()->doctor)->hospital ?? \App\Models\Hospital::find($hospitalId);
        return view('users.doctor.declaration.deces.direct', compact('hospital'));
    }

    public function storeDirectDeces(Request $request)
    {
        $res = $this->instance()->storeDirectDeces($request);

        if ($res['status'] === 'error') {
            return redirect()->back()->withInput()->with('error', $res['message']);
        }

        return redirect()->route('doctor.declaration.deces.list')
            ->with('success', $res['message'])
            ->with('latest_declaration_id', $res['declaration_id'] ?? null);
    }

    public function showDeces($id)
    {
        return view('users.doctor.declaration.deces.show', ['declaration' => $this->instance()->showDeces($id)]);
    }

    public function addDeces($person)
    {
        return view('users.doctor.declaration.deces.add', ['person' => $person]);
    }

    public function storeDeces(DeclarationDecesRequest $request)
    {
        // dd($request->all());
        $res = $this->instance()->storeDeces($request);

        if ($res['status'] == 'error')
            return redirect()->back()->withErrors($res['message']);

        return redirect()->route('doctor.declaration.deces.list')->with('success', $res['message']);
    }


    //liste de naissance
    public function listNaissance()
    {
        return view('users.doctor.declaration.naissance.list', ['declarations' => $this->instance()->indexNaissance()]);
    }

    public function showNaissance($id)
    {
        return view('users.doctor.declaration.naissance.show', ['declaration' => $this->instance()->showNaissance($id)]);
    }

    public function addNaissance()
    {
        return view('users.doctor.declaration.naissance.add');
    }

    public function storeNaissance(DeclarationNaissanceRequest $request)
    {
        $res = $this->instance()->storeNaissance($request);

        if ($res['status'] == 'error')
            return redirect()->back()->withErrors($res['message']);


        return redirect()->route('doctor.declaration.naissance.list')->with('success', $res['message']);
    }

    //doc pdf
    public function certificatNaissance($id)
    {
        $declaration = Declaration::findOrFail($id);


        $pdf =  Pdf::loadView('users.doctor.declaration.pdf.naissance', ['declaration' => $declaration]);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $response = new Response();
        $response->setContent($pdf->output());
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'inline; filename="certificat_medical_naissance.pdf"');

        return $response;
    }

    public function certificatDeces($id)
    {
        $declaration = Declaration::findOrFail($id);


        $pdf =  Pdf::loadView('users.doctor.declaration.pdf.deces', ['declaration' => $declaration]);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $response = new Response();
        $response->setContent($pdf->output());
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'inline; filename="certificat_medical_deces.pdf"');

        return $response;
    }
}
