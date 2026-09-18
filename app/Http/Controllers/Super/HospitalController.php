<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Http\Requests\Super\HospitalRequest;
use App\Http\Requests\Super\UpdateHospitalRequest;
use App\Models\Admission;
use App\Models\Consultation;
use App\Models\Declaration;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\ServiceHospital;
use App\Models\SubPrefecture;
use App\Models\TypeConsultation;
use App\Models\User;
use App\Notifications\HospitalCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HospitalController extends Controller
{
    public function index()
    {
        $hospitals = Hospital::all();;
        $title = "Liste des hopitaux";
        $empty = "Liste vide...";
        return view('users.super.hospital.index', ["title" => $title, 'hospitals' => $hospitals, 'empty' => $empty]);
    }

    public function show($id)
    {
        $hospital = Hospital::findOrFail($id);
        $title = "Detail sur l'hopital " . $hospital->name;
        $city = SubPrefecture::all();


        return view('users.super.hospital.show', ["title" => $title, 'hospital' => $hospital, 'cities' => $city]);

    }

    public function add()
    {
        $title = "Ajout d'hopital";
        $city = SubPrefecture::all();
        return view('users.super.hospital.add', compact('title', 'city'));
    }

    public function store(HospitalRequest $request)
    {
        $request->validated();

        $existing = Hospital::where('reference', $request->reference)->exists();

        if ($existing)
        {
            return back()->with('success','Hopital existant dans vos données renseigné.');
        }

        //save user
        $user = new User();
        $user->name = $request -> name;
        $user->prenom = $request -> prenom;
        $user->email = $request -> email;
        $user->role_as = 'hospital';
        $user->password = bcrypt($request -> password);
        $user->save();

        //save hospital
        $hospital = new Hospital();

        if($request -> hasFile('image'))
        {
            $hospital->img_url = $this->uploadImage($request->image, 'hospital');
        }
        $hospital -> user_id = $user->id;
        $hospital -> reference =  $request -> reference;
        $hospital -> contact = $request -> contact;
        $hospital -> localite = $request -> address;
        $hospital -> label = $request -> label;
        $hospital -> district_sanitaire = $request -> district;
        $hospital -> nom_direction_generale = $request -> direction_generale ?? null;
        $hospital -> is_teleconsultation_active = $request->has('is_teleconsultation_active') ? (bool)$request->is_teleconsultation_active : false;
        $hospital -> save();

        // Envoyer l'email de confirmation
        try {
            $user->notify(new HospitalCreatedNotification($hospital, $user, $request->password));
        } catch (\Exception $e) {
            // Vous pouvez logger l'erreur ici si nécessaire
            Log::error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }

        return redirect()->route('super.hospital.index')->with('success', 'Hopital ajouté avec succès.');
    }

    public function update(UpdateHospitalRequest $request, $id)
    {
        $request->validated();

        $hospital = Hospital::findOrFail($id);

        //user
        $user = Auth::user();
        if ($request ->password)
        {
            $user -> password = bcrypt($request ->password);
        }
        $user -> save();

        //update hospital
        $hospital -> label = $request -> label;
        $hospital -> localite = $request -> address;
        $hospital -> contact = $request -> contact;
        $hospital -> district_sanitaire = $request -> district;
        $hospital -> nom_direction_generale = $request -> direction_generale ?? $hospital -> nom_direction_general;
        $hospital -> is_teleconsultation_active = $request->has('is_teleconsultation_active');

        if($request -> hasFile('image'))
        {
            $hospital->img_url = $this->deleteUploadImage($request->image, 'hospital');
        }
        $hospital -> save();

        return redirect()->route('super.hospital.index')->with('success', "Hôpital modifié avec succès.");
    }

    public function status($id)
    {
        $hospital = Hospital::findOrFail($id);

        $hospital -> status = $hospital -> status == 0 ? 1 : 0;

        $hospital->save();

        return back()->with('success',"Status modifié avec succès.");
    }

    public function toggleTeleconsultation($id)
    {
        $hospital = Hospital::findOrFail($id);
        $hospital->is_teleconsultation_active = !$hospital->is_teleconsultation_active;
        $hospital->save();

        $statusLabel = $hospital->is_teleconsultation_active ? 'activée' : 'désactivée';
        return back()->with('success', "Téléconsultation {$statusLabel} avec succès pour {$hospital->label}.");
    }

    public function HospReport($id)
    {
        $hospital = Hospital::findOrFail($id);
        $title = 'Tableau de bord | '. $hospital->label;
        $consultation = Consultation::where('hospital_id', $id)->get();
        $totalMontant = $consultation->sum('montant');
        $totalConsult = $consultation->count();

        $declaration = Declaration::where('hospital_id', $id)->get();
        $totalDN = $declaration->where('type','birth')->count();
        $totalDD = $declaration->where('type','death')->count();

        $totalPatient = Patient::where('hospital_id', $id)->count();
        $totalDoctor = Doctor::where('hospital_id', $id)->count();
        $totalAdmAttente = Admission::where('hospital_id', $id)->where('statut_paiement', '==',0)->count();
        $totalAdmission = Admission::where('hospital_id', $id)->count();

        $services = ServiceHospital::where('hospital_id', $id)->with('prestationHospitals')->get();

        return view('users.super.hospital.hospDash', [

            'title' => $title,
            'hospital' => $hospital,
            'consultation' => $consultation,
            'totalMontant' => $totalMontant,
            'totalConsult' => $totalConsult,
            'totalDN' => $totalDN,
            'totalDD' => $totalDD,
            'totalPatient' => $totalPatient,
            'totalDoctor' => $totalDoctor,
            'totalAdmAttente' => $totalAdmAttente,
            'totalAdmission' => $totalAdmission,
            'services' => $services
        ]);
    }
    public function statusSce($id)
    {

        $service = ServiceHospital::findOrFail($id);

        $service->status = $service->status == 0 ? 1 : 0;

        $service->save();

        return back()->with('success', "Statut modifié avec succès.");
    }
}
