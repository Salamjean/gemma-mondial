<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {

        $permissions = Permission::where('user_id', auth()->user()->id)->get();

        return view('users.permission.permission', ['title' => 'Liste des permissions', 'permissions' => $permissions]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'beging_date' => 'required|date',
            'end_date' => 'required|date',
            'beging_time' => 'nullable',
            'end_time' => 'nullable',
            'description' => 'required',
            '_url' => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf'])],
        ]);

        //type
        if (auth()->user()->role_as == 'doctor')
            $type = 'doctor';

        elseif (auth()->user()->role_as == 'cashier')
            $type = 'cashier';

        elseif (auth()->user()->role_as == 'secretariat' || auth()->user()->role_as == 'secreteriat')
            $type = 'secreteriat';

        elseif (auth()->user()->role_as == 'accountant')
            $type = 'accountant';

        elseif (auth()->user()->role_as == 'pharmacy')
            $type = 'pharmacy';

        elseif (auth()->user()->role_as == 'infirmier')
            $type = 'male_nurse';
        else
            $type = null;


        if (!$type)
            return back()->with('error', 'Personal not found.');

        $hospitalId = null;
        //hospital
        if (auth()->user()->role_as == 'doctor')
            $hospitalId = auth()->user()->doctor->hospital_id;

        elseif (auth()->user()->role_as == 'cashier')
        $hospitalId = auth()->user()->cashier->hospital_id;

        elseif (auth()->user()->role_as == 'secretariat')
        $hospitalId = auth()->user()->secretariat->hospital_id;

        elseif (auth()->user()->role_as == 'accountant')
        $hospitalId = auth()->user()->accountant->hospital_id;

        elseif (auth()->user()->role_as == 'pharmacy')
        $hospitalId = auth()->user()->pharmacy->hospital_id;

        elseif (auth()->user()->role_as == 'infirmier')
        $hospitalId = auth()->user()->infirmier->hospital_id;
        else
            $hospitalId = null;


        if (!Permission::whereDate('beging_date', $request->beging_date)->whereDate('end_date', $request->end_date)->where('user_id', auth()->user()->id)->exists()) {
            if(!Permission::where('user_id', auth()->user()->id)->where('status', 'pending')->exists()){
                $permission = new Permission();
                $permission->type = $type;
                $permission->code = rand(00000, 99999);
                $permission->user_id = auth()->user()->id;
                $permission->hospital_id = $hospitalId;

                $permission->beging_date = $request->beging_date;
                $permission->end_date = $request->end_date;
                $permission->beging_time = $request->beging_time ?? null;
                $permission->end_time = $request->end_time ?? null;
                $permission->description = $request->description;

                if ($request->hasFile('_url'))
                $permission->_url = uploadImage($request->_url, 'permission');

                $permission->save();
            } else
                return back()->with('error', 'Personal permission already!');
        }else
            return back()->with('error', 'Personal permission already!');
        return back()->with('success', 'Votre demande de permission est en cours de validation!');
    }

    public function update($_id, $status)
    {
        // dd($status);
        Permission::find($_id)->update([
            'status' => $status,
        ]);

        return back()->with('success', 'Status modifié.');
    }

    public function status($status)
    {
        $permissions = Permission::where('hospital_id', auth()->user()->hospital->id)->where('status', $status)->get();

        return view('users.permission.list', ['permissions' => $permissions, 'status' => $status]);

    }

    public function userUpdate(Request $request, $id)
    {
        $permission = Permission::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();

        if ($permission->status !== 'pending') {
            return back()->with('error', 'Vous ne pouvez modifier que les demandes de permission en attente.');
        }

        $request->validate([
            'beging_date' => 'required|date',
            'end_date' => 'required|date',
            'beging_time' => 'nullable',
            'end_time' => 'nullable',
            'description' => 'required',
            '_url' => ['nullable', new FileTypeValidate(['jpg', 'jpeg', 'png', 'pdf'])],
        ]);

        $permission->beging_date = $request->beging_date;
        $permission->end_date = $request->end_date;
        $permission->beging_time = $request->beging_time ?? null;
        $permission->end_time = $request->end_time ?? null;
        $permission->description = $request->description;

        if ($request->hasFile('_url')) {
            $permission->_url = uploadImage($request->_url, 'permission');
        }

        $permission->save();

        return back()->with('success', 'Votre demande de permission a été modifiée avec succès !');
    }
}
