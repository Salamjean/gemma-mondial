<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingJournal extends Model
{
    use HasFactory;

    protected $table = 'accounting_journals';

    protected $fillable = [
        'hospital_id',
        'code',
        'label',
        'is_active',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function entries()
    {
        return $this->hasMany(AccountingEntry::class, 'journal_id');
    }
}
