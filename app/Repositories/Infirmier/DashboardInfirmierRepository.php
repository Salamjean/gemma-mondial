<?php

namespace App\Repositories\Infirmier;

use App\Models\Consultation;
use App\Models\Infirmier;
use Illuminate\Http\Request;
use App\Models\Registre;
use Illuminate\Support\Facades\Auth;

class DashboardInfirmierRepository
{

    protected $userId;
    protected $infirmierId;
    protected $hospitalId;

    public function __construct($infirmier)
    {
        $this->userId = $infirmier;
        $infirmierData = Infirmier::where('user_id', $this->userId)->first();
        $this->infirmierId = optional($infirmierData)->id;
        $this->hospitalId = optional($infirmierData)->hospital_id;
    }

    public function model()
    {
        return Infirmier::class;
    }
    public function consultation()
    {
        $infId = $this->infirmierId ?? optional(Auth::user()->infirmier)->id;
        $consultations = Consultation::orderByDESC('created_at')
            ->where(function ($q) use ($infId) {
                if ($infId) {
                    $q->where('infirmier_id', $infId)->orWhereNull('infirmier_id');
                } else {
                    $q->whereNull('infirmier_id');
                }
            })
            ->where('date_consultation', date('Y-m-d'))
            ->where('status_inf', '0')
            ->get();

        return $consultations;
    }

    public function makeConsult()
    {
        $infId = $this->infirmierId ?? optional(auth()->user()->infirmier)->id;
        return Consultation::where('infirmier_id', $infId)->where('date_consultation', date('Y-m-d'))->get();
    }
}
