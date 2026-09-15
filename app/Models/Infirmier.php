<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Infirmier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function serviceHospital()
    {
        return $this->belongsTo(ServiceHospital::class, 'service_hospital_id', 'id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(ServiceInfirmier::class, 'infirmier_id', 'id');
    }

    public function hospital() : BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }

    public function consultations() : HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function daysHospitalisation(): HasMany
    {
        return $this->hasMany(DayHospitalisation::class);
    }

}
