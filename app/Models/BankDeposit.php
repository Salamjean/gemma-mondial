<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankDeposit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bank_deposits';

    protected $fillable = [
        'hospital_id',
        'accountant_id',
        'deposit_date',
        'bank_account_number',
        'bank_name',
        'source_type',
        'source_account_number',
        'mode_depot',
        'amount',
        'reference_piece',
        'depositor_name',
        'label',
        'description',
        'justificatif_path',
    ];

    protected $casts = [
        'deposit_date' => 'date',
        'amount' => 'float',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function accountant()
    {
        return $this->belongsTo(Accountant::class);
    }
}
