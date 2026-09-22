<?php

namespace App\Models;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Admission;
use App\Models\Ordonnance;
use App\Models\Consultation;
use App\Models\SubPrefecture;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    public function birthPlace(): BelongsTo
    {
        return $this->belongsTo(SubPrefecture::class, 'lieu_de_naissance_id', 'id');
    }

    public function habitualResidence(): BelongsTo
    {
        return $this->belongsTo(SubPrefecture::class, 'residence_habituelle_id', 'id');
    }

    public function currentResidence(): BelongsTo
    {
        return $this->belongsTo(SubPrefecture::class, 'residence_actuelle_id', 'id');
    }

    //trouver la mere d'un enfant
    public function mere()
    {
        return $this->hasOne(Patient::class, 'id', 'mere_id');
    }

    //enfant a qui appartient une declaration de naissance
    public function enfant()
    {
        return $this->hasOne(DeclarationNaissance::class, 'enfant_id', 'id');
    }

    //trouver les enfants d'un patient
    public function enfantsPatient()
    {
        return $this->hasMany(Declaration::class, 'patient_id', 'id')->where('type', 'birth')->with('naissance');
    }

    // Patient déclaré décédé (relation)
    public function declarationDeces()
    {
        return $this->hasOne(Declaration::class, 'patient_id', 'id')->where('type', 'death')->with('deces');
    }

    public function deces()
    {
        return $this->hasMany(Declaration::class, 'patient_id', 'id')->where('type', 'death');
    }

    public function isDeceased(): bool
    {
        return ($this->status === 0 || $this->status === '0') || $this->declarationDeces()->exists();
    }

    public function passagePatients(): HasMany
    {
        return $this->hasMany(PassagePatient::class);
    }

    public function passage()
    {
        return $this->hasOne(PassagePatient::class);
    }

    public function secretariat()
    {
        return $this->belongsTo(Secretaire::class, 'secretaire_id', 'id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function examens(): HasMany
    {
        return $this->hasMany(Examen::class);
    }

    public function ordonnances(): HasMany
    {
        return $this->hasMany(Ordonnance::class);
    }

    public function antecedents()
    {
        return $this->hasOne(Antecedent::class);
    }

    public function assurances()
    {
        return $this->hasMany(Assurance::class);
    }

    public function lieuNaissance()
    {
        return $this->belongsTo(SubPrefecture::class, 'lieu_de_naissance_id', 'id');
    }
    public function residenceActuelle()
    {
        return $this->belongsTo(SubPrefecture::class, 'residence_actuelle_id', 'id');
    }
    public function residenceHabituelle()
    {
        return $this->belongsTo(SubPrefecture::class, 'residence_habituelle_id', 'id');
    }

    public function getFingerprintStatusAttribute()
    {
        if ($this->fingerprint_left_index && $this->fingerprint_right_index) {
            return 'Complet';
        } elseif ($this->fingerprint_left_index || $this->fingerprint_right_index) {
            return 'Partiel';
        }
        return 'Non enregistré';
    }

    public function hasFingerprints()
    {
        return !empty($this->fingerprint_left_index) && !empty($this->fingerprint_right_index);
    }
}

