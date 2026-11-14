<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UcpFmAdminController extends Controller
{
    public const ORGANISATION = 'UCP-FM';
    public const ROLE = 'admin';

    private function baseQuery()
    {
        return Admin::with('user')
            ->where('organisation', self::ORGANISATION)
            ->whereHas('user', fn ($u) => $u->where('role_as', self::ROLE));
    }

    public function index(Request $request)
    {
        $query = $this->baseQuery();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('contact', 'like', "%{$search}%")
                  ->orWhere('fonction', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $admins = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $title = "Administrateurs UCP-FM";

        return view('users.super.ucpfm.index', compact('admins', 'title'));
    }

    public function add()
    {
        $title = "Ajouter un administrateur UCP-FM";
        return view('users.super.ucpfm.add', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate($this->rules(), $this->messages());

        $user = new User();
        $user->name = $request->name;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->role_as = self::ROLE;
        $user->password = Hash::make($request->password);
        $user->save();

        // Tables MyISAM (pas de transactions) : on supprime l'utilisateur si le profil échoue.
        try {
            Admin::create([
                'user_id' => $user->id,
                'organisation' => self::ORGANISATION,
                'fonction' => $request->fonction,
                'contact' => $request->contact ?? '',
                'address' => $request->address,
                'status' => '0', // Actif
            ]);
        } catch (\Throwable $e) {
            User::withTrashed()->whereKey($user->id)->forceDelete();
            throw $e;
        }

        AuditLogService::log(
            'CREATION_COMPTE',
            'UCP-FM',
            "Création d'un administrateur UCP-FM : {$user->name} {$user->prenom}",
            ['user_id' => $user->id, 'email' => $user->email, 'role' => self::ROLE]
        );

        return redirect()->route('super.ucpfm.index')->with('success', 'Administrateur UCP-FM créé avec succès.');
    }

    public function edit($id)
    {
        $admin = $this->baseQuery()->findOrFail($id);
        $title = "Modifier l'administrateur UCP-FM : " . ($admin->user->name ?? '');
        return view('users.super.ucpfm.edit', compact('admin', 'title'));
    }

    public function update(Request $request, $id)
    {
        $admin = $this->baseQuery()->findOrFail($id);
        $user = $admin->user;

        $request->validate($this->rules($user->id), $this->messages());

        $user->name = $request->name;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $admin->fonction = $request->fonction;
        $admin->contact = $request->contact ?? '';
        $admin->address = $request->address;
        $admin->save();

        AuditLogService::log(
            'MODIFICATION_COMPTE',
            'UCP-FM',
            "Mise à jour de l'administrateur UCP-FM : {$user->name} {$user->prenom}",
            ['admin_id' => $admin->id]
        );

        return redirect()->route('super.ucpfm.index')->with('success', 'Administrateur UCP-FM mis à jour avec succès.');
    }

    public function toggleStatus($id)
    {
        $admin = $this->baseQuery()->findOrFail($id);
        $admin->status = $admin->status == '0' ? '1' : '0';
        $admin->save();

        $action = $admin->status == '0' ? 'activé' : 'désactivé';

        AuditLogService::log(
            'STATUT_COMPTE',
            'UCP-FM',
            "Administrateur UCP-FM {$admin->user->name} {$action}",
            ['admin_id' => $admin->id, 'nouveau_statut' => $admin->status]
        );

        return back()->with('success', "Le compte a été {$action} avec succès.");
    }

    public function destroy($id)
    {
        $admin = $this->baseQuery()->findOrFail($id);
        $admin->status = '1';
        $admin->save();
        $admin->delete(); // Soft delete

        AuditLogService::log(
            'SUPPRESSION_COMPTE',
            'UCP-FM',
            "Suppression de l'administrateur UCP-FM ID {$id}",
            ['admin_id' => $id]
        );

        return redirect()->route('super.ucpfm.index')->with('success', 'Administrateur supprimé avec succès.');
    }

    private function rules(?int $userId = null): array
    {
        return [
            'name' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email' . ($userId ? ',' . $userId : ''),
            'contact' => 'nullable|string|max:30',
            'fonction' => 'nullable|string|max:150',
            'address' => 'nullable|string|max:255',
            'password' => ($userId ? 'nullable' : 'required') . '|min:6|confirmed',
        ];
    }

    private function messages(): array
    {
        return [
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre compte.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ];
    }
}
