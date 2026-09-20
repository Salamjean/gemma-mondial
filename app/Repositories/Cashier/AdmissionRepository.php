<?php

namespace App\Repositories\Cashier;

use App\Models\Admission;
use App\Models\Payment;
use App\Models\TypeAssurance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdmissionRepository
{
    public function __construct()
    {
        //
    }

    public function day()
    {
        return $today = Carbon::today();
    }

    public function cashier()
    {
        return Auth::user()->cashier->id;
    }

    public function payment($type = 'all')
    {
        $query = Payment::where('caissiere_id', Auth::user()->cashier->id);

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        return $query->orderBy('id', 'DESC')->get();
    }


    public function show($id)
    {
        return Payment::findOrFail($id);
    }

    public function verify($id)
    {
        $verify = Payment::where('id', $id)->where('status', 'pending')->exists();

        return $verify ? ['status' => 'success'] : ['status' => 'failure'];
    }

    public function validated($id, $request = null)
    {
        $payment = Payment::findOrFail($id);
        $payment->status = 'success';
        $payment->caissiere_id = auth()->user()->cashier->id;

        $modePaiement = ($request && $request->has('mode_paiement')) ? $request->input('mode_paiement') : 'espece';
        $operateurMobile = ($modePaiement === 'mobile_money' && $request) ? $request->input('operateur_mobile') : null;
        $referencePaiement = ($modePaiement === 'mobile_money' && $request) ? $request->input('reference_paiement') : null;

        $payment->mode_paiement = $modePaiement;
        $payment->operateur_mobile = $operateurMobile;
        $payment->reference_paiement = $referencePaiement;
        $payment->save();

        if ($payment->type == 'admission') {
            $admission = Admission::findOrFail($payment->admission_id);
            $admission->statut_paiement = 1;
            $admission->statut_validation = 1;
            $admission->caissiere_id = Auth::user()->cashier->id;
            $admission->date_paiement = Carbon::now()->format('Y-m-d');
            $admission->mode_paiement = $modePaiement;
            $admission->operateur_mobile = $operateurMobile;
            $admission->reference_paiement = $referencePaiement;
            $admission->save();

            return [
                'status' => 'success',
                'type' => $payment->type,
                'data' => $admission,
                'prestation' => $admission->prestationHospital->prestationService
            ];
        }

        if ($payment->type == 'hospitalisation') {
            return [
                'status' => 'success',
                'type' => $payment->type,
            ];
        }
    }

    public function assure($id, Request $request)
    {

        $request->validate([
            'type' => 'required',
            'no_assurance' => 'required'
        ]);

        $payment = Payment::findOrFail($id);
        $type = TypeAssurance::findOrFail($request->type);
        $payment->type_assurance_id = $type->id;
        $payment->prix_normal = $payment->prix ;
        $payment->prix = $payment->prix - ($type->reduction * $payment->prix);
        $payment->no_assurance = $request->no_assurance;
        $payment->save();

        return ['status' => 'success', 'data' => $payment];
    }
}
