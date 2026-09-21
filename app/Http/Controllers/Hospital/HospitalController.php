<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\UpdateHospitalRequest;
use App\Models\Hospital;
use App\Models\ServiceHospital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HospitalController extends Controller
{
    public function index()
    {
        $title = 'Paramètres & Profil de l\'Hôpital';
        $hospital = Hospital::with(['user', 'localiteH'])->findOrFail(Auth::user()->hospital->id);

        return view('users.hospital.profile', compact('title', 'hospital'));
    }

    public function update(UpdateHospitalRequest $request)
    {
        $request->validated();

        $hospital = Hospital::findOrFail(Auth::user()->hospital->id);
        $user = Auth::user();

        // Mot de passe
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        // Nom de l'établissement
        if ($request->filled('label')) {
            $hospital->label = $request->label;
            $user->name = $request->label;
            $user->save();
        }

        // Contact téléphonique
        if ($request->has('contact')) {
            $hospital->contact = $request->contact;
        }

        // District Sanitaire
        if ($request->has('district_sanitaire')) {
            $hospital->district_sanitaire = $request->district_sanitaire;
        }

        // Direction Générale
        if ($request->has('nom_direction_generale')) {
            $hospital->nom_direction_generale = $request->nom_direction_generale;
        }

        // Logo officiel
        if ($request->hasFile('image')) {
            $hospital->img_url = $this->deleteUploadImage($request->file('image'), 'hospital');
        }

        // Image de filigrane (Watermark pour documents à imprimer)
        if ($request->hasFile('watermark')) {
            $hospital->watermark_url = $this->deleteUploadImage($request->file('watermark'), 'hospital');
        }

        $hospital->save();

        return back()->with('success', "Informations et paramètres de l'hôpital mis à jour avec succès.");
    }

    public function deleteWatermark()
    {
        $hospital = Hospital::findOrFail(Auth::user()->hospital->id);
        if ($hospital->watermark_url) {
            $this->deleteImage($hospital->watermark_url, 'hospital');
            $hospital->watermark_url = null;
            $hospital->save();
        }
        return back()->with('success', "Image de filigrane supprimée avec succès.");
    }

    public function grille()
    {
        $service = ServiceHospital::where('hospital_id', Auth::user()->hospital->id)->get();
        return 'ok';
    }
}
