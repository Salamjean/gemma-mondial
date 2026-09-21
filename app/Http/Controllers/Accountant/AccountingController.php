<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Accountant;
use App\Models\AccountingEntry;
use App\Models\AccountingEntryLine;
use App\Models\AccountingJournal;
use App\Models\ChartOfAccount;
use App\Models\Expense;
use App\Models\InsuranceSettlement;
use App\Models\TypeAssurance;
use App\Models\Assurance;
use App\Models\Admission;
use App\Models\Hospital;
use App\Services\AccountingService;
use App\Services\SageSaariExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class AccountingController extends Controller
{
    protected function getHospitalId()
    {
        $user = Auth::user();
        if ($user && $user->hospital) {
            return $user->hospital->id;
        }
        $accountant = Accountant::where('user_id', $user->id)->first();
        if ($accountant && $accountant->hospital_id) {
            return $accountant->hospital_id;
        }
        return $user->hospital_id ?? 1;
    }

    /**
     * Tableau de bord comptable
     */
    public function dashboard(Request $request)
    {
        $title = 'Comptabilité | Tableau de bord';
        $hospitalId = $this->getHospitalId();

        // Initialiser et synchroniser
        AccountingService::initHospitalAccounting($hospitalId);
        AccountingService::syncExistingOperations($hospitalId);

        $today = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $balance = AccountingService::getGeneralBalance($hospitalId, $startOfMonth, $endOfMonth);

        $totalEntries = AccountingEntry::where('hospital_id', $hospitalId)->count();
        $unexportedEntries = AccountingEntry::where('hospital_id', $hospitalId)->where('is_exported_sage', false)->count();

        $recentEntries = AccountingEntry::with('lines')
            ->where('hospital_id', $hospitalId)
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $todayTotal = AccountingEntryLine::join('accounting_entries', 'accounting_entry_lines.accounting_entry_id', '=', 'accounting_entries.id')
            ->where('accounting_entries.hospital_id', $hospitalId)
            ->where('accounting_entries.status', 'valide')
            ->whereDate('accounting_entries.entry_date', $today)
            ->whereIn('accounting_entry_lines.account_number', ['531100', '518100', '512100'])
            ->sum('accounting_entry_lines.debit');

        $todayExpenses = Expense::where('hospital_id', $hospitalId)
            ->whereDate('expense_date', $today)
            ->sum('amount');

        $monthExpenses = Expense::where('hospital_id', $hospitalId)
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $journals = AccountingJournal::where('hospital_id', $hospitalId)->get();

        return view('users.accountant.accounting.dashboard', compact(
            'title', 'balance', 'totalEntries', 'unexportedEntries', 'recentEntries', 'journals', 'todayTotal', 'todayExpenses', 'monthExpenses'
        ));
    }

    /**
     * Plan Comptable
     */
    public function planComptable(Request $request)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->route('accountant.accounting.dashboard')->with('error', 'Accès non autorisé. Le Plan Comptable est réservé au comptable.');
        }

        $title = 'Comptabilité | Plan Comptable';
        $hospitalId = $this->getHospitalId();
        AccountingService::initHospitalAccounting($hospitalId);

        $accounts = ChartOfAccount::where('hospital_id', $hospitalId)
            ->orderBy('account_number', 'asc')
            ->get();

        return view('users.accountant.accounting.plan_comptable', compact('title', 'accounts'));
    }

    /**
     * Ajouter un compte comptable
     */
    public function storeCompte(Request $request)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->back()->with('error', 'Action non autorisée. En tant qu\'administrateur, vous êtes en mode consultation seule.');
        }

        $hospitalId = $this->getHospitalId();

        $request->validate([
            'account_number' => 'required|string|max:20',
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:produit,charge,tresorerie,client,tiers,general',
        ]);

        ChartOfAccount::updateOrCreate(
            [
                'hospital_id' => $hospitalId,
                'account_number' => trim($request->account_number),
            ],
            [
                'label' => trim($request->label),
                'type' => $request->type,
                'is_active' => true,
            ]
        );

        return redirect()->back()->with('success', 'Compte comptable enregistré avec succès.');
    }

    /**
     * Modifier un compte comptable
     */
    public function updateCompte(Request $request, $id)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->back()->with('error', 'Action non autorisée. En tant qu\'administrateur, vous êtes en mode consultation seule.');
        }

        $hospitalId = $this->getHospitalId();
        $account = ChartOfAccount::where('hospital_id', $hospitalId)->findOrFail($id);

        $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:produit,charge,tresorerie,client,tiers,general',
            'is_active' => 'required|boolean',
        ]);

        $account->update([
            'label' => trim($request->label),
            'type' => $request->type,
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'Compte comptable mis à jour.');
    }

    /**
     * Supprimer un compte comptable
     */
    public function deleteCompte($id)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->back()->with('error', 'Action non autorisée. En tant qu\'administrateur, vous êtes en mode consultation seule.');
        }

        $hospitalId = $this->getHospitalId();
        $account = ChartOfAccount::where('hospital_id', $hospitalId)->findOrFail($id);
        $account->delete();

        return redirect()->back()->with('success', 'Compte comptable supprimé avec succès.');
    }

    /**
     * Journaux d'écritures
     */
    public function journaux(Request $request)
    {
        $title = 'Comptabilité | Journaux des Écritures';
        $hospitalId = $this->getHospitalId();
        AccountingService::initHospitalAccounting($hospitalId);

        $journals = AccountingJournal::where('hospital_id', $hospitalId)->get();

        $journalCode = $request->get('journal', 'ALL');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $query = AccountingEntry::with('lines')
            ->where('hospital_id', $hospitalId)
            ->whereBetween('entry_date', [$startDate, $endDate]);

        if ($journalCode && $journalCode !== 'ALL') {
            $query->where('journal_code', $journalCode);
        }

        $entries = $query->orderBy('entry_date', 'desc')->orderBy('id', 'desc')->paginate(25);

        return view('users.accountant.accounting.journaux', compact(
            'title', 'journals', 'journalCode', 'startDate', 'endDate', 'entries'
        ));
    }

    /**
     * Téléchargement en PDF du journal des écritures
     */
    public function journauxPdf(Request $request)
    {
        $hospitalId = $this->getHospitalId();
        $comptable = Accountant::with(['user', 'hospital'])->where('user_id', Auth::user()->id)->first();
        $hospital = Hospital::find($hospitalId);

        $journalCode = $request->get('journal', 'ALL');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $query = AccountingEntry::with('lines')
            ->where('hospital_id', $hospitalId)
            ->whereBetween('entry_date', [$startDate, $endDate]);

        if ($journalCode && $journalCode !== 'ALL') {
            $query->where('journal_code', $journalCode);
        }

        $entries = $query->orderBy('entry_date', 'asc')->orderBy('id', 'asc')->get();

        $pdf = Pdf::loadView('users.accountant.accounting.journaux_pdf', compact(
            'entries', 'journalCode', 'startDate', 'endDate', 'comptable', 'hospital'
        ));

        $pdf->setPaper('A4', 'landscape');

        $filename = 'JOURNAL_ECRITURES_' . $journalCode . '_' . Carbon::parse($startDate)->format('Ymd') . '_' . Carbon::parse($endDate)->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Grand Livre
     */
    public function grandLivre(Request $request)
    {
        $title = 'Comptabilité | Grand Livre';
        $hospitalId = $this->getHospitalId();
        AccountingService::initHospitalAccounting($hospitalId);

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $accountNumber = $request->get('account_number', 'ALL');

        $query = AccountingEntryLine::join('accounting_entries', 'accounting_entry_lines.accounting_entry_id', '=', 'accounting_entries.id')
            ->where('accounting_entries.hospital_id', $hospitalId)
            ->where('accounting_entries.status', 'valide')
            ->whereBetween('accounting_entries.entry_date', [$startDate, $endDate]);

        if ($accountNumber && $accountNumber !== 'ALL') {
            $query->where('accounting_entry_lines.account_number', $accountNumber);
        }

        $lines = $query->select(
            'accounting_entry_lines.*',
            'accounting_entries.entry_date',
            'accounting_entries.piece_number',
            'accounting_entries.journal_code'
        )->orderBy('accounting_entry_lines.account_number', 'asc')
         ->orderBy('accounting_entries.entry_date', 'asc')
         ->get();

        // Grouper par compte
        $groupedLines = $lines->groupBy('account_number');
        $accountsList = ChartOfAccount::where('hospital_id', $hospitalId)->orderBy('account_number', 'asc')->get();

        return view('users.accountant.accounting.grand_livre', compact(
            'title', 'groupedLines', 'accountsList', 'accountNumber', 'startDate', 'endDate'
        ));
    }

    /**
     * Téléchargement en PDF du Grand Livre
     */
    public function grandLivrePdf(Request $request)
    {
        $hospitalId = $this->getHospitalId();
        $comptable = Accountant::with(['user', 'hospital'])->where('user_id', Auth::user()->id)->first();
        $hospital = Hospital::find($hospitalId);

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());
        $accountNumber = $request->get('account_number', 'ALL');

        $query = AccountingEntryLine::join('accounting_entries', 'accounting_entry_lines.accounting_entry_id', '=', 'accounting_entries.id')
            ->where('accounting_entries.hospital_id', $hospitalId)
            ->where('accounting_entries.status', 'valide')
            ->whereBetween('accounting_entries.entry_date', [$startDate, $endDate]);

        if ($accountNumber && $accountNumber !== 'ALL') {
            $query->where('accounting_entry_lines.account_number', $accountNumber);
        }

        $lines = $query->select(
            'accounting_entry_lines.*',
            'accounting_entries.entry_date',
            'accounting_entries.piece_number',
            'accounting_entries.journal_code'
        )->orderBy('accounting_entry_lines.account_number', 'asc')
         ->orderBy('accounting_entries.entry_date', 'asc')
         ->get();

        $groupedLines = $lines->groupBy('account_number');

        $pdf = Pdf::loadView('users.accountant.accounting.grand_livre_pdf', compact(
            'groupedLines', 'accountNumber', 'startDate', 'endDate', 'comptable', 'hospital'
        ));

        $pdf->setPaper('A4', 'landscape');

        $filename = 'GRAND_LIVRE_' . $accountNumber . '_' . Carbon::parse($startDate)->format('Ymd') . '_' . Carbon::parse($endDate)->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Balance Générale
     */
    public function balance(Request $request)
    {
        $title = 'Comptabilité | Balance Générale';
        $hospitalId = $this->getHospitalId();

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $balance = AccountingService::getGeneralBalance($hospitalId, $startDate, $endDate);

        return view('users.accountant.accounting.balance', compact(
            'title', 'balance', 'startDate', 'endDate'
        ));
    }

    /**
     * Téléchargement en PDF de la Balance Générale
     */
    public function balancePdf(Request $request)
    {
        $hospitalId = $this->getHospitalId();
        $comptable = Accountant::with(['user', 'hospital'])->where('user_id', Auth::user()->id)->first();
        $hospital = Hospital::find($hospitalId);

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $balance = AccountingService::getGeneralBalance($hospitalId, $startDate, $endDate);

        $pdf = Pdf::loadView('users.accountant.accounting.balance_pdf', compact(
            'balance', 'startDate', 'endDate', 'comptable', 'hospital'
        ));

        $pdf->setPaper('A4', 'landscape');

        $filename = 'BALANCE_GENERALE_' . Carbon::parse($startDate)->format('Ymd') . '_' . Carbon::parse($endDate)->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Vue Export Sage SAARI
     */
    public function exportSageView(Request $request)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->route('accountant.accounting.dashboard')->with('error', 'Accès non autorisé. La passerelle Sage SAARI est réservée au comptable.');
        }

        $title = 'Comptabilité | Export Sage SAARI 100';
        $hospitalId = $this->getHospitalId();
        AccountingService::initHospitalAccounting($hospitalId);

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $totalUnexported = AccountingEntry::where('hospital_id', $hospitalId)
            ->where('is_exported_sage', false)
            ->count();

        $entriesToExport = AccountingEntry::with('lines')
            ->where('hospital_id', $hospitalId)
            ->whereBetween('entry_date', [$startDate, $endDate])
            ->orderBy('entry_date', 'asc')
            ->get();

        return view('users.accountant.accounting.export_sage', compact(
            'title', 'startDate', 'endDate', 'totalUnexported', 'entriesToExport'
        ));
    }

    /**
     * Téléchargement de l'export Sage SAARI (.txt)
     */
    public function exportSageDownload(Request $request)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->route('accountant.accounting.dashboard')->with('error', 'Accès non autorisé. La passerelle Sage SAARI est réservée au comptable.');
        }

        $hospitalId = $this->getHospitalId();

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $markExported = $request->has('mark_exported');

        $export = SageSaariExportService::generateSageExport($hospitalId, $startDate, $endDate, false);

        if ($markExported && !empty($export['entry_ids'])) {
            SageSaariExportService::markAsExported($export['entry_ids']);
        }

        $filename = 'EXPORT_SAGE_SAARI_' . Carbon::now()->format('Ymd_His') . '.txt';

        return Response::make($export['content'], 200, [
            'Content-Type' => 'text/plain; charset=ISO-8859-1',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Synchroniser / Recalculer les écritures
     */
    public function sync(Request $request)
    {
        $hospitalId = $this->getHospitalId();
        AccountingService::syncExistingOperations($hospitalId);

        return redirect()->back()->with('success', 'Toutes les écritures comptables ont été synchronisées avec succès.');
    }

    /**
     * Liste des dépenses / charges
     */
    public function expenses(Request $request)
    {
        $title = 'Comptabilité | Dépenses & Charges';
        $hospitalId = $this->getHospitalId();
        AccountingService::initHospitalAccounting($hospitalId);

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $expenses = Expense::with('accountant.user')
            ->where('hospital_id', $hospitalId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $totalExpenses = Expense::where('hospital_id', $hospitalId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');

        // Comptes de charges disponibles
        $chargeAccounts = ChartOfAccount::where('hospital_id', $hospitalId)
            ->where('type', 'charge')
            ->orderBy('account_number', 'asc')
            ->get();

        if ($chargeAccounts->isEmpty()) {
            $chargeAccounts = ChartOfAccount::where('hospital_id', $hospitalId)->get();
        }

        return view('users.accountant.accounting.expenses', compact(
            'title', 'expenses', 'totalExpenses', 'chargeAccounts', 'startDate', 'endDate'
        ));
    }

    /**
     * Téléchargement en PDF de l'historique des dépenses
     */
    public function expensesPdf(Request $request)
    {
        $hospitalId = $this->getHospitalId();
        $comptable = Accountant::with(['user', 'hospital'])->where('user_id', Auth::user()->id)->first();
        $hospital = Hospital::find($hospitalId);

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $expenses = Expense::with('accountant.user')
            ->where('hospital_id', $hospitalId)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalExpenses = $expenses->sum('amount');

        $pdf = Pdf::loadView('users.accountant.accounting.expenses_pdf', compact(
            'expenses', 'totalExpenses', 'startDate', 'endDate', 'comptable', 'hospital'
        ));

        $pdf->setPaper('A4', 'landscape');

        $filename = 'ETAT_DEPENSES_' . Carbon::parse($startDate)->format('Ymd') . '_' . Carbon::parse($endDate)->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Enregistrer une nouvelle dépense
     */
    public function storeExpense(Request $request)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->back()->with('error', 'Action non autorisée. En tant qu\'administrateur, vous êtes en mode consultation seule.');
        }

        $hospitalId = $this->getHospitalId();
        $accountant = Accountant::where('user_id', Auth::user()->id)->first();

        $request->validate([
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'label' => 'required|string|max:255',
            'account_number' => 'required|string',
            'mode_paiement' => 'required|string',
            'beneficiaire' => 'nullable|string|max:255',
            'piece_number' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $expense = Expense::create([
            'hospital_id' => $hospitalId,
            'accountant_id' => $accountant ? $accountant->id : null,
            'expense_date' => $request->expense_date,
            'account_number' => trim($request->account_number),
            'label' => trim($request->label),
            'beneficiaire' => $request->beneficiaire ? trim($request->beneficiaire) : null,
            'mode_paiement' => $request->mode_paiement,
            'amount' => floatval($request->amount),
            'piece_number' => $request->piece_number ? trim($request->piece_number) : null,
            'description' => $request->description ? trim($request->description) : null,
        ]);

        // Générer l'écriture comptable en partie double
        AccountingService::recordExpenseEntry($expense);

        return redirect()->back()->with('success', 'Dépense enregistrée et comptabilisée avec succès.');
    }

    /**
     * Supprimer une dépense
     */
    public function deleteExpense($id)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->back()->with('error', 'Action non autorisée. En tant qu\'administrateur, vous êtes en mode consultation seule.');
        }

        $hospitalId = $this->getHospitalId();
        $expense = Expense::where('hospital_id', $hospitalId)->findOrFail($id);
        
        AccountingService::deleteExpenseEntry($expense->id);
        $expense->delete();

        return redirect()->back()->with('success', 'Dépense et écriture comptable supprimées.');
    }

    /**
     * Suivi des assurances et règlements / recouvrements
     */
    public function assurances(Request $request)
    {
        $title = 'Comptabilité | Suivi des Assurances & Recouvrement';
        $hospitalId = $this->getHospitalId();
        AccountingService::initHospitalAccounting($hospitalId);

        $assurancesList = TypeAssurance::where('hospital_id', $hospitalId)->get();
        $assurancesSummary = [];
        $grandTotalPriseEnCharge = 0;
        $grandTotalRegle = 0;
        $grandTotalReste = 0;

        foreach ($assurancesList as $assur) {
            // Prise en charge totale facturée (table assurances)
            $totalPriseEnCharge = Assurance::where('hospital_id', $hospitalId)
                ->where('type_assurance_id', $assur->id)
                ->sum('prix');

            // Montant total déjà recouvré / payé par l'assurance
            $totalRegle = InsuranceSettlement::where('hospital_id', $hospitalId)
                ->where('type_assurance_id', $assur->id)
                ->sum('amount');

            $reste = max(0, $totalPriseEnCharge - $totalRegle);

            $assurancesSummary[] = [
                'id' => $assur->id,
                'nom' => $assur->libelle ?? $assur->nom ?? 'Assurance',
                'total_prise_en_charge' => $totalPriseEnCharge,
                'total_regle' => $totalRegle,
                'reste_a_payer' => $reste,
            ];

            $grandTotalPriseEnCharge += $totalPriseEnCharge;
            $grandTotalRegle += $totalRegle;
            $grandTotalReste += $reste;
        }

        // Règlements récents avec auteur
        $settlements = InsuranceSettlement::with(['typeAssurance', 'accountant.user'])
            ->where('hospital_id', $hospitalId)
            ->orderBy('settlement_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('users.accountant.accounting.assurances', compact(
            'title', 'assurancesSummary', 'settlements', 'assurancesList',
            'grandTotalPriseEnCharge', 'grandTotalRegle', 'grandTotalReste'
        ));
    }

    /**
     * Téléchargement en PDF de l'état des assurances et règlements
     */
    public function assurancesPdf(Request $request)
    {
        $hospitalId = $this->getHospitalId();
        $comptable = Accountant::with(['user', 'hospital'])->where('user_id', Auth::user()->id)->first();
        $hospital = Hospital::find($hospitalId);

        $assurancesList = TypeAssurance::where('hospital_id', $hospitalId)->get();
        $assurancesSummary = [];
        $grandTotalPriseEnCharge = 0;
        $grandTotalRegle = 0;
        $grandTotalReste = 0;

        foreach ($assurancesList as $assur) {
            $totalPriseEnCharge = Assurance::where('hospital_id', $hospitalId)
                ->where('type_assurance_id', $assur->id)
                ->sum('prix');

            $totalRegle = InsuranceSettlement::where('hospital_id', $hospitalId)
                ->where('type_assurance_id', $assur->id)
                ->sum('amount');

            $reste = max(0, $totalPriseEnCharge - $totalRegle);

            $assurancesSummary[] = [
                'id' => $assur->id,
                'nom' => $assur->libelle ?? $assur->nom ?? 'Assurance',
                'total_prise_en_charge' => $totalPriseEnCharge,
                'total_regle' => $totalRegle,
                'reste_a_payer' => $reste,
            ];

            $grandTotalPriseEnCharge += $totalPriseEnCharge;
            $grandTotalRegle += $totalRegle;
            $grandTotalReste += $reste;
        }

        $settlements = InsuranceSettlement::with(['typeAssurance', 'accountant.user'])
            ->where('hospital_id', $hospitalId)
            ->orderBy('settlement_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $pdf = Pdf::loadView('users.accountant.accounting.assurances_pdf', compact(
            'assurancesSummary', 'settlements', 'grandTotalPriseEnCharge', 'grandTotalRegle', 'grandTotalReste', 'comptable', 'hospital'
        ));

        $pdf->setPaper('A4', 'landscape');

        $filename = 'ETAT_ASSURANCES_RECOUVREMENT_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Enregistrer un règlement reçu d'un assureur
     */
    public function storeSettlement(Request $request)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->back()->with('error', 'Action non autorisée. En tant qu\'administrateur, vous êtes en mode consultation seule.');
        }

        $hospitalId = $this->getHospitalId();
        $accountant = Accountant::where('user_id', Auth::user()->id)->first();

        $request->validate([
            'type_assurance_id' => 'required|exists:type_assurances,id',
            'settlement_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'mode_paiement' => 'required|string',
            'reference_piece' => 'nullable|string|max:100',
            'note' => 'nullable|string',
        ]);

        $settlement = InsuranceSettlement::create([
            'hospital_id' => $hospitalId,
            'type_assurance_id' => $request->type_assurance_id,
            'accountant_id' => $accountant ? $accountant->id : null,
            'settlement_date' => $request->settlement_date,
            'amount' => floatval($request->amount),
            'mode_paiement' => $request->mode_paiement,
            'reference_piece' => $request->reference_piece ? trim($request->reference_piece) : null,
            'note' => $request->note ? trim($request->note) : null,
        ]);

        // Générer l'écriture comptable en partie double
        AccountingService::recordSettlementEntry($settlement);

        return redirect()->back()->with('success', 'Règlement assurance enregistré et comptabilisé avec succès.');
    }

    /**
     * Supprimer un règlement d'assurance
     */
    public function deleteSettlement($id)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->back()->with('error', 'Action non autorisée. En tant qu\'administrateur, vous êtes en mode consultation seule.');
        }

        $hospitalId = $this->getHospitalId();
        $settlement = InsuranceSettlement::where('hospital_id', $hospitalId)->findOrFail($id);

        AccountingService::deleteSettlementEntry($settlement->id);
        $settlement->delete();

        return redirect()->back()->with('success', 'Règlement assurance et écriture comptable supprimés.');
    }
}
