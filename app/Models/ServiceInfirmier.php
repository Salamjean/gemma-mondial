<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceInfirmier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function serviceHospital(): BelongsTo
    {
        return $this->belongsTo(ServiceHospital::class, 'service_hospital_id', 'id');
    }

    public function infirmier(): BelongsTo
    {
        return $this->belongsTo(Infirmier::class, 'infirmier_id', 'id');
    }
}
