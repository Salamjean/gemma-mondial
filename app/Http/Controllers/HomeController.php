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
        $this->middleware('auth')->except('index', 'about', 'contact', 'terms', 'privacy', 'deleteAccount', 'submitDeleteAccount');
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

    public function terms()
    {
        return view('home.terms');
    }

    public function privacy()
    {
        return view('home.privacy');
    }

    public function deleteAccount()
    {
        return view('home.delete-account');
    }

    public function submitDeleteAccount(Request $request)
    {
        $request->validate([
            'patient_code' => 'nullable|string|max:50',
            'full_name' => 'required|string|max:150',
            'phone' => 'required|string|max:30',
            'email' => 'required|email|max:150',
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
            'confirm_consent' => 'required|accepted',
        ], [
            'full_name.required' => 'Votre nom et prénom sont obligatoires.',
            'phone.required' => 'Le numéro de téléphone associé à votre compte est obligatoire.',
            'email.required' => 'L\'adresse email associée à votre compte est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'reason.required' => 'Veuillez sélectionner le motif de suppression.',
            'confirm_consent.accepted' => 'Vous devez accepter les conditions relatives à la suppression de compte.',
        ]);

        $ticketNumber = 'SUPPR-' . strtoupper(substr(uniqid(), -6));

        // Journaliser la demande dans les logs d'audit de sécurité
        try {
            \App\Services\AuditLogService::log(
                'DEMANDE_SUPPRESSION_COMPTE',
                'PATIENT_ACCOUNT',
                "Demande de suppression de compte soumise [Ticket #{$ticketNumber}] : {$request->full_name}, Tél: {$request->phone}, Email: {$request->email}, Motif: {$request->reason}"
            );
        } catch (\Throwable $e) {}

        return redirect()->route('account.delete')->with('success_ticket', [
            'ticket' => $ticketNumber,
            'name' => $request->full_name,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);
    }

    public function reset()
    {
        return view('auth.passwords.reset');
    }
}
