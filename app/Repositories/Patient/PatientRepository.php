<?php

namespace App\Repositories\Patient;

use App\Http\Requests\Patient\PatientRequest;
use App\Models\Consultation;
use App\Models\Declaration;
use App\Models\Patient;
use App\Models\RendezVous;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Database\Eloquent\Builder;

class PatientRepository
{

    public function __construct()
    {
        //
    }

    public function update(PatientRequest $request)
    {

        $request->validated();

        $patient = Patient::find(Auth::user()->patient->id);
        if (!$patient)
            return ['status' => 'error', 'message' => 'Utilisateur introuvable.'];

        //user
        $user = Auth::user();

        if ($request->password)
            $user->password = bcrypt($request->password);

        if ($request->email)
            $user->email = $request->email;

        $user->save();

        //update patient
        $patient->telephone = $request->contact1;
        $patient->contact2 = $request->contact2 ?? null;
        $patient->residence_actuelle_id = $request->residence_actuelle;
        $patient->residence_habituelle_id = $request->residence_habituelle;
        $patient->situation_matrimoniale = $request->situation_matrimoniale;
        $patient->profession = $request->profession;
        $patient->address = $request->adresse;
        $patient->nom_personne_cas_urgence = $request->nom_persn_sos;
        $patient->telephone_personne_cas_urgence = $request->tel_persn_sos;
        $patient->lien_personne_cas_urgence = $request->lien_persn_sos;
        $patient->nom_personne2_cas_urgence = $request->nom_persn_sos2;
        $patient->telephone_personne2_cas_urgence = $request->tel_persn_sos2;
        $patient->lien_personne2_cas_urgence = $request->lien_persn_sos2;

        if ($request->hasFile('image')) {
            $patient->img_url = deleteUploadImage($request->image, 'patient');
        }
        if ($request->imagef) {
            $patient->img_url = saveImage($request->imagef, 'patient');
        }
        $patient->save();

        // Recharger le patient avec ses relations pour retourner les données complètes
        $patient->load('user', 'habitualResidence', 'currentResidence', 'lieuNaissance', 'hospital');

        return [
            'status' => 'success',
            'message' => 'Vos données ont été modifié avec succès.',
            'patient' => $patient  // Retourner les données du patient mises à jour
        ];
    }

    public function consultations()
    {
        return Consultation::orderByDESC('created_at')
            ->where('patient_id', Auth::user()->patient->id)
            ->where(function ($query) {
                $query->where('status', 1)
                      ->orWhereIn('call_status', ['pending', 'payment_pending', 'calling', 'in_progress', 'completed', 'doctor_ended', 'ended'])
                      ->orWhereNotNull('issue_consultation_id')
                      ->orWhereHas('registre');
            })
            ->with('admission', 'doctor.user', 'hospital.user', 'ordonnance.prescriptions', 'examen.examens', 'arret', 'registre', 'declaration.deces', 'declaration.naissance', 'declaration.decesPatient')
            ->with('hospitalisation.daysHospitalisation.therapeutiqueProtocols')
            ->get();
    }

    public function declarations()
    {
        return Declaration::orderByDESC('created_at')->where('patient_id', Auth::user()->patient->id)->with('doctor.user', 'hospital.user', 'deces', 'naissance', 'consultation.registre')->get();
    }

    public function rendezVous()
    {
        $patient = Auth::user()->patient->id;
        return RendezVous::orderByDESC('created_at')->whereHas(
            'consultation',
            function (Builder $query) use ($patient) {
                $query->where('patient_id', $patient);
            }
        )->with('consultation', function ($query) {
            $query->with('doctor.user')->with('hospital')->get();
        })
            ->get();
    }

}
