<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\Ministere;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MinistereController extends Controller
{
    public function index(Request $request)
    {
        $query = Ministere::with('user')->where('delete', 0);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('nom_direction', 'like', "%{$search}%")
                  ->orWhere('fonction', 'like', "%{$search}%")
                  ->orWhere('contact', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $ministeres = $query->orderByDesc('created_at')->paginate(15);
        $title = "Comptes Ministère de la Santé";

        return view('users.super.ministere.index', compact('ministeres', 'title'));
    }

    public function add()
    {
        $title = "Inscrire un compte Ministère de la Santé";
        $autoRef = 'MIN-' . strtoupper(Str::random(4)) . '-' . rand(100, 999);
        return view('users.super.ministere.add', compact('title', 'autoRef'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'contact' => 'nullable|string|max:30',
            'nom_direction' => 'required|string|max:200',
            'fonction' => 'nullable|string|max:150',
            'password' => 'required|min:6|confirmed',
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre compte.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->role_as = 'ministere';
        $user->password = Hash::make($request->password);
        $user->save();

        $ministere = new Ministere();
        $ministere->user_id = $user->id;
        $ministere->reference = $request->reference ?: ('MIN-' . strtoupper(Str::random(5)));
        $ministere->nom_direction = $request->nom_direction;
        $ministere->contact = $request->contact;
        $ministere->fonction = $request->fonction;
        $ministere->status = 0; // Actif
        $ministere->delete = 0;
        $ministere->save();

        AuditLogService::log(
            'CREATION_COMPTE',
            'MINISTERE',
            "Création du compte Ministère de la Santé : {$user->name} {$user->prenom} ({$ministere->nom_direction})",
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'direction' => $ministere->nom_direction,
                'reference' => $ministere->reference
            ]
        );

        return redirect()->route('super.ministere.index')->with('success', 'Compte Ministère de la Santé créé avec succès.');
    }

    public function edit($id)
    {
        $ministere = Ministere::with('user')->findOrFail($id);
        $title = "Modifier le compte Ministère : " . ($ministere->user->name ?? '');
        return view('users.super.ministere.edit', compact('ministere', 'title'));
    }

    public function update(Request $request, $id)
    {
        $ministere = Ministere::with('user')->findOrFail($id);
        $user = $ministere->user;

        $request->validate([
            'name' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . ($user->id ?? 0),
            'contact' => 'nullable|string|max:30',
            'nom_direction' => 'required|string|max:200',
            'fonction' => 'nullable|string|max:150',
            'password' => 'nullable|min:6|confirmed',
        ]);

        if ($user) {
            $user->name = $request->name;
            $user->prenom = $request->prenom;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
        }

        $ministere->nom_direction = $request->nom_direction;
        $ministere->contact = $request->contact;
        $ministere->fonction = $request->fonction;
        if ($request->filled('reference')) {
            $ministere->reference = $request->reference;
        }
        $ministere->save();

        AuditLogService::log(
            'MODIFICATION_COMPTE',
            'MINISTERE',
            "Mise à jour du compte Ministère : {$user->name} {$user->prenom}",
            ['ministere_id' => $ministere->id]
        );

        return redirect()->route('super.ministere.index')->with('success', 'Compte Ministère mis à jour avec succès.');
    }

    public function toggleStatus($id)
    {
        $ministere = Ministere::with('user')->findOrFail($id);
        $ministere->status = $ministere->status == 0 ? 1 : 0;
        $ministere->save();

        $action = $ministere->status == 0 ? 'activé' : 'désactivé';

        AuditLogService::log(
            'STATUT_COMPTE',
            'MINISTERE',
            "Compte Ministère {$ministere->user->name} {$action}",
            ['ministere_id' => $ministere->id, 'nouveau_statut' => $ministere->status]
        );

        return back()->with('success', "Le compte a été {$action} avec succès.");
    }

    public function destroy($id)
    {
        $ministere = Ministere::findOrFail($id);
        $ministere->delete = 1;
        $ministere->save();
        $ministere->delete(); // Soft delete

        AuditLogService::log(
            'SUPPRESSION_COMPTE',
            'MINISTERE',
            "Suppression du compte Ministère ID {$id}",
            ['ministere_id' => $id]
        );

        return redirect()->route('super.ministere.index')->with('success', 'Compte supprimé avec succès.');
    }
}
