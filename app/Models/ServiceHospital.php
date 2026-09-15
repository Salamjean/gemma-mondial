<?php

namespace App\Models;

use App\Models\Service;
use App\Models\Hospital;
use App\Models\PrestationHospital;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceHospital extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function prestationHospitals(): HasMany
    {
        return $this->hasMany(PrestationHospital::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class, 'hospital_id', 'id');
    }
}
