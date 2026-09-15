<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\ResetPasswordController;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index', 'about', 'contact');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {

        return view('home.accueil');
    }

    public function about()
    {

        return view('home.apropos');
    }

    public function contact()
    {

        return view('home.contact');
    }

    public function reset()
    {
        return view('auth.passwords.reset');
    }
}
