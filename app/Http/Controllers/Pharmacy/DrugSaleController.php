<?php

namespace App\Http\Controllers\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\CareRequested;
use App\Models\DrugSale;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class DrugSaleController extends Controller
{


    public function pending()
    {
        $hospitalId = Auth::user()->pharmacy->hospital_id;
        $payments = DrugSale::whereDate('created_at', date('Y-m-d'))->with('careRequested.admission.patient.user', 'hospitalisationDrugRequested')->where('status', 'pending')
            ->where('hospital_id', $hospitalId)
            ->get();

        return view('users.pharmacist.sale.pending', compact('payments'));
    }

    public function history()
    {
        $hospitalId = Auth::user()->pharmacy->hospital_id;
        $payments = DrugSale::where('hospital_id', $hospitalId)
            ->where(function ($q) {
                $q->whereDate('created_at', '<', date('Y-m-d'))
                  ->orWhere('status', 'success');
            })
            ->with('careRequested')
            ->get();

        return view('users.pharmacist.sale.history', compact('payments'));
    }

    public function indicate()
    {

        $payments = DrugSale::where('pharmacy_id', Auth::user()->pharmacy->id)->whereDate('created_at', date('Y-m-d'))->get();
        $phamarcy = Auth::user()->pharmacy;

        $pdf = Pdf::loadView('users.pharmacist.indicate', ['payments' => $payments, 'pharmacy' => $phamarcy]);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $response = new Response();
        $response->setContent($pdf->output());
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'inline; filename="payment_of_' . date('d-m-Y') . '.pdf"');

        return $response;
    }

    public function show($id)
    {

        $payment = DrugSale::findOrFail($id);

        return view('users.pharmacist.sale.show', compact('payment'));
    }

    public function detail($id)
    {

        $payment = DrugSale::findOrFail($id);

        return view('users.pharmacist.sale.detail', compact('payment'));
    }

    public function pay($id)
    {
        try {

            $payment = DrugSale::findOrFail($id);

            if ($payment->type == 'care_requested')
                $care = CareRequested::findOrFail($payment->care_requested_id);

            if ($payment->status == 'success')
                return back()->with('error', 'Paiement déja effectuer.');

            // Gestion du stock avant de valider le paiement
            $this->updateDrugStock($payment);

            $payment->status = 'success';
            $payment->pharmacy_id = Auth::user()->pharmacy->id;
            $payment->save();

            if ($payment->type == 'care_requested') {
                $care->status = 'payment_success';
                $care->save();
            }

            return redirect()->route('dashboard')->with('success', 'Paiement effectué avec succès. Stock mis à jour.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Met à jour le stock de médicaments après une vente
     */
    private function updateDrugStock($payment)
    {
        $hospitalId = Auth::user()->pharmacy->hospital_id;

        // Récupérer les médicaments selon le type de vente
        $drugs = [];

        if ($payment->type == 'care_requested' && $payment->careRequested) {
            // Médicaments d'une demande de soin
            $drugs = $payment->careRequested->careRequestedDrugs()
                ->with('drug')
                ->get();

            foreach ($drugs as $drugItem) {
                $this->decrementStock($drugItem->drug_id, $drugItem->quantity, $hospitalId, $payment->id);
            }

        } elseif ($payment->type == 'hospitalisation' && $payment->hospitalisationDrugRequested) {
            // Médicaments d'une hospitalisation
            $drugId = $payment->hospitalisationDrugRequested->drug_id;
            $quantity = $payment->hospitalisationDrugRequested->quantity ?? 1;

            $this->decrementStock($drugId, $quantity, $hospitalId, $payment->id);

        } elseif ($payment->ordonnance) {
            // Médicaments d'une ordonnance (prescriptions)
            $drugs = $payment->ordonnance->prescriptions()
                ->with('drug')
                ->get();

            foreach ($drugs as $drugItem) {
                $this->decrementStock($drugItem->drug_id, $drugItem->quantity, $hospitalId, $payment->id);
            }
        }
    }

    /**
     * Décrémente le stock d'un médicament spécifique
     */
    private function decrementStock($drugId, $quantity, $hospitalId, $saleId)
    {
        $drugHospital = \App\Models\DrugHospital::where('drug_id', $drugId)
            ->where('hospital_id', $hospitalId)
            ->first();

        if (!$drugHospital) {
            Log::warning("Médicament ID {$drugId} non trouvé dans le stock de l'hôpital {$hospitalId}");
            throw new \Exception("Médicament non disponible dans le stock de l'hôpital.");
        }

        // Vérifier si le stock est suffisant
        if ($drugHospital->quantity < $quantity) {
            Log::error("Stock insuffisant pour le médicament ID {$drugId}. Disponible: {$drugHospital->quantity}, Demandé: {$quantity}");
            throw new \Exception("Stock insuffisant pour le médicament. Disponible: {$drugHospital->quantity}, Demandé: {$quantity}");
        }

        // Décrémenter le stock
        $drugHospital->quantity -= $quantity;
        $drugHospital->save();

        // Enregistrer dans les logs pour traçabilité
        Log::info("Stock mis à jour - Médicament ID: {$drugId}, Quantité vendue: {$quantity}, Stock restant: {$drugHospital->quantity}, Vente ID: {$saleId}");

        // Alerte si le stock est faible (moins de 10)
        if ($drugHospital->quantity < 10) {
            Log::warning("ALERTE STOCK FAIBLE - Médicament ID: {$drugId}, Quantité restante: {$drugHospital->quantity}");
        }
    }

    public function payImpression($id)
    {

        $payment = DrugSale::findOrFail($id);
        $phamarcy = Auth::user()->pharmacy;

        $pdf = Pdf::loadView('users.pharmacist.pdf.pay', ['payment' => $payment, 'pharmacy' => $phamarcy]);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        $response = new Response();
        $response->setContent($pdf->output());
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'inline; filename="payment_of_' . date('d-m-Y') . '.pdf"');

        return $response;
    }

}
