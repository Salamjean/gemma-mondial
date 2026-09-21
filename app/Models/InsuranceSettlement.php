<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InsuranceSettlement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'insurance_settlements';

    protected $fillable = [
        'hospital_id',
        'type_assurance_id',
        'accountant_id',
        'settlement_date',
        'amount',
        'mode_paiement',
        'reference_piece',
        'note',
    ];

    protected $casts = [
        'settlement_date' => 'date',
        'amount' => 'float',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function typeAssurance()
    {
        return $this->belongsTo(TypeAssurance::class, 'type_assurance_id');
    }

    public function accountant()
    {
        return $this->belongsTo(Accountant::class);
    }
}
