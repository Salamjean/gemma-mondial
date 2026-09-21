<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hospital extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_teleconsultation_active' => 'boolean',
    ];

    public function hasTeleconsultation(): bool
    {
        return (bool) ($this->is_teleconsultation_active ?? true);
    }

    public function getOrGenerateTvToken(): string
    {
        if (empty($this->tv_token)) {
            $this->tv_token = \Illuminate\Support\Str::random(32);
            $this->saveQuietly();
        }
        return $this->tv_token;
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function localiteH(): BelongsTo
    {
        return $this->belongsTo(SubPrefecture::class, 'localite', 'id');
    }

    public function doctors() : HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function drugHospitals(): HasMany
    {
        return $this->hasMany(DrugHospital::class);
    }

    public function secretariats() : HasMany
    {
        return $this->hasMany(Secretaire::class);
    }

    public function caishiers(): HasMany
    {
        return $this->hasMany(Caissiere::class);
    }

    public function passagePatients(): HasMany
    {
        return $this->hasMany(PassagePatient::class);
    }

    public function patientCalls(): HasMany
    {
        return $this->hasMany(PatientCall::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function departements(): HasMany
    {
        return $this->hasMany(Departement::class);
    }

    public function typeDoctors(): HasMany
    {
        return $this->hasMany(TypeDoctor::class);
    }

    public function declarations(): HasMany
    {
        return $this->hasMany(Declaration::class);
    }

    public function declarationMaternelles(): HasMany
    {
        return $this->hasMany(DeclarationMaternelle::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function typeConsultations(): HasMany
    {
        return $this->hasMany(TypeConsultation::class);
    }

    public function typeAssurances(): HasMany
    {
        return $this->hasMany(TypeAssurance::class);
    }

    public function assurances(): HasMany
    {
        return $this->hasMany(Assurance::class);
    }

    public function factures(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function bedrooms(): HasMany
    {
        return $this->hasMany(Bedroom::class);
    }

}
