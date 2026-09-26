<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Declaration extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends = ['pdf_url'];

    public function getPdfUrlAttribute()
    {
        $type = ($this->naissance_id || $this->relationLoaded('naissance') && $this->naissance) ? 'birth' : 'death';
        return url('api/v1/patient/documents/' . $type . '/' . $this->id . '/pdf');
    }

    public function naissance(): HasOne
    {
        return $this->hasOne(DeclarationNaissance::class);
    }

    public function decesPatient(): HasOne
    {
        return $this->hasOne(DeclarationDeces::class)->where('type', 'patient');
    }

    public function deces(): HasOne
    {
        return $this->hasOne(DeclarationDeces::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'id');
    }

}
