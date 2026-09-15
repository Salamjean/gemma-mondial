<?php

namespace App\Repositories\Hospital;

use App\Http\Requests\Hospital\DoctorRequest;
use App\Http\Requests\Hospital\InfirmierRequest;
use App\Http\Requests\SageFRequest;
use App\Http\Requests\Hospital\UpdateDoctorRequest;
use App\Models\Availability;
use App\Models\Doctor;
use App\Models\Infirmier;
use App\Models\PrestationDoctor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AgentRepository
{
    public function __construct()
    {
        //
    }

    public function storeDoctor(DoctorRequest $request)
    {
        $request->validated();

        $existingS = Doctor::where('matricule', 'MS' . $request->matricule)->where('hospital_id', Auth::user()->hospital->id)->exists();

        if ($existingS) {
            return back()->with('success', 'Medecin specialisté existant dans vos données renseignée.');
        }

        $existing = Doctor::where('matricule', 'MG' . $request->matricule)->where('hospital_id', Auth::user()->hospital->id)->exists();

        if ($existing) {
            return back()->with('success', 'Medecin géneraliste existant dans vos données renseignées.');
        }

        //save user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_as = 'doctor';
        $user->password = bcrypt($request->password);
        $user->save();

        //save doctor
        $doctor = new Doctor();
        if ($request->hasFile('image')) {
            $doctor->img_url = uploadImage($request->image, 'doctor');
        }
        $doctor->user_id = $user->id;
        $doctor->hospital_id = Auth::user()->hospital->id;
        $doctor->type_name = $request->type_name;
        $doctor->type_agent_id = 1;
        $doctor->service_hospital_id = $request->service;
        if ($request->type_name == 'specialiste') {
            $doctor->matricule = 'MS' . $request->matricule;
            if ($request->gyneco == '1') {
                $doctor->gyneco = $request->gyneco;
            } else {
                $doctor->type_doctor_id = $request->type_doctor;
                $doctor->gyneco = '0';
            }
        } else {
            $doctor->matricule = 'MS' . $request->matricule;
        }
        $doctor->contact = $request->contact;
        $doctor->address = $request->address;
        $doctor->save();

        //save service
        foreach ($request->pservice as $key => $value) {
            $service = new PrestationDoctor();
            $service->doctor_id = $doctor->id;
            $service->prestation_hospital_id = $value;
            $service->save();
        }

        //save availability
        $planning = new Availability();
        $planning->user_id = $user->id;
        $planning->days = json_encode($request->day);

        $planning->hour_start = json_encode($request->time);

        $planning->save();

        return ['status' => 'success', 'message' => 'Medecin ajouté avec succès.'];
    }



    public function storeSageF(SageFRequest $request)
    {
        $request->validated();

        $existing = Doctor::where('matricule', 'SG' . $request->matricule)->where('hospital_id', Auth::user()->hospital->id)->exists();

        if ($existing) {
            return back()->with('success', 'Sage femme existant dans vos données renseignée.');
        }

        //save user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_as = 'doctor';
        $user->password = bcrypt($request->password);
        $user->save();

        //save g=sage
        $sageF = new Doctor();
        if ($request->hasFile('image')) {
            $sageF->img_url = uploadImage($request->image, 'doctor');
        }
        $sageF->user_id = $user->id;
        $sageF->hospital_id = Auth::user()->hospital->id;
        $sageF->type_agent_id = 2;
        $sageF->gyneco = 1;
        $sageF->service_hospital_id = $request->service;
        $sageF->matricule = 'SG' . $request->matricule;
        $sageF->contact = $request->contact;
        $sageF->address = $request->address;
        $sageF->save();

        //save service
        foreach ($request->pservice as $key => $value) {
            $service = new PrestationDoctor();
            $service->doctor_id = $sageF->id;
            $service->prestation_hospital_id = $value;
            $service->save();
        }

        //save availability
        $planning = new Availability();
        $planning->user_id = $user->id;
        $planning->days = json_encode($request->day);

        $planning->hour_start = json_encode($request->time);

        $planning->save();

        return ['status' => 'success', 'message' => 'Sage femme ajoutée avec succès.'];
    }

    public function update(UpdateDoctorRequest $request, $id, $type)
    {

        $doctor = Doctor::findOrFail($id);

        //user
        $user = User::findOrFail($doctor->user_id);
        $user->name = $request->name;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        //update doctor
        $doctor->contact = $request->contact;
        $doctor->address = $request->address;

        /*verify
         * gyneco && specialiste
         * || specialiste
         * || other
         */
        if ($type == 'specialiste' && $doctor->gyneco == '1')
            $doctor->type_doctor_id = null;
        elseif ($type == 'specialiste')
            $doctor->type_doctor_id = $request->type_doctor;
        else
            $doctor->type_doctor_id = null;

        if ($request->hasFile('image'))
            $doctor->img_url = deleteUploadImage($request->image, 'doctor');

        $doctor->service_hospital_id = $request->service;
        $doctor->save();

        //update pservice
        $doctor->PrestationDoctors()->delete();

        foreach ($request->pservice as $key => $value) {
            $service = new PrestationDoctor();
            $service->doctor_id = $doctor->id;
            $service->prestation_hospital_id = $value;
            $service->save();
        }

        //update availability
        $planning = Availability::where('user_id', $user->id)->first();
        $planning->days = json_encode($request->day);

        $planning->hour_start = json_encode($request->time);

        $planning->save();

        return ['status' => 'success', 'message' => 'Données modifiées avec succès.', 'agent' => $doctor->type_agent_id];
    }

    public function storeInfirmier(InfirmierRequest $request)
    {
        $request->validated();

        $existing = Infirmier::where('matricule', 'INF' . $request->matricule)->where('hospital_id', Auth::user()->hospital->id)->exists();

        if ($existing) {
            return back()->with('success', 'Infirmier(e) existant dans vos données renseignée.');
        }

        //save user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_as = 'infirmier';
        $user->password = bcrypt($request->password);
        $user->save();

        //save infirmier
        $infirmier = new Infirmier();
        if ($request->hasFile('image'))
            $infirmier->img_url = uploadImage($request->image, 'infirmier');
        $infirmier->user_id = $user->id;
        $infirmier->hospital_id = Auth::user()->hospital->id;

        $serviceIds = [];
        if ($request->has('services') && is_array($request->services)) {
            $serviceIds = array_filter($request->services);
        } elseif ($request->filled('service')) {
            $serviceIds = [$request->service];
        }

        $infirmier->service_hospital_id = !empty($serviceIds) ? $serviceIds[0] : null;
        $infirmier->matricule = 'INF' . $request->matricule;
        $infirmier->contact = $request->contact;
        $infirmier->address = $request->address;
        $infirmier->save();

        foreach ($serviceIds as $srvId) {
            \App\Models\ServiceInfirmier::create([
                'infirmier_id' => $infirmier->id,
                'service_hospital_id' => $srvId,
            ]);
        }

        //save availability
        $planning = new Availability();
        $planning->user_id = $user->id;

        //save availability
        $planning = new Availability();
        $planning->user_id = $user->id;
        $planning->days = json_encode($request->day);

        $planning->hour_start = json_encode($request->time);


        $planning->save();

        return ['status' => 'success', 'message' => 'Infirmier(e) ajouté(e) avec succès.'];
    }
}
