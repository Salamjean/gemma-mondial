<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expenses';

    protected $fillable = [
        'hospital_id',
        'accountant_id',
        'expense_date',
        'account_number',
        'label',
        'beneficiaire',
        'mode_paiement',
        'amount',
        'piece_number',
        'description',
    ];

    protected $casts = [
        'expense_date' => 'date',
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
