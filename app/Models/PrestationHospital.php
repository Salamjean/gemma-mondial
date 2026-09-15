<?php

namespace App\Models;

use App\Models\ServiceHospital;
use App\Models\PrestationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrestationHospital extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function serviceHospital(): BelongsTo
    {
        return $this->belongsTo(ServiceHospital::class, 'service_hospital_id', 'id');
    }

    public function prestationService(): BelongsTo
    {
        return $this->belongsTo(PrestationService::class, 'prestation_service_id', 'id');
    }
}
