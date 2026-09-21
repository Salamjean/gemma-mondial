<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    use HasFactory;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'hospital_id',
        'account_number',
        'label',
        'type', // produit, charge, client, tresorerie, tiers, general
        'is_default',
        'is_active',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
