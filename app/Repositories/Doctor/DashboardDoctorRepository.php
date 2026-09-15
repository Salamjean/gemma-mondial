<?php

namespace App\Repositories\Doctor;

use App\Models\Consultation;
use App\Models\Declaration;
use App\Models\DeclarationDeces;
use App\Models\DeclarationNaissance;
use App\Models\Doctor;
use Illuminate\Support\Facades\DB;

class DashboardDoctorRepository
{

    protected $userId;
    protected $doctorId;
    protected $hospitalId;

    public function __construct($doctor)
    {
        $this->userId = $doctor;
        $doctorData = Doctor::where('user_id', $this->userId)->first();
        $this->doctorId = $doctorData->id;
        $this->hospitalId = $doctorData->hospital_id;
    }

    public function model()
    {
        return Doctor::class;
    }

    public function dashboard()
    {
        //BBIRTH HOSPITAL
        $moisB = [];

        for ($i = 9; $i >= 0; $i--) {
            $moisB[] = now()->subMonths($i)->format('Y-m');
        }

        $birth = Declaration::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") AS month'), DB::raw('COUNT(*) as count'))
            ->where('hospital_id', $this->hospitalId)
            ->whereIn(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $moisB)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $birth = array_replace(array_fill_keys($moisB, 0), $birth->toArray());

        //DETATH HOSPITAL
        $moisD = [];

        for ($i = 9; $i >= 0; $i--) {
            $moisD[] = now()->subMonths($i)->format('Y-m');
        }

        $death = Declaration::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") AS month'), DB::raw('COUNT(*) as count'))
        ->where('hospital_id', $this->hospitalId)
            ->whereIn(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $moisD)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $death = array_replace(array_fill_keys($moisD, 0), $death->toArray());

        //CONSULTATION
        $moisC = [];

        for ($i = 9; $i >= 0; $i--) {
            $moisC[] = now()->subMonths($i)->format('Y-m');
        }


        $consultation = Consultation::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") AS month'), DB::raw('COUNT(*) as count'))
            ->where('doctor_id', $this->doctorId)
            ->where(function ($q) {
                $q->where('status_inf', 1)->orWhere('status', 1);
            })
            ->where('hospital_id', $this->hospitalId)
            ->whereIn(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'), $moisC)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        $consultation = array_replace(array_fill_keys($moisC, 0), $consultation->toArray());

        return [
            'consultation' => $consultation,
            'birth' => $birth,
            'death' => $death
        ];
    }




}
