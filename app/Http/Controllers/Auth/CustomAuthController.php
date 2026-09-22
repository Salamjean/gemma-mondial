<?php

namespace App\Http\Controllers\Auth;

use App\Services\AuditLogService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Hash;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class CustomAuthController extends Controller
{

    protected $redirectTo = RouteServiceProvider::HOME;

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $response = $this->roleResponse($request->email);

        if($response['status'] === 'error') {
            AuditLogService::log(
                'ECHEC_CONNEXION',
                'AUTHENTIFICATION',
                "Tentative de connexion refusée pour l'email: {$request->email} ({$response['message']})",
                ['email' => $request->email]
            );
            return back()->withErrors($response['message']);
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $userName = trim(($user->nom ?? '') . ' ' . ($user->prenom ?? $user->name ?? ''));
            AuditLogService::log(
                'CONNEXION',
                'AUTHENTIFICATION',
                "Connexion réussie de l'utilisateur: {$userName} (" . strtoupper($user->role_as ?? 'utilisateur') . ")",
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role_as
                ],
                $user->id,
                $user->hospital_id ?? null
            );

            $intended = session()->get('url.intended');
            if ($intended && (
                str_contains($intended, 'incoming-requests') ||
                str_contains($intended, 'pending-online-requests') ||
                str_contains($intended, 'patient-info') ||
                str_contains($intended, 'call/')
            )) {
                session()->forget('url.intended');
            }

            return redirect()->intended('dashboard')->with('success', 'Bienvenue!');
        }

        AuditLogService::log(
            'ECHEC_CONNEXION',
            'AUTHENTIFICATION',
            "Échec de connexion (mot de passe incorrect) pour l'email: {$request->email}",
            ['email' => $request->email]
        );

        return back()->withErrors('Adresse ou mot de passe incorrecte!');
    }

    // public function registration()
    // {
    //     return view('auth.registration');
    // }

    // public function customRegistration(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required|min:6',
    //     ]);

    //     $data = $request->all();
    //     $check = $this->create($data);

    //     return redirect("dashboard")->withSuccess('You have signed-in');
    // }

    // public function create(array $data)
    // {
    //     return User::create([
    //         'name' => $data['name'],
    //         'email' => $data['email'],
    //         'password' => Hash::make($data['password'])
    //     ]);
    // }

    public function dashboard()
    {
        if (Auth::check()) {
            return view('dashboard');
        }

        return back()->withErrors('401 status');
    }

    public function logout()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userName = trim(($user->nom ?? '') . ' ' . ($user->prenom ?? $user->name ?? ''));
            AuditLogService::log(
                'DECONNEXION',
                'AUTHENTIFICATION',
                "Déconnexion de l'utilisateur: {$userName} (" . strtoupper($user->role_as ?? 'utilisateur') . ")",
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role_as
                ],
                $user->id,
                $user->hospital_id ?? null
            );
        }

        Session::flush();
        Auth::logout();

        return Redirect('login');
    }

    private function roleResponse($email) : array
    {
        if ($user = User::where('email', $email)->with('hospital', 'doctor', 'accountant', 'cashier', 'infirmier', 'pharmacy', 'secretariat', 'admin', 'patient')->first()) {
            switch ($user->role_as) {
                case 'super':
                    
                    return ['status' => 'success', 'message' => 'Ok!'];

                case 'hospital':

                    if ($user->hospital->status == 1 || $user->hospital->delete == 1)
                        return ['status' => 'error', 'message' => 'Compte désactivé ou supprimé!'];
                    return ['status' => 'success', 'message' => 'Ok!'];

                case 'doctor':

                    if ($user->doctor->status == 1 || $user->doctor->delete == 1)
                        return ['status' => 'error', 'message' => 'Compte désactivé ou supprimé!'];
                    return ['status' => 'success', 'message' => 'Ok!'];

                case 'accountant':

                    if ($user->accountant->status == 1 || $user->accountant->delete == 1)
                        return ['status' => 'error', 'message' => 'Compte désactivé ou supprimé!'];
                    return ['status' => 'success', 'message' => 'Ok!'];

                case 'cashier':

                    if ($user->cashier->status == 1 || $user->cashier->delete == 1)
                        return ['status' => 'error', 'message' => 'Compte désactivé ou supprimé!'];
                    return ['status' => 'success', 'message' => 'Ok!'];

                case 'infirmier':

                    if ($user->infirmier->status == 1 || $user->infirmier->delete == 1)
                        return ['status' => 'error', 'message' => 'Compte désactivé ou supprimé!'];
                    return ['status' => 'success', 'message' => 'Ok!'];

                case 'pharmacy' :

                    if ($user->pharmacy->status == 1 || $user->pharmacy->delete == 1)
                        return ['status' => 'error', 'message' => 'Compte désactivé ou supprimé!'];
                    return ['status' => 'success', 'message' => 'Ok!'];

                case 'secretariat':

                    if ($user->secretariat->status == 1 || $user->secretariat->delete == 1)
                        return ['status' => 'error', 'message' => 'Compte désactivé ou supprimé!'];
                    return ['status' => 'success', 'message' => 'Ok!'];

                case 'patient':

                    if ($user->patient->status == 1 || $user->patient->delete == 1)
                        return ['status' => 'error', 'message' => 'Vous n\'êtes pas aurorisé!'];
                    return ['status' => 'success', 'message' => 'Ok!'];

                default:
                    return ['status' => 'error', 'message' => 'Inscrivez vous!'];
            }
        }

        return ['status' => 'error', 'message' => 'Inscrivez vous!'];
    }
}
