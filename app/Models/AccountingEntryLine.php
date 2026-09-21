<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingEntryLine extends Model
{
    use HasFactory;

    protected $table = 'accounting_entry_lines';

    protected $fillable = [
        'accounting_entry_id',
        'account_number',
        'account_label',
        'third_party_code',
        'libelle',
        'debit',
        'credit',
        'lettering',
    ];

    protected $casts = [
        'debit' => 'float',
        'credit' => 'float',
    ];

    public function entry()
    {
        return $this->belongsTo(AccountingEntry::class, 'accounting_entry_id');
    }
}
