<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Caissiere;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function profile()
    {
        $title = 'Profile | Caissière';
        $cashier = Caissiere::where('user_id', Auth::user()->id)->first();
        return view('users.cashier.profile', compact('title', 'cashier'));
    }

    public function update(Request $request)
    {

        $user = User::findOrFail(auth()->user()->id);
        $user->name = $request->name;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();


        $cashier = Caissiere::where("user_id", $user->id)->first();

        //update cashier
        $cashier->contact = $request->contact;

        if ($request->hasFile('image'))
            $cashier->img_url = deleteUploadImage($request->image, 'cashier');
        $cashier->save();

        return redirect()->route('dashboard')->with('success', "Informations modifiées avec succès.");
    }

    public function planning()
    {
        $title = 'Planning & Disponibilités | Caisse';
        $user = Auth::user();
        $availability = $user->availability;

        $daysMap = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $availableDays = $availability ? (json_decode($availability->days, true) ?? []) : [0, 1, 2, 3, 4];
        $hourStart = $availability ? (json_decode($availability->hour_start, true) ?? []) : [];
        $hourEnd = $availability ? (json_decode($availability->hour_end, true) ?? []) : [];

        return view('users.cashier.planning', compact('title', 'user', 'availability', 'daysMap', 'availableDays', 'hourStart', 'hourEnd'));
    }
}
