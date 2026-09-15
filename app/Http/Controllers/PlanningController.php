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
            $rdv = RendezVous::whereHas('consultation', function ($query) use ($doctor) {
                $query->with('patient.user')->where('doctor_id', $doctor->id);
            })->get();

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
