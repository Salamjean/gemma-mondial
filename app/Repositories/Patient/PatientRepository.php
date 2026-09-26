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
        if (!$patient) {
            return ['status' => 'error', 'message' => 'Utilisateur introuvable.'];
        }

        // 1. Mise à jour de l'utilisateur associé
        $user = Auth::user();

        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('prenom')) {
            $user->prenom = $request->prenom;
        }
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        $user->save();

        // 2. Mise à jour des informations du patient
        if ($request->has('contact1') || $request->has('telephone')) {
            $patient->telephone = $request->contact1 ?: $request->telephone;
        }
        if ($request->has('contact2')) {
            $patient->contact2 = $request->contact2;
        }
        if ($request->has('residence_actuelle')) {
            $patient->residence_actuelle_id = $request->residence_actuelle;
        }
        if ($request->has('residence_habituelle')) {
            $patient->residence_habituelle_id = $request->residence_habituelle;
        }
        if ($request->has('situation_matrimoniale')) {
            $patient->situation_matrimoniale = $request->situation_matrimoniale;
        }
        if ($request->has('profession')) {
            $patient->profession = $request->profession;
        }
        if ($request->has('adresse') || $request->has('address')) {
            $patient->address = $request->adresse ?: $request->address;
        }
        if ($request->has('nom_persn_sos')) {
            $patient->nom_personne_cas_urgence = $request->nom_persn_sos;
        }
        if ($request->has('tel_persn_sos')) {
            $patient->telephone_personne_cas_urgence = $request->tel_persn_sos;
        }
        if ($request->has('lien_persn_sos')) {
            $patient->lien_personne_cas_urgence = $request->lien_persn_sos;
        }
        if ($request->has('nom_persn_sos2')) {
            $patient->nom_personne2_cas_urgence = $request->nom_persn_sos2;
        }
        if ($request->has('tel_persn_sos2')) {
            $patient->telephone_personne2_cas_urgence = $request->tel_persn_sos2;
        }
        if ($request->has('lien_persn_sos2')) {
            $patient->lien_personne2_cas_urgence = $request->lien_persn_sos2;
        }

        // 3. Gestion de la photo de profil (Fichier ou Base64)
        if ($request->hasFile('image') || $request->hasFile('photo') || $request->hasFile('img_url')) {
            $file = $request->file('image') ?: ($request->file('photo') ?: $request->file('img_url'));
            
            if ($file && $file->isValid()) {
                $uploadDirectory = public_path('assets/uploads/patient/');
                if (!file_exists($uploadDirectory)) {
                    mkdir($uploadDirectory, 0755, true);
                }

                // Supprimer l'ancienne image si elle existe
                if ($patient->img_url) {
                    $oldPath = $uploadDirectory . $patient->img_url;
                    if (file_exists($oldPath) && is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $extension = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = time() . '_' . uniqid() . '.' . $extension;
                $file->move($uploadDirectory, $filename);
                $patient->img_url = $filename;
            }
        } elseif ($request->filled('imagef') || (is_string($request->input('image')) && str_starts_with($request->input('image'), 'data:image'))) {
            // Support Image Base64
            $base64String = $request->input('imagef') ?: $request->input('image');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $data = substr($base64String, strpos($base64String, ',') + 1);
                $type = strtolower($type[1]);

                $data = base64_decode($data);
                if ($data !== false) {
                    $uploadDirectory = public_path('assets/uploads/patient/');
                    if (!file_exists($uploadDirectory)) {
                        mkdir($uploadDirectory, 0755, true);
                    }

                    if ($patient->img_url) {
                        $oldPath = $uploadDirectory . $patient->img_url;
                        if (file_exists($oldPath) && is_file($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    $filename = time() . '_' . uniqid() . '.' . $type;
                    file_put_contents($uploadDirectory . $filename, $data);
                    $patient->img_url = $filename;
                }
            }
        }

        $patient->save();

        // Recharger le patient avec ses relations pour retourner les données complètes
        $patient->load('user', 'habitualResidence', 'currentResidence', 'lieuNaissance', 'hospital');

        return [
            'status' => 'success',
            'message' => 'Vos données et votre photo de profil ont été modifiées avec succès.',
            'patient' => $patient,
            'photo_url' => $patient->img_url ? asset('assets/uploads/patient/' . $patient->img_url) : null
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
            ->with([
                'admission',
                'doctor.user',
                'hospital.user',
                'prestationHospital.prestationService.service',
                'ordonnance.prescriptions.drug',
                'ordonnance.prescriptions.drugHospital.drug',
                'ordonnances.prescriptions.drug',
                'ordonnances.prescriptions.drugHospital.drug',
                'examen.examens',
                'arret',
                'registre.registreConsultationCurative',
                'declaration.deces',
                'declaration.naissance',
                'declaration.decesPatient',
                'hospitalisation.daysHospitalisation.therapeutiqueProtocols'
            ])
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
