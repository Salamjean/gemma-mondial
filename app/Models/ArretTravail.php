<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArretTravail extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends = ['pdf_url'];

    public function getPdfUrlAttribute()
    {
        return url('api/v1/patient/documents/arret/' . $this->id . '/pdf');
    }
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id', 'id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id');
    }

    public function examen(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'examen_id', 'id');
    }
}
