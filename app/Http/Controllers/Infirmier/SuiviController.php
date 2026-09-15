<?php

namespace App\Http\Controllers\Infirmier;

use Carbon\Carbon;
use App\Models\Surveillance;
use Illuminate\Http\Request;
use App\Models\Hospitalisation;
use App\Models\DayHospitalisation;
use App\Http\Controllers\Controller;
use App\Models\ProtocolHourApplication;
use Illuminate\Support\Facades\Validator;

class SuiviController extends Controller
{
    public function suiviePatient($id){
        $hospitalisation = Hospitalisation::findOrFail($id);
        $day_hospitalisation = DayHospitalisation::where('hospitalisation_id', $hospitalisation->id)->first();
        $surveillances = Surveillance::where('day_hospitalisation_id', $day_hospitalisation->id)->get();
        return view('users.infirmier.suivi.hospitalisation', compact('hospitalisation', 'day_hospitalisation', 'surveillances'));
    }
    public function appliqueProtocol($id)
    {
        $hour = ProtocolHourApplication::findOrFail($id);
        $day = DayHospitalisation::whereHas('therapeutiqueProtocols.protocolHourApplications', function ($q) use ($id) {
            $q->where('id', $id);
        })->first();

        if ($day && $day->hospitalisation && $day->hospitalisation->status === 'finished') {
            return response()->json(['message' => 'L\'hospitalisation est terminée. Impossible de modifier le protocole.'], 403);
        }

        $hour->update([
            'status' => 'appliqué',
            'hour_applique'=> Carbon::now(),
        ]);
        return response()->json(['message' => 'Protocole appliqué avec succès!']);
    }
    public function makeSuveillance(Request $request)
    {
        $day = DayHospitalisation::find($request->day_hospitalisation_id);
        if ($day && $day->hospitalisation && $day->hospitalisation->status === 'finished') {
            return back()->with('error', 'L\'hospitalisation est terminée. Impossible d\'ajouter de surveillance.');
        }

        $rules = [
            'ta' => 'required',
            'temperature' => 'required',
            'pouls' => 'required',
            'dierese' => 'required',
            'conscience' => 'required',
            'glycemie' => 'required',
            'sao2' => 'required',
            'poids' => 'required',
        ];
        $messages = [
            'ta.required' => 'Le TA est obligatoire!',
            'temperature.required' => 'La température est obligatoire',
            'pouls.required' => 'Le pouls est obligatoire!',
            'dierese.required' => 'Le diérèse est obligatoire!',
            'conscience.required' => 'La conscience est obligatoire',
            'glycemie.required' => 'Glycemie est obligatoire!',
            'sao2.required' => 'SaO2 est obligatoire!',
            'poids.required' => 'Le poids est obligatoire!',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            return redirect()->back()->withErrors($errorMessages)->withInput();
        }
        $surveillance = Surveillance::create([
            'day_hospitalisation_id' => $request->day_hospitalisation_id,
            'hour' => $request->hour,
            'ta' => $request->ta,
            't' => $request->temperature,
            'pouls' => $request->pouls,
            'diurese' => $request->dierese,
            'conscience' => $request->conscience,
            'glycemie' => $request->glycemie,
            'sao2' => $request->sao2,
            'poids' => $request->poids,
            'evolution' => $request->evolution,
            'conduite_tenir' => $request->conduite_tenir,
        ]);
        return back()->with('success', 'Donnée de surveillance enregistré avec succès');
    }
}
