<?php

namespace App\Models;

use App\Models\Consultation;
use App\Models\BulletinExamen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Examen extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['resultat_url'];

    public function getResultatUrlAttribute()
    {
        if (!empty($this->resultat) && is_string($this->resultat)) {
            if (str_starts_with($this->resultat, 'http://') || str_starts_with($this->resultat, 'https://')) {
                return $this->resultat;
            }
            return asset('assets/uploads/analyses/' . $this->resultat);
        }
        return null;
    }

    public function bulletin_examen()
    {
        return $this->belongsTo(BulletinExamen::class, 'bulletin_examen_id', 'id');
    }
}
