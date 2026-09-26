<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\Send;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function create()
    {
        return view('contact');
    }

    public function store(ContactRequest $request)
    {
        
        
        Mail::to(env('MAIL_FROM_ADDRESS'))
        ->send(new Send($request->except('_token')));

        return response([
            'message' => 'Message envoyé. Nous vous contacterons sous 24h.',
        ], 200);
    }
}


