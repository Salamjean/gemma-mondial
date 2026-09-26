<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OneSignalToken;
use Illuminate\Http\Request;

class OneSignalTokenController extends Controller
{
    public function store(Request $request)
    {
        if (OneSignalToken::where('patient_id', auth()->user()->patient->id)->exists()) {
            OneSignalToken::firstWhere('patient_id', auth()->user()->patient->id)->delete();
        }
        $token = new OneSignalToken();
        $token -> patient_id =  auth()->user()->patient->id;
        $token -> token = $request->token;
        $token -> save();

        return response()->json(["message" => "Token store successfully"], 200);

    }
}


