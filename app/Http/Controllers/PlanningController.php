<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RendezVous;
use App\Models\Availability;

class PlanningController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rdv = [];

        if ($user && $doctor = $user->doctor) {
            $hospitalId = $doctor->hospital_id ?? $doctor->service_hospital_id ?? 1;

            $rdv = RendezVous::where(function ($query) use ($doctor, $user, $hospitalId) {
                $query->where('doctor_id', $doctor->id)
                    ->orWhere('doctor_id', $user->id)
                    ->orWhereNull('doctor_id')
                    ->orWhereHas('doctor', function ($dq) use ($hospitalId) {
                        $dq->where('hospital_id', $hospitalId);
                    })
                    ->orWhereHas('patient', function ($pq) use ($hospitalId) {
                        $pq->where('hospital_id', $hospitalId);
                    })
                    ->orWhereHas('consultation', function ($cq) use ($hospitalId, $doctor) {
                        $cq->where('hospital_id', $hospitalId)->orWhere('doctor_id', $doctor->id);
                    });
            })
            ->with(['patient.user', 'consultation.patient.user', 'doctor.user'])
            ->orderBy('date', 'desc')
            ->get();

            if (count($rdv) > 0) {
                foreach ($rdv as $item) {
                    $item->date = Carbon::parse($item->date)->format('Y-m-d');
                }
            }
        }

        $availability = $user->availability ?? null;
        $daysMap = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $availableDays = $availability ? (json_decode($availability->days, true) ?? []) : [0, 1, 2, 3, 4];
        $hourStart = $availability ? (json_decode($availability->hour_start, true) ?? []) : [];
        $hourEnd = $availability ? (json_decode($availability->hour_end, true) ?? []) : [];

        return view('users.planning', [
            'title' => 'Votre planning',
            'rendezVous' => $rdv,
            'user' => $user,
            'availability' => $availability,
            'daysMap' => $daysMap,
            'availableDays' => $availableDays,
            'hourStart' => $hourStart,
            'hourEnd' => $hourEnd,
        ]);
    }
}
