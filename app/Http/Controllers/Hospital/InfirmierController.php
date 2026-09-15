<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\InfirmierRequest;
use App\Http\Requests\Hospital\UpdateInfirmierRequest;
use App\Models\Availability;
use App\Models\Departement;
use App\Models\Infirmier;
use App\Models\ServiceHospital;
use App\Models\User;
use App\Repositories\Hospital\AgentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfirmierController extends Controller
{

    public function repository()
    {
        return new AgentRepository;
    }

    public function index()
    {
        $infirmiers = Infirmier::where('hospital_id', Auth::user()->hospital->id)
            ->where('delete', 0)
            ->with(['user.availability', 'services.serviceHospital.service', 'serviceHospital.service'])
            ->get();

        $title = "Liste des infirmiers";
        $empty = "Liste vide...";
        return view('users.hospital.infirmier.index', ["title" => $title, 'infirmiers' => $infirmiers, 'empty' => $empty]);
    }

    public function show($id)
    {
        $infirmier = Infirmier::findOrFail($id);
        $service = ServiceHospital::where('hospital_id', Auth::user()->hospital->id)->get();
        $title = "Edition | " .  $infirmier->user->name;

        return view('users.hospital.infirmier.show', compact('infirmier', 'service', 'title'));
    }

    public function add()
    {
        $title = "Ajout d'infirmier";
        $service = ServiceHospital::where('hospital_id', Auth::user()->hospital->id)->get();

        return view('users.hospital.infirmier.add', compact( 'title', 'service'));
    }

    public function store(InfirmierRequest $request)
    {

        $request->validated();

        if (!count($request->day) && count($request->day) == 0)
            return back()->withErrors('Veuillez selectionner un jour svp .');

        $res = $this->repository()->storeInfirmier($request);

        if ($res['status'] == 'success')
            return redirect()->route('hospital.infirmier.index')->with('success', $res['message']);
    }


    public function update($id, UpdateInfirmierRequest $request)
    {
        $request->validated();

        $infirmier = Infirmier::findOrFail($id);

        //user
        $user = User::findOrFail($infirmier->user_id);
        $user->name = $request->name;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        //update
        $infirmier->contact = $request->contact;
        $infirmier->address = $request->address;
        if ($request->hasFile('image')) {
            $infirmier->img_url = $this->deleteUploadImage($request->image, 'infirmier');
        }

        $serviceIds = [];
        if ($request->has('services') && is_array($request->services)) {
            $serviceIds = array_filter($request->services);
        } elseif ($request->filled('service')) {
            $serviceIds = [$request->service];
        }

        $infirmier->service_hospital_id = !empty($serviceIds) ? $serviceIds[0] : null;
        $infirmier->save();

        \App\Models\ServiceInfirmier::where('infirmier_id', $infirmier->id)->delete();
        foreach ($serviceIds as $srvId) {
            \App\Models\ServiceInfirmier::create([
                'infirmier_id' => $infirmier->id,
                'service_hospital_id' => $srvId,
            ]);
        }

        //update availability
        $planning = Availability::where('user_id', $user->id)->first();
        $planning->days = json_encode($request->day);

        $planning->hour_start = json_encode($request->time);

        $planning->save();


        return redirect()->route('hospital.infirmier.index')->with('success', 'Ces informations ont été mises à jour.');
    }

    public function status($id)
    {
        $doctor = Infirmier::findOrFail($id);

        $doctor->status = $doctor->status == 0 ? 1 : 0;

        $doctor->save();

        return back()->with('success', "Status modifié avec succès.");
    }

    public function delete($id)
    {

        $personal = Infirmier::findOrFail($id);

        $personal->delete = 1;
        $personal->save();

        return back()->with('success', "Infirmier supprimé(e) avec succès.");
    }
}
