<?php

namespace App\Models;

use App\Models\Examen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulletinExamen extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['pdf_url'];

    public function getPdfUrlAttribute()
    {
        return url('api/v1/patient/documents/examen/' . $this->id . '/pdf');
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class, 'consultation_id', 'id');
    }
    public function examens(): HasMany
    {
        return $this->hasMany(Examen::class);
    }
}
