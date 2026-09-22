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
use App\Models\BankDeposit;
use App\Models\TypeAssurance;
use App\Models\Assurance;
use App\Models\Admission;
use App\Models\Hospital;
use App\Services\AccountingService;
use App\Services\SageSaariExportService;
use App\Services\AuditLogService;
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

        $search = trim($request->get('search', ''));
        $query = ChartOfAccount::where('hospital_id', $hospitalId);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('account_number', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('account_number', 'asc')->paginate(10);

        return view('users.accountant.accounting.plan_comptable', compact('title', 'accounts', 'search'));
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
        $search = trim($request->get('search', ''));

        $query = AccountingEntry::with('lines')
            ->where('hospital_id', $hospitalId)
            ->whereBetween('entry_date', [$startDate, $endDate]);

        if ($journalCode && $journalCode !== 'ALL') {
            $query->where('journal_code', $journalCode);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('piece_number', 'like', "%{$search}%")
                  ->orWhere('libelle', 'like', "%{$search}%")
                  ->orWhereHas('lines', function ($lq) use ($search) {
                      $lq->where('account_number', 'like', "%{$search}%")
                         ->orWhere('account_label', 'like', "%{$search}%")
                         ->orWhere('libelle', 'like', "%{$search}%");
                  });
            });
        }

        $entries = $query->orderBy('entry_date', 'desc')->orderBy('id', 'desc')->paginate(10);

        return view('users.accountant.accounting.journaux', compact(
            'title', 'journals', 'journalCode', 'startDate', 'endDate', 'entries', 'search'
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

        AuditLogService::log('TELECHARGEMENT_PDF', 'COMPTABILITE', "Téléchargement PDF du Grand Livre (Compte: {$accountNumber})", [
            'account_number' => $accountNumber,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

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

        AuditLogService::log('TELECHARGEMENT_PDF', 'COMPTABILITE', "Téléchargement PDF de la Balance Générale", [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

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
        $formatType = $request->get('format_type', 'standard_semicolon');
        $decimalSeparator = $request->get('decimal_separator', '.');
        $journalMapping = [
            'CAI' => $request->get('journal_caisse', 'CAIS'),
            'BQ'  => $request->get('journal_banque', 'BQ1'),
            'OD'  => $request->get('journal_od', 'OD'),
            'VTE' => $request->get('journal_ventes', 'VTE'),
        ];

        $export = SageSaariExportService::generateSageExport($hospitalId, $startDate, $endDate, false, $formatType, $decimalSeparator, $journalMapping);

        if ($markExported && !empty($export['entry_ids'])) {
            SageSaariExportService::markAsExported($export['entry_ids']);
        }

        $extension = 'txt';
        $contentType = 'text/plain; charset=windows-1252';

        if ($formatType === 'sage100_pnm') {
            $extension = 'pnm';
        } elseif ($formatType === 'excel_csv') {
            $extension = 'csv';
            $contentType = 'text/csv; charset=windows-1252';
        }

        $filename = 'EXPORT_COMPTABLE_' . Carbon::now()->format('Ymd_His') . '.' . $extension;

        // Audit Trail
        AuditLogService::log('EXPORT_SAGE', 'COMPTABILITE', "Exportation de {$export['count_entries']} écritures pour Sage 100 (.{$extension})", [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'format_type' => $formatType,
            'count' => $export['count_entries']
        ]);

        // Conversion sécurisée en Windows-1252 (ANSI standard Sage)
        $content = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $export['content']);
        if ($content === false) {
            $content = mb_convert_encoding($export['content'], 'Windows-1252', 'UTF-8');
        }

        return Response::make($content, 200, [
            'Content-Type' => $contentType,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Téléchargement des écritures au format Tableur Excel (.xls)
     */
    public function exportExcelDownload(Request $request)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->route('accountant.accounting.dashboard')->with('error', 'Accès non autorisé.');
        }

        $hospitalId = $this->getHospitalId();
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $markExported = $request->has('mark_exported');

        $query = AccountingEntry::with(['lines', 'journal'])
            ->where('hospital_id', $hospitalId)
            ->where('status', 'valide');

        if ($startDate) {
            $query->where('entry_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('entry_date', '<=', $endDate);
        }

        $entries = $query->orderBy('entry_date', 'asc')->orderBy('id', 'asc')->get();

        if ($markExported) {
            $entryIds = $entries->pluck('id')->toArray();
            SageSaariExportService::markAsExported($entryIds);
        }

        $hospital = \App\Models\Hospital::find($hospitalId);
        $hospitalName = $hospital ? $hospital->nom : 'Etablissement de Sante';
        $periodText = ($startDate && $endDate) ? "Du " . Carbon::parse($startDate)->format('d/m/Y') . " au " . Carbon::parse($endDate)->format('d/m/Y') : "Toutes les dates";

        $totalDebitGlobal = 0;
        $totalCreditGlobal = 0;

        // Construction du tableau HTML Spreadsheet pour Excel
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $html .= '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $html .= '<style>';
        $html .= 'body { font-family: Calibri, Arial, sans-serif; }';
        $html .= 'table { border-collapse: collapse; width: 100%; }';
        $html .= 'th { background-color: #1e40af; color: #ffffff; font-weight: bold; border: 1px solid #cbd5e1; padding: 10px; text-align: center; }';
        $html .= 'td { border: 1px solid #e2e8f0; padding: 8px; font-size: 13px; }';
        $html .= '.text-center { text-align: center; }';
        $html .= '.text-end { text-align: right; }';
        $html .= '.fw-bold { font-weight: bold; }';
        $html .= '.bg-total { background-color: #f1f5f9; font-weight: bold; }';
        $html .= '</style></head><body>';

        $html .= '<h2>' . htmlspecialchars($hospitalName) . ' - JOURNAL DES ECRITURES COMPTABLES</h2>';
        $html .= '<p><strong>Periode :</strong> ' . htmlspecialchars($periodText) . ' | <strong>Date generation :</strong> ' . Carbon::now()->format('d/m/Y H:i') . ' | <strong>Nb ecritures :</strong> ' . count($entries) . '</p>';

        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Date</th>';
        $html .= '<th>Journal</th>';
        $html .= '<th>N° Piece</th>';
        $html .= '<th>Compte General</th>';
        $html .= '<th>Intitule Compte</th>';
        $html .= '<th>Compte Tiers</th>';
        $html .= '<th>Libelle de l\'ecriture</th>';
        $html .= '<th>Sens</th>';
        $html .= '<th>Debit (FCFA)</th>';
        $html .= '<th>Credit (FCFA)</th>';
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        $journalMapping = [
            'CAI' => $request->get('journal_caisse', 'CAIS'),
            'BQ'  => $request->get('journal_banque', 'BQ1'),
            'OD'  => $request->get('journal_od', 'OD'),
            'VTE' => $request->get('journal_ventes', 'VTE'),
        ];

        foreach ($entries as $entry) {
            $dateFr = Carbon::parse($entry->entry_date)->format('d/m/Y');
            $rawJournal = $entry->journal_code ?: 'CAI';
            $journalCode = $journalMapping[$rawJournal] ?? $rawJournal;
            $piece = $entry->piece_number ?: ('P' . $entry->id);

            foreach ($entry->lines as $line) {
                $compteNum = $line->account_number;
                $compteNom = $line->account_label ?: '';
                $tiers = $line->third_party_code ?? '';
                $libelle = $line->libelle ?: $entry->libelle ?: '';
                $debit = floatval($line->debit ?? 0);
                $credit = floatval($line->credit ?? 0);
                $sens = $debit > 0 ? 'D' : 'C';

                $totalDebitGlobal += $debit;
                $totalCreditGlobal += $credit;

                $html .= '<tr>';
                $html .= '<td class="text-center">' . htmlspecialchars($dateFr) . '</td>';
                $html .= '<td class="text-center fw-bold">' . htmlspecialchars($journalCode) . '</td>';
                $html .= '<td class="text-center">' . htmlspecialchars($piece) . '</td>';
                $html .= '<td class="text-center fw-bold" style="mso-number-format:\'@\';">' . htmlspecialchars($compteNum) . '</td>';
                $html .= '<td>' . htmlspecialchars($compteNom) . '</td>';
                $html .= '<td class="text-center">' . htmlspecialchars($tiers) . '</td>';
                $html .= '<td>' . htmlspecialchars($libelle) . '</td>';
                $html .= '<td class="text-center fw-bold">' . htmlspecialchars($sens) . '</td>';
                $html .= '<td class="text-end">' . number_format($debit, 2, '.', '') . '</td>';
                $html .= '<td class="text-end">' . number_format($credit, 2, '.', '') . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '<tr class="bg-total">';
        $html .= '<td colspan="8" class="text-end fw-bold" style="font-size: 14px;">TOTAL GENERAL :</td>';
        $html .= '<td class="text-end fw-bold" style="font-size: 14px; color: #1e40af;">' . number_format($totalDebitGlobal, 2, '.', '') . '</td>';
        $html .= '<td class="text-end fw-bold" style="font-size: 14px; color: #1e40af;">' . number_format($totalCreditGlobal, 2, '.', '') . '</td>';
        $html .= '</tr>';

        $html .= '</tbody></table></body></html>';

        $filename = 'JOURNAL_ECRITURES_EXCEL_' . Carbon::now()->format('Ymd_His') . '.xls';

        // Audit Trail
        AuditLogService::log('EXPORT_EXCEL', 'COMPTABILITE', "Exportation des écritures comptables au format Excel (.xls) - " . count($entries) . " écritures", [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'count' => count($entries),
            'total_debit' => $totalDebitGlobal,
            'total_credit' => $totalCreditGlobal
        ]);

        return Response::make($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
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
        $search = trim($request->get('search', ''));

        $query = Expense::with('accountant.user')
            ->where('hospital_id', $hospitalId)
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                  ->orWhere('beneficiaire', 'like', "%{$search}%")
                  ->orWhere('piece_number', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        $totalExpenses = (clone $query)->sum('amount');

        $expenses = $query->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Comptes de charges disponibles
        $chargeAccounts = ChartOfAccount::where('hospital_id', $hospitalId)
            ->where('type', 'charge')
            ->orderBy('account_number', 'asc')
            ->get();

        if ($chargeAccounts->isEmpty()) {
            $chargeAccounts = ChartOfAccount::where('hospital_id', $hospitalId)->get();
        }

        return view('users.accountant.accounting.expenses', compact(
            'title', 'expenses', 'totalExpenses', 'chargeAccounts', 'startDate', 'endDate', 'search'
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

        AuditLogService::log('TELECHARGEMENT_PDF', 'COMPTABILITE', "Téléchargement PDF de l'État des Dépenses ({$expenses->count()} dépenses)", [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total' => $totalExpenses
        ]);

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

        AuditLogService::log('ENREGISTREMENT', 'DEPENSE', "Enregistrement d'une dépense: {$expense->label} (" . number_format($expense->amount, 0, ',', ' ') . " FCFA)", [
            'expense_id' => $expense->id,
            'amount' => $expense->amount,
            'beneficiaire' => $expense->beneficiaire,
            'account_number' => $expense->account_number
        ]);

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
        $label = $expense->label;
        $amount = $expense->amount;
        
        AccountingService::deleteExpenseEntry($expense->id);
        $expense->delete();

        AuditLogService::log('SUPPRESSION', 'DEPENSE', "Suppression de la dépense: {$label} (" . number_format($amount, 0, ',', ' ') . " FCFA)", [
            'expense_id' => $id,
            'amount' => $amount
        ]);

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

        $allAssurances = TypeAssurance::where('hospital_id', $hospitalId)->get();
        $grandTotalPriseEnCharge = 0;
        $grandTotalRegle = 0;
        $grandTotalReste = 0;

        foreach ($allAssurances as $assur) {
            $totalPriseEnCharge = Assurance::where('hospital_id', $hospitalId)
                ->where('type_assurance_id', $assur->id)
                ->sum('prix');

            $totalRegle = InsuranceSettlement::where('hospital_id', $hospitalId)
                ->where('type_assurance_id', $assur->id)
                ->sum('amount');

            $reste = max(0, $totalPriseEnCharge - $totalRegle);
            $grandTotalPriseEnCharge += $totalPriseEnCharge;
            $grandTotalRegle += $totalRegle;
            $grandTotalReste += $reste;
        }

        // Pagination à 3 de l'État des Créances par Assureur
        $assurancesList = TypeAssurance::where('hospital_id', $hospitalId)
            ->orderBy('libelle', 'asc')
            ->paginate(3, ['*'], 'page_assurances');

        $assurancesSummary = [];

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
                'nom' => $assur->libelle ?? 'Assurance',
                'total_prise_en_charge' => $totalPriseEnCharge,
                'total_regle' => $totalRegle,
                'reste_a_payer' => $reste,
            ];
        }

        // Règlements récents avec auteur
        $search = trim($request->get('search', ''));
        $querySettlements = InsuranceSettlement::with(['typeAssurance', 'accountant.user'])
            ->where('hospital_id', $hospitalId);

        if ($search !== '') {
            $querySettlements->where(function ($q) use ($search) {
                $q->where('reference_piece', 'like', "%{$search}%")
                  ->orWhere('note', 'like', "%{$search}%")
                  ->orWhereHas('typeAssurance', function ($aq) use ($search) {
                      $aq->where('libelle', 'like', "%{$search}%")
                         ->orWhere('reference', 'like', "%{$search}%");
                  });
            });
        }

        $settlements = $querySettlements->orderBy('settlement_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('users.accountant.accounting.assurances', compact(
            'title', 'assurancesSummary', 'settlements', 'assurancesList',
            'grandTotalPriseEnCharge', 'grandTotalRegle', 'grandTotalReste', 'search'
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

        AuditLogService::log('TELECHARGEMENT_PDF', 'ASSURANCE', "Téléchargement PDF de l'État des Assurances & Recouvrements", [
            'total_prise_en_charge' => $grandTotalPriseEnCharge,
            'total_regle' => $grandTotalRegle,
            'reste_a_payer' => $grandTotalReste
        ]);

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

        AuditLogService::log('ENREGISTREMENT', 'ASSURANCE', "Enregistrement d'un règlement d'assurance (" . number_format($settlement->amount, 0, ',', ' ') . " FCFA)", [
            'settlement_id' => $settlement->id,
            'amount' => $settlement->amount,
            'mode_paiement' => $settlement->mode_paiement
        ]);

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
        $amount = $settlement->amount;

        AccountingService::deleteSettlementEntry($settlement->id);
        $settlement->delete();

        AuditLogService::log('SUPPRESSION', 'ASSURANCE', "Suppression d'un règlement d'assurance (" . number_format($amount, 0, ',', ' ') . " FCFA)", [
            'settlement_id' => $id,
            'amount' => $amount
        ]);

        return redirect()->back()->with('success', 'Règlement assurance et écriture comptable supprimés.');
    }

    /**
     * Gestion des dépôts et versements bancaires multi-sources
     */
    public function deposits(Request $request)
    {
        $title = 'Comptabilité | Dépôts & Versements Bancaires';
        $hospitalId = $this->getHospitalId();
        AccountingService::initHospitalAccounting($hospitalId);

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $selectedSource = $request->get('source_type');
        $selectedBank = $request->get('bank_account_number');
        $search = trim($request->get('search', ''));

        $query = BankDeposit::where('hospital_id', $hospitalId)
            ->whereBetween('deposit_date', [$startDate, $endDate]);

        if ($selectedSource) {
            $query->where('source_type', $selectedSource);
        }
        if ($selectedBank) {
            $query->where('bank_account_number', $selectedBank);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reference_piece', 'like', "%{$search}%")
                  ->orWhere('depositor_name', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('bank_account_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $totalDeposits = (clone $query)->sum('amount');

        $deposits = $query->orderBy('deposit_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);
        
        $today = Carbon::today()->toDateString();
        $todayDeposits = BankDeposit::where('hospital_id', $hospitalId)
            ->whereDate('deposit_date', $today)
            ->sum('amount');

        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
        $monthDeposits = BankDeposit::where('hospital_id', $hospitalId)
            ->whereBetween('deposit_date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // Récupérer les comptes disponibles
        $accounts = ChartOfAccount::where('hospital_id', $hospitalId)->where('is_active', true)->orderBy('account_number')->get();
        
        // Tous les comptes de trésorerie pouvant recevoir des fonds (Banques 512xxx, Mobile Money 518xxx, Caisse 531xxx, etc.)
        $bankAccounts = $accounts->where('type', 'tresorerie')->values();
        if ($bankAccounts->isEmpty()) {
            $bankAccounts = $accounts->whereIn('account_number', ['512100', '518100', '531100'])->values();
        }

        return view('users.accountant.accounting.deposits', compact(
            'title', 'deposits', 'totalDeposits', 'todayDeposits', 'monthDeposits',
            'startDate', 'endDate', 'selectedSource', 'selectedBank', 'accounts', 'bankAccounts'
        ));
    }

    /**
     * Enregistrer un nouveau dépôt bancaire
     */
    public function storeDeposit(Request $request)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->back()->with('error', 'Action non autorisée. En tant qu\'administrateur, vous êtes en mode consultation seule.');
        }

        $hospitalId = $this->getHospitalId();
        $accountant = Accountant::where('user_id', Auth::user()->id)->first();

        $request->validate([
            'deposit_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'bank_account_number' => 'required|string',
            'source_type' => 'required|string',
            'source_account_number' => 'required|string',
            'mode_depot' => 'required|string',
            'label' => 'required|string|max:255',
            'reference_piece' => 'nullable|string|max:100',
            'depositor_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $deposit = BankDeposit::create([
            'hospital_id' => $hospitalId,
            'accountant_id' => $accountant ? $accountant->id : null,
            'deposit_date' => $request->deposit_date,
            'bank_account_number' => trim($request->bank_account_number),
            'bank_name' => $request->bank_name ? trim($request->bank_name) : null,
            'source_type' => $request->source_type,
            'source_account_number' => trim($request->source_account_number),
            'mode_depot' => $request->mode_depot,
            'amount' => floatval($request->amount),
            'reference_piece' => $request->reference_piece ? trim($request->reference_piece) : null,
            'depositor_name' => $request->depositor_name ? trim($request->depositor_name) : null,
            'label' => trim($request->label),
            'description' => $request->description ? trim($request->description) : null,
        ]);

        // Générer l'écriture comptable en partie double
        AccountingService::recordBankDepositEntry($deposit);

        AuditLogService::log('ENREGISTREMENT', 'BANQUE', "Enregistrement d'un versement/dépôt bancaire: {$deposit->label} (" . number_format($deposit->amount, 0, ',', ' ') . " FCFA)", [
            'deposit_id' => $deposit->id,
            'amount' => $deposit->amount,
            'bank_account' => $deposit->bank_account_number,
            'source_type' => $deposit->source_type
        ]);

        return redirect()->back()->with('success', 'Dépôt bancaire enregistré et comptabilisé avec succès.');
    }

    /**
     * Supprimer un dépôt bancaire
     */
    public function deleteDeposit($id)
    {
        if (Auth::user()->role_as !== 'accountant') {
            return redirect()->back()->with('error', 'Action non autorisée. En tant qu\'administrateur, vous êtes en mode consultation seule.');
        }

        $hospitalId = $this->getHospitalId();
        $deposit = BankDeposit::where('hospital_id', $hospitalId)->findOrFail($id);
        $label = $deposit->label;
        $amount = $deposit->amount;

        AccountingService::deleteBankDepositEntry($deposit->id);
        $deposit->delete();

        AuditLogService::log('SUPPRESSION', 'BANQUE', "Suppression du dépôt bancaire: {$label} (" . number_format($amount, 0, ',', ' ') . " FCFA)", [
            'deposit_id' => $id,
            'amount' => $amount
        ]);

        return redirect()->back()->with('success', 'Dépôt bancaire et écriture comptable supprimés.');
    }

    /**
     * Générer le bordereau / reçu PDF d'un dépôt individuel
     */
    public function depositPdf($id)
    {
        $hospitalId = $this->getHospitalId();
        $deposit = BankDeposit::where('hospital_id', $hospitalId)->with('accountant.user')->findOrFail($id);
        $hospital = Hospital::find($hospitalId);
        $comptable = Auth::user();

        $pdf = Pdf::loadView('users.accountant.accounting.deposit_single_pdf', compact(
            'deposit', 'hospital', 'comptable'
        ));

        $pdf->setPaper('A5', 'landscape');
        $filename = 'BORDEREAU_DEPOT_' . ($deposit->reference_piece ?: $deposit->id) . '.pdf';

        AuditLogService::log('TELECHARGEMENT_PDF', 'BANQUE', "Téléchargement du bordereau de versement bancaire N° " . ($deposit->reference_piece ?: $deposit->id), [
            'deposit_id' => $deposit->id,
            'amount' => $deposit->amount
        ]);

        return $pdf->download($filename);
    }

    /**
     * Exporter la liste des dépôts en PDF
     */
    public function depositsPdf(Request $request)
    {
        $hospitalId = $this->getHospitalId();
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());
        $selectedSource = $request->get('source_type');

        $query = BankDeposit::where('hospital_id', $hospitalId)
            ->whereBetween('deposit_date', [$startDate, $endDate]);

        if ($selectedSource) {
            $query->where('source_type', $selectedSource);
        }

        $deposits = $query->orderBy('deposit_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalDeposits = $deposits->sum('amount');
        $hospital = Hospital::find($hospitalId);
        $comptable = Auth::user();

        $pdf = Pdf::loadView('users.accountant.accounting.deposits_pdf', compact(
            'deposits', 'totalDeposits', 'startDate', 'endDate', 'comptable', 'hospital'
        ));

        $pdf->setPaper('A4', 'landscape');
        $filename = 'ETAT_DEPOTS_BANCAIRES_' . Carbon::parse($startDate)->format('Ymd') . '_' . Carbon::parse($endDate)->format('Ymd') . '.pdf';

        AuditLogService::log('TELECHARGEMENT_PDF', 'BANQUE', "Téléchargement PDF de l'État des Dépôts Bancaires ({$deposits->count()} dépôts)", [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total' => $totalDeposits
        ]);

        return $pdf->download($filename);
    }
}
