<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Hospital;
use App\Models\Caissiere;
use App\Models\Secretaire;
use App\Models\ModeAdmission;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory,Notifiable,HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'role_as',
        'gender',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function hospital()
    {
        return $this->hasOne(Hospital::class);
    }

    public function secretariat()
    {
        return $this->hasOne(Secretaire::class);
    }

    public function pharmacy()
    {
        return $this->hasOne(Pharmacy::class);
    }

    public function infirmier()
    {
        return $this->hasOne(Infirmier::class);
    }

    public function accountant()
    {
        return $this->hasOne(Accountant::class);
    }

    public function cashier()
    {
        return $this->hasOne(Caissiere::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class, 'user_id');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'user_id');
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }

    public function availability()
    {
        return $this->hasOne(Availability::class);
    }

    public function ministere()
    {
        return $this->hasOne(Ministere::class, 'user_id');
    }

    public function getTelephoneAttribute()
    {
        if ($this->doctor && !empty($this->doctor->contact)) {
            return $this->doctor->contact;
        }
        if ($this->infirmier && !empty($this->infirmier->contact)) {
            return $this->infirmier->contact;
        }
        if ($this->secretariat && !empty($this->secretariat->contact)) {
            return $this->secretariat->contact;
        }
        if ($this->cashier && !empty($this->cashier->contact)) {
            return $this->cashier->contact;
        }
        if ($this->accountant && !empty($this->accountant->contact)) {
            return $this->accountant->contact;
        }
        if ($this->patient && !empty($this->patient->telephone)) {
            return $this->patient->telephone;
        }
        return $this->attributes['telephone'] ?? null;
    }

    public function getContactAttribute()
    {
        return $this->telephone;
    }

}
