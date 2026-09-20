<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\CashierRequest;
use App\Http\Requests\Hospital\UpdateCashierRequest;
use App\Models\Availability;
use App\Models\Caisse;
use App\Models\Caissiere;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{

    public function index()
    {
        $cashiers = Caissiere::where('hospital_id', Auth::user()->hospital->id)->where('delete', 0)->with('user.availability')->get();;
        $title = "Liste des caissières";
        $empty = "Liste vide...";
        return view('users.hospital.cashier.index', ["title" => $title, 'cashiers' => $cashiers, 'empty' => $empty]);
    }

    public function show($id)
    {
        $caissiere = Caissiere::findOrFail($id);
        $title = "Détails | " .  $caissiere->user->name;

        return view('users.hospital.cashier.show', compact('caissiere', 'title'));

    }

    public function add()
    {
        $title = "Ajout de caissière";

        return view('users.hospital.cashier.add', compact( 'title'));
    }

    public function store(CashierRequest $request)
    {
        $request->validated();

        if (!count($request->day) && count($request->day) == 0)
            return back()->withErrors('Veuillez selectionner un jour svp .');

        $existing = Caissiere::where('matricule', 'CS-' . $request->matricule)->where('hospital_id', Auth::user()->hospital->id)->exists();

        if ($existing)
        {
            return back()->with('success','Cette Caissière existe déjà.');
        }

        //save user
        $user = new User();
        $user->name = $request -> name;
        $user->email = $request -> email;
        $user->role_as = 'cashier';
        $user->password = bcrypt($request -> password);
        $user->save();

        //save doctor
        $caissiere = new Caissiere();

        if($request -> hasFile('image'))
        {
            $caissiere->img_url = $this->uploadImage($request->image, 'cashier');
        }
        $caissiere -> user_id = $user->id;
        $caissiere -> hospital_id = Auth::user()->hospital->id;
        $caissiere -> matricule = 'CS' . $request -> matricule;
        $caissiere -> contact = $request -> contact;
        $caissiere -> address = $request -> address;
        $caissiere -> is_accueil = $request->has('is_accueil') ? 1 : 0;
        $caissiere -> save();

        //save availability
        $planning = new Availability();
        $planning->user_id = $user->id;
        $planning->days = json_encode($request->day);

        $planning->hour_start = json_encode($request->time);

        $planning->save();

        return redirect()->route('hospital.cashier.index')->with('success',"Cette Caissière a été ajouté avec succès.");

    }

    public function update($id, UpdateCashierRequest $request)
    {
        $request->validated();

        $caissiere = Caissiere::findOrFail($id);

        //user
        $user = User::findOrFail($caissiere->user_id);
        $user -> name = $request -> name;
        if ($request ->password)
        {
            $user -> password = bcrypt($request ->password);
        }
        $user -> save();

        $caissiere -> contact = $request -> contact;
        $caissiere -> address = $request -> address;
        $caissiere -> is_accueil = $request->has('is_accueil') ? 1 : 0;
        if($request -> hasFile('image'))
        {
            $caissiere->img_url = $this->deleteUploadImage($request->image, 'cashier');
        }
        $caissiere -> save();

        //update availability
        $planning = Availability::where('user_id', $user->id)->first();
        $planning->days = json_encode($request->day);

        $planning->hour_start = json_encode($request->time);

        $planning->save();


        return redirect()->route('hospital.cashier.index')->with('success', 'Ces informations ont été mises à jour.');
    }

    public function status($id)
    {
        $caissiere = Caissiere::findOrFail($id);

        $caissiere -> status = $caissiere -> status == 0 ? 1 : 0;

        $caissiere->save();

        return back()->with('success',"Statut a été modifié avec succès.");

    }

    public function delete($id)
    {

        $personal = Caissiere::findOrFail($id);

        $personal->delete = 1;
        $personal->save();

        return back()->with('success', "Caissier(e) supprimé(e) avec succès.");
    }

}
