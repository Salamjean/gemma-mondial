<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consultation extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function ($consultation) {
            if ($consultation->status == 1) {
                try {
                    \App\Models\PatientCall::where('consultation_id', $consultation->id)
                        ->where('status', '!=', 'completed')
                        ->update(['status' => 'completed']);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Erreur mise à jour status PatientCall : ' . $e->getMessage());
                }
            }
        });
    }

    public function examen() : HasOne
    {
        return $this->hasOne(BulletinExamen::class);
    }

    public function careRequested(): HasOne
    {
        return $this->hasOne(CareRequested::class);
    }

    public function rendezVous() : HasOne
    {
        return $this->hasOne(RendezVous::class);
    }

    public function ordonnances() : HasMany
    {
        return $this->hasMany(Ordonnance::class);
    }

    public function arret() : HasOne
    {
        return $this->hasOne(ArretTravail::class);
    }

    public function registre() : HasOne
    {
        return $this->hasOne(Registre::class);
    }

    public function declaration()  : HasOne
    {
        return $this->hasOne(Declaration::class);
    }

    public function hospitalisation()  : HasOne
    {
        return $this->hasOne(Hospitalisation::class);
    }
    public function observation()  : HasOne
    {
        return $this->hasOne(Observation::class);
    }

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function admission() : BelongsTo
    {
        return $this->belongsTo(Admission::class, 'admission_id', 'id');
    }

    public function doctor() : BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

    public function infirmier() : BelongsTo
    {
        return $this->belongsTo(Infirmier::class, 'infirmier_id', 'id');
    }

    public function hospital() : BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }

    public function IssueConsultation() : BelongsTo
    {
        return $this->belongsTo(IssueConsultation::class, 'issue_consultation_id', 'id');
    }

    public function prestationHospital() : BelongsTo
    {
        return $this->belongsTo(PrestationHospital::class, 'prestation_hospital_id', 'id');
    }

    public function ordonnance(): HasOne
    {
        return $this->hasOne(Ordonnance::class);
    }

    public function teleconsultationHospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'teleconsultation_hospital_id', 'id');
    }
}
