<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingEntry extends Model
{
    use HasFactory;

    protected $table = 'accounting_entries';

    protected $fillable = [
        'hospital_id',
        'journal_id',
        'journal_code',
        'entry_date',
        'piece_number',
        'reference_type',
        'reference_id',
        'libelle',
        'status',
        'is_exported_sage',
        'exported_at',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'is_exported_sage' => 'boolean',
        'exported_at' => 'datetime',
    ];

    public function journal()
    {
        return $this->belongsTo(AccountingJournal::class, 'journal_id');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function lines()
    {
        return $this->hasMany(AccountingEntryLine::class, 'accounting_entry_id');
    }

    public function totalDebit()
    {
        return $this->lines->sum('debit');
    }

    public function totalCredit()
    {
        return $this->lines->sum('credit');
    }

    public function isBalanced()
    {
        return round($this->totalDebit(), 2) == round($this->totalCredit(), 2);
    }
}
