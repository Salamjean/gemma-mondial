<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\AccountingJournal;
use App\Models\AccountingEntry;
use App\Models\AccountingEntryLine;
use App\Models\Payment;
use App\Models\Admission;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\InsuranceSettlement;
use App\Models\DrugSale;
use App\Models\BankDeposit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Initialise le plan comptable et les journaux par défaut (SYSCOHADA) pour un hôpital.
     */
    public static function initHospitalAccounting($hospitalId)
    {
        // 1. Journaux
        $defaultJournals = [
            ['code' => 'VTE', 'label' => 'Journal des Ventes & Prestations'],
            ['code' => 'CAI', 'label' => 'Journal de Caisse Principale'],
            ['code' => 'BQ',  'label' => 'Journal de Banque'],
            ['code' => 'OD',  'label' => 'Journal des Opérations Diverses'],
        ];

        foreach ($defaultJournals as $j) {
            AccountingJournal::firstOrCreate(
                ['hospital_id' => $hospitalId, 'code' => $j['code']],
                ['label' => $j['label'], 'is_active' => true]
            );
        }

        // 2. Plan Comptable SYSCOHADA Santé
        $defaultAccounts = [
            ['account_number' => '706100', 'label' => 'Prestations Médicales - Consultations', 'type' => 'produit'],
            ['account_number' => '706200', 'label' => 'Prestations Médicales - Examens & Analyses', 'type' => 'produit'],
            ['account_number' => '706300', 'label' => 'Prestations Médicales - Hospitalisations & Soins', 'type' => 'produit'],
            ['account_number' => '701100', 'label' => 'Ventes Pharmacie & Médicaments', 'type' => 'produit'],
            ['account_number' => '411100', 'label' => 'Clients - Patients (Part Directe)', 'type' => 'client'],
            ['account_number' => '411200', 'label' => 'Clients - Assurances & Tiers Payants', 'type' => 'tiers'],
            ['account_number' => '531100', 'label' => 'Caisse Principale Espèces', 'type' => 'tresorerie'],
            ['account_number' => '512100', 'label' => 'Banque', 'type' => 'tresorerie'],
            ['account_number' => '511200', 'label' => 'Chèques à encaisser', 'type' => 'tresorerie'],
            ['account_number' => '518100', 'label' => 'Trésorerie Mobile Money (Wave, Orange, MTN)', 'type' => 'tresorerie'],
            ['account_number' => '585000', 'label' => 'Virements Internes de Fonds', 'type' => 'tresorerie'],
            ['account_number' => '455100', 'label' => 'Compte Courant Associés / Apports', 'type' => 'tiers'],
            ['account_number' => '771000', 'label' => 'Subventions d\'Exploitation & Dons', 'type' => 'produit'],
            ['account_number' => '758000', 'label' => 'Autres Produits de Gestion Courante', 'type' => 'produit'],
            // 2.3 Charges d'Exploitation & Dépenses (Classe 6)
            ['account_number' => '601100', 'label' => 'Achats Médicaments & Produits Pharmaceutiques', 'type' => 'charge'],
            ['account_number' => '601200', 'label' => 'Achats Consommables Médicaux & Réactifs', 'type' => 'charge'],
            ['account_number' => '605100', 'label' => 'Fournitures de Bureau & Imprimés', 'type' => 'charge'],
            ['account_number' => '605200', 'label' => 'Eau & Électricité (CIE / SODECI)', 'type' => 'charge'],
            ['account_number' => '605300', 'label' => 'Carburant (Ambulance / Groupe Électrogène)', 'type' => 'charge'],
            ['account_number' => '613100', 'label' => 'Locations Immobilières & Loyers', 'type' => 'charge'],
            ['account_number' => '618100', 'label' => 'Entretien & Maintenance Matériel Médical', 'type' => 'charge'],
            ['account_number' => '622100', 'label' => 'Honoraires & Prestations Extérieures', 'type' => 'charge'],
            ['account_number' => '624100', 'label' => 'Transports & Déplacements', 'type' => 'charge'],
            ['account_number' => '626100', 'label' => 'Télécoms, Internet & Frais Postaux', 'type' => 'charge'],
            ['account_number' => '627100', 'label' => 'Frais & Services Bancaires', 'type' => 'charge'],
            ['account_number' => '632100', 'label' => 'Impôts, Taxes & Droits Directs', 'type' => 'charge'],
            ['account_number' => '658100', 'label' => 'Charges Diverses de Gestion Courante', 'type' => 'charge'],
            ['account_number' => '661100', 'label' => 'Salaires & Rémunérations du Personnel', 'type' => 'charge'],
            ['account_number' => '661200', 'label' => 'Primes, Indemnités & Gratifications', 'type' => 'charge'],
            ['account_number' => '661300', 'label' => 'Vacations & Honoraires Médicaux', 'type' => 'charge'],
            ['account_number' => '664100', 'label' => 'Charges Sociales & Cotisations CNPS', 'type' => 'charge'],
        ];

        foreach ($defaultAccounts as $acc) {
            ChartOfAccount::firstOrCreate(
                ['hospital_id' => $hospitalId, 'account_number' => $acc['account_number']],
                [
                    'label' => $acc['label'],
                    'type' => $acc['type'],
                    'is_default' => true,
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Récupère le nom complet propre du patient.
     */
    public static function getPatientName($patient)
    {
        if (!$patient) return '';
        if ($patient->user && !empty(trim($patient->user->name ?? ''))) {
            $name = trim($patient->user->name);
            $prenom = trim($patient->user->prenom ?? '');
            return trim($name . ' ' . $prenom);
        }
        $nom = trim($patient->nom ?? '');
        $prenom = trim($patient->prenom ?? '');
        $full = trim($nom . ' ' . $prenom);
        return $full ?: ($patient->code_patient ?? 'Patient');
    }

    /**
     * Récupère l'intitulé réel de l'acte / prestation / consultation.
     */
    public static function getPrestationLibelle($admission)
    {
        if (!$admission) return 'Prestation Médicale';

        if (!empty(trim($admission->motif_consultation ?? ''))) {
            return trim($admission->motif_consultation);
        }

        if ($admission->prestationHospital) {
            $nom = $admission->prestationHospital->prestationService->nom ?? $admission->prestationHospital->nom ?? null;
            if ($nom) return trim($nom);
        }

        if ($admission->typeExamen) {
            $nom = $admission->typeExamen->libelle ?? $admission->typeExamen->nom ?? null;
            if ($nom) return trim($nom);
        }

        if ($admission->consultation && !empty($admission->consultation->motif)) {
            return trim($admission->consultation->motif);
        }

        if (!empty($admission->type_admission)) {
            return ucfirst(trim($admission->type_admission));
        }

        return 'Prestation Médicale';
    }

    /**
     * Obtenir le compte de trésorerie approprié selon le mode de paiement.
     */
    public static function getTreasuryAccount($modePaiement)
    {
        $mode = strtolower(trim($modePaiement ?? 'espece'));
        if (in_array($mode, ['wave', 'orange_money', 'orange', 'mtn', 'mtn_money', 'moov', 'moov_money', 'mobile', 'mobile_money'])) {
            return ['account_number' => '518100', 'label' => 'Trésorerie Mobile Money'];
        } elseif (in_array($mode, ['banque', 'virement', 'cheque', 'carte', 'cb'])) {
            return ['account_number' => '512100', 'label' => 'Banque'];
        } else {
            return ['account_number' => '531100', 'label' => 'Caisse Principale Espèces'];
        }
    }

    /**
     * Enregistrer une écriture comptable pour un paiement reçu.
     */
    public static function recordPaymentEntry($payment)
    {
        if (!$payment || !$payment->hospital_id) {
            return null;
        }

        $hospitalId = $payment->hospital_id;
        self::initHospitalAccounting($hospitalId);

        $amount = floatval($payment->amount ?? 0);
        if ($amount <= 0) {
            return null;
        }

        $mode = $payment->mode_paiement ?? 'espece';
        $treasury = self::getTreasuryAccount($mode);
        $journalCode = ($treasury['account_number'] == '531100') ? 'CAI' : (($treasury['account_number'] == '512100') ? 'BQ' : 'CAI');

        $journal = AccountingJournal::where('hospital_id', $hospitalId)->where('code', $journalCode)->first();
        $entryDate = $payment->created_at ? Carbon::parse($payment->created_at)->toDateString() : date('Y-m-d');
        $pieceNumber = $payment->reference_paiement ?? ('PAY-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT));

        // Déterminer le vrai libellé
        if ($payment->admission) {
            $prestation = self::getPrestationLibelle($payment->admission);
            $patientNom = self::getPatientName($payment->admission->patient);
            $libelle = $patientNom ? "{$prestation} - {$patientNom}" : $prestation;
        } elseif ($payment->hospitalisation) {
            $patientNom = self::getPatientName($payment->hospitalisation->patient ?? null);
            $libelle = $patientNom ? "Hospitalisation - {$patientNom}" : "Hospitalisation";
        } elseif ($payment->observation) {
            $patientNom = self::getPatientName($payment->observation->patient ?? null);
            $libelle = $patientNom ? "Observation - {$patientNom}" : "Observation";
        } else {
            $libelle = "Recette #" . $payment->id . " (" . ucfirst($mode) . ")";
        }

        return DB::transaction(function () use ($hospitalId, $journal, $journalCode, $entryDate, $pieceNumber, $payment, $libelle, $treasury, $amount) {
            $entry = AccountingEntry::updateOrCreate(
                [
                    'hospital_id' => $hospitalId,
                    'reference_type' => Payment::class,
                    'reference_id' => $payment->id,
                ],
                [
                    'journal_id' => $journal ? $journal->id : null,
                    'journal_code' => $journalCode,
                    'entry_date' => $entryDate,
                    'piece_number' => $pieceNumber,
                    'libelle' => $libelle,
                    'status' => 'valide',
                ]
            );

            // Reconstruire les lignes
            $entry->lines()->delete();

            // Ligne 1 : Débit Compte de Trésorerie (Caisse / Mobile Money / Banque)
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_number' => $treasury['account_number'],
                'account_label' => $treasury['label'],
                'libelle' => $libelle,
                'debit' => $amount,
                'credit' => 0,
            ]);

            // Ligne 2 : Crédit Compte Produit / Prestations (706100)
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_number' => '706100',
                'account_label' => 'Prestations Médicales',
                'third_party_code' => $payment->patient_id ? 'PAT-' . $payment->patient_id : null,
                'libelle' => $libelle,
                'debit' => 0,
                'credit' => $amount,
            ]);

            return $entry;
        });
    }

    /**
     * Enregistrer une écriture comptable pour une admission payée.
     */
    public static function recordAdmissionEntry($admission)
    {
        if (!$admission || !$admission->hospital_id) {
            return null;
        }

        $hospitalId = $admission->hospital_id;
        self::initHospitalAccounting($hospitalId);

        $isGtc = ($admission->mode_paiement === 'gtc' || $admission->type_admission === 'GTC (Gratuité)');
        $amount = floatval($admission->montant_patient ?? $admission->montant ?? 0);
        $montantNormal = floatval($admission->montant_normal ?? 0);

        if ($isGtc && $montantNormal <= 0 && $admission->prestationHospital) {
            $montantNormal = floatval($admission->prestationHospital->prix ?? 0);
        }

        if ($amount <= 0 && (!$isGtc || $montantNormal <= 0)) {
            return null;
        }

        $mode = $admission->mode_paiement ?? 'espece';
        $treasury = self::getTreasuryAccount($mode);
        $journalCode = $isGtc ? 'OD' : (($treasury['account_number'] == '531100') ? 'CAI' : (($treasury['account_number'] == '512100') ? 'BQ' : 'CAI'));

        $journal = AccountingJournal::where('hospital_id', $hospitalId)->where('code', $journalCode)->first();
        $entryDate = $admission->created_at ? Carbon::parse($admission->created_at)->toDateString() : date('Y-m-d');
        $pieceNumber = $admission->num_facture ?? $admission->reference_paiement ?? $admission->code_admission ?? ('ADM-' . str_pad($admission->id, 6, '0', STR_PAD_LEFT));
        
        $prestation = self::getPrestationLibelle($admission);
        $patientNom = self::getPatientName($admission->patient);
        $libelle = $isGtc ? ("[GTC] " . ($patientNom ? "{$prestation} - {$patientNom}" : $prestation)) : ($patientNom ? "{$prestation} - {$patientNom}" : $prestation);

        return DB::transaction(function () use ($hospitalId, $journal, $journalCode, $entryDate, $pieceNumber, $admission, $libelle, $treasury, $amount, $patientNom, $isGtc, $montantNormal) {
            $entry = AccountingEntry::updateOrCreate(
                [
                    'hospital_id' => $hospitalId,
                    'reference_type' => Admission::class,
                    'reference_id' => $admission->id,
                ],
                [
                    'journal_id' => $journal ? $journal->id : null,
                    'journal_code' => $journalCode,
                    'entry_date' => $entryDate,
                    'piece_number' => $pieceNumber,
                    'libelle' => $libelle,
                    'status' => 'valide',
                ]
            );

            $entry->lines()->delete();

            if ($isGtc) {
                // Écriture GTC : Débit Compte État GTC (411300) / Crédit Prestations Médicales GTC (706100)
                AccountingEntryLine::create([
                    'accounting_entry_id' => $entry->id,
                    'account_number' => '411300',
                    'account_label' => 'Clients État - Prise en charge GTC (Gratuité)',
                    'third_party_code' => $admission->patient_id ? 'PAT-' . $admission->patient_id : 'ETAT-GTC',
                    'libelle' => $libelle,
                    'debit' => $montantNormal,
                    'credit' => 0,
                ]);

                AccountingEntryLine::create([
                    'accounting_entry_id' => $entry->id,
                    'account_number' => '706100',
                    'account_label' => 'Prestations Médicales (Gratuité Ciblée GTC)',
                    'third_party_code' => 'ETAT-GTC',
                    'libelle' => $libelle,
                    'debit' => 0,
                    'credit' => $montantNormal,
                ]);
            } else {
                // Débit Trésorerie
                AccountingEntryLine::create([
                    'accounting_entry_id' => $entry->id,
                    'account_number' => $treasury['account_number'],
                    'account_label' => $treasury['label'],
                    'libelle' => $libelle,
                    'debit' => $amount,
                    'credit' => 0,
                ]);

                // Crédit Prestations
                AccountingEntryLine::create([
                    'accounting_entry_id' => $entry->id,
                    'account_number' => '706100',
                    'account_label' => 'Prestations Médicales',
                    'third_party_code' => $admission->patient_id ? 'PAT-' . $admission->patient_id : null,
                    'libelle' => $libelle,
                    'debit' => 0,
                    'credit' => $amount,
                ]);

                // Part assurance si présente
                $montantAssurance = floatval($admission->montant_assurance ?? 0);
                if ($montantAssurance > 0) {
                    $codeAssurance = $admission->typeAssurance ? ('ASSUR-' . $admission->typeAssurance->id) : 'ASSUR-GEN';
                    $nomAssurance = $admission->typeAssurance ? ($admission->typeAssurance->libelle ?? 'Assurance') : 'Assurance';

                    AccountingEntryLine::create([
                        'accounting_entry_id' => $entry->id,
                        'account_number' => '411200',
                        'account_label' => 'Clients - Assurances & Tiers Payants (' . $nomAssurance . ')',
                        'third_party_code' => $codeAssurance,
                        'libelle' => "Prise en charge " . $nomAssurance . " - " . ($patientNom ?: 'Patient'),
                        'debit' => $montantAssurance,
                        'credit' => 0,
                    ]);

                    AccountingEntryLine::create([
                        'accounting_entry_id' => $entry->id,
                        'account_number' => '706100',
                        'account_label' => 'Prestations Médicales (Part Assurance)',
                        'third_party_code' => $codeAssurance,
                        'libelle' => "Part prise en charge " . $nomAssurance,
                        'debit' => 0,
                        'credit' => $montantAssurance,
                    ]);
                }
            }

            return $entry;
        });
    }

    /**
     * Enregistrer une écriture comptable pour une vente de médicaments (Pharmacie).
     */
    public static function recordDrugSaleEntry(DrugSale $drugSale)
    {
        if (!$drugSale || !$drugSale->hospital_id || $drugSale->status !== 'success') {
            return null;
        }

        $hospitalId = $drugSale->hospital_id;
        self::initHospitalAccounting($hospitalId);

        $amount = floatval($drugSale->price ?? 0);
        if ($amount <= 0) {
            return null;
        }

        $journalCode = 'CAI';
        $journal = AccountingJournal::where('hospital_id', $hospitalId)->where('code', $journalCode)->first();
        $entryDate = $drugSale->updated_at ? Carbon::parse($drugSale->updated_at)->toDateString() : ($drugSale->created_at ? Carbon::parse($drugSale->created_at)->toDateString() : date('Y-m-d'));
        $pieceNumber = 'PHARM-' . str_pad($drugSale->id, 6, '0', STR_PAD_LEFT);

        // Trouver le patient lié
        $patient = null;
        if ($drugSale->ordonnance && $drugSale->ordonnance->patient) {
            $patient = $drugSale->ordonnance->patient;
        } elseif ($drugSale->careRequested && $drugSale->careRequested->admission && $drugSale->careRequested->admission->patient) {
            $patient = $drugSale->careRequested->admission->patient;
        } elseif ($drugSale->hospitalisationDrugRequested && $drugSale->hospitalisationDrugRequested->hospitalisation && $drugSale->hospitalisationDrugRequested->hospitalisation->patient) {
            $patient = $drugSale->hospitalisationDrugRequested->hospitalisation->patient;
        }

        $patientNom = self::getPatientName($patient);
        $libelle = $patientNom ? "Vente Pharmacie - {$patientNom} (#{$drugSale->id})" : "Vente Pharmacie & Médicaments (#{$drugSale->id})";

        return DB::transaction(function () use ($hospitalId, $journal, $journalCode, $entryDate, $pieceNumber, $drugSale, $libelle, $amount, $patient) {
            $entry = AccountingEntry::updateOrCreate(
                [
                    'hospital_id' => $hospitalId,
                    'reference_type' => DrugSale::class,
                    'reference_id' => $drugSale->id,
                ],
                [
                    'journal_id' => $journal ? $journal->id : null,
                    'journal_code' => $journalCode,
                    'entry_date' => $entryDate,
                    'piece_number' => $pieceNumber,
                    'libelle' => $libelle,
                    'status' => 'valide',
                ]
            );

            $entry->lines()->delete();

            // Débit Caisse (531100)
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_number' => '531100',
                'account_label' => 'Caisse Principale Espèces',
                'libelle' => $libelle,
                'debit' => $amount,
                'credit' => 0,
            ]);

            // Crédit Ventes Pharmacie & Médicaments (701100)
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_number' => '701100',
                'account_label' => 'Ventes Pharmacie & Médicaments',
                'third_party_code' => ($patient && isset($patient->id)) ? 'PAT-' . $patient->id : null,
                'libelle' => $libelle,
                'debit' => 0,
                'credit' => $amount,
            ]);

            return $entry;
        });
    }

    /**
     * Synchroniser automatiquement les opérations existantes pour un hôpital.
     */
    public static function syncExistingOperations($hospitalId)
    {
        self::initHospitalAccounting($hospitalId);

        // Synchroniser les admissions
        $admissions = Admission::where('hospital_id', $hospitalId)->with(['patient.user', 'prestationHospital.prestationService', 'typeExamen', 'typeAssurance'])->get();
        foreach ($admissions as $admission) {
            self::recordAdmissionEntry($admission);
        }

        // Synchroniser les paiements
        $payments = Payment::where('hospital_id', $hospitalId)->with(['admission.patient.user', 'admission.prestationHospital', 'admission.typeExamen'])->get();
        foreach ($payments as $payment) {
            self::recordPaymentEntry($payment);
        }

        // Synchroniser les ventes de médicaments (Pharmacie)
        $drugSales = DrugSale::where('hospital_id', $hospitalId)
            ->where('status', 'success')
            ->with([
                'ordonnance.patient.user',
                'careRequested.admission.patient.user',
                'hospitalisationDrugRequested.hospitalisation.patient.user'
            ])
            ->get();
        foreach ($drugSales as $sale) {
            self::recordDrugSaleEntry($sale);
        }
    }

    /**
     * Calculer la Balance Générale des comptes.
     */
    public static function getGeneralBalance($hospitalId, $startDate = null, $endDate = null)
    {
        self::initHospitalAccounting($hospitalId);

        $query = DB::table('accounting_entry_lines')
            ->join('accounting_entries', 'accounting_entry_lines.accounting_entry_id', '=', 'accounting_entries.id')
            ->where('accounting_entries.hospital_id', $hospitalId)
            ->where('accounting_entries.status', 'valide');

        if ($startDate) {
            $query->where('accounting_entries.entry_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('accounting_entries.entry_date', '<=', $endDate);
        }

        $lines = $query->select(
            'accounting_entry_lines.account_number',
            DB::raw('MAX(accounting_entry_lines.account_label) as account_label'),
            DB::raw('SUM(accounting_entry_lines.debit) as total_debit'),
            DB::raw('SUM(accounting_entry_lines.credit) as total_credit')
        )
        ->groupBy('accounting_entry_lines.account_number')
        ->orderBy('accounting_entry_lines.account_number', 'asc')
        ->get();

        $balance = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $totalSoldeDebiteur = 0;
        $totalSoldeCrediteur = 0;

        foreach ($lines as $row) {
            $debit = floatval($row->total_debit);
            $credit = floatval($row->total_credit);
            $diff = $debit - $credit;

            $soldeDebiteur = $diff > 0 ? $diff : 0;
            $soldeCrediteur = $diff < 0 ? abs($diff) : 0;

            $balance[] = [
                'account_number' => $row->account_number,
                'account_label' => $row->account_label,
                'total_debit' => $debit,
                'total_credit' => $credit,
                'solde_debiteur' => $soldeDebiteur,
                'solde_crediteur' => $soldeCrediteur,
            ];

            $totalDebit += $debit;
            $totalCredit += $credit;
            $totalSoldeDebiteur += $soldeDebiteur;
            $totalSoldeCrediteur += $soldeCrediteur;
        }

        return [
            'accounts' => $balance,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'total_solde_debiteur' => $totalSoldeDebiteur,
            'total_solde_crediteur' => $totalSoldeCrediteur,
            'is_balanced' => round($totalDebit, 2) === round($totalCredit, 2),
        ];
    }

    /**
     * Enregistrer une écriture comptable pour une dépense / charge.
     */
    public static function recordExpenseEntry(Expense $expense)
    {
        if (!$expense || !$expense->hospital_id) {
            return null;
        }

        $hospitalId = $expense->hospital_id;
        self::initHospitalAccounting($hospitalId);

        $amount = floatval($expense->amount ?? 0);
        if ($amount <= 0) {
            return null;
        }

        $mode = $expense->mode_paiement ?? 'espece';
        $treasury = self::getTreasuryAccount($mode);
        $journalCode = ($treasury['account_number'] == '531100') ? 'CAI' : (($treasury['account_number'] == '512100') ? 'BQ' : 'CAI');

        $journal = AccountingJournal::where('hospital_id', $hospitalId)->where('code', $journalCode)->first();
        $entryDate = $expense->expense_date ? Carbon::parse($expense->expense_date)->toDateString() : date('Y-m-d');
        $pieceNumber = $expense->piece_number ?: ('DEP-' . str_pad($expense->id, 6, '0', STR_PAD_LEFT));
        $libelle = "Dépense: " . $expense->label . ($expense->beneficiaire ? " (Bénéficiaire: " . $expense->beneficiaire . ")" : "");
        $accountNumber = $expense->account_number ?: '601100';

        $chargeAccount = ChartOfAccount::where('hospital_id', $hospitalId)->where('account_number', $accountNumber)->first();
        $accountLabel = $chargeAccount ? $chargeAccount->label : 'Charges & Dépenses Diverses';

        return DB::transaction(function () use ($hospitalId, $journal, $journalCode, $entryDate, $pieceNumber, $expense, $libelle, $treasury, $amount, $accountNumber, $accountLabel) {
            $entry = AccountingEntry::updateOrCreate(
                [
                    'hospital_id' => $hospitalId,
                    'reference_type' => Expense::class,
                    'reference_id' => $expense->id,
                ],
                [
                    'journal_id' => $journal ? $journal->id : null,
                    'journal_code' => $journalCode,
                    'entry_date' => $entryDate,
                    'piece_number' => $pieceNumber,
                    'libelle' => $libelle,
                    'status' => 'valide',
                ]
            );

            $entry->lines()->delete();

            // Débit Compte de Charge (Classe 6)
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_number' => $accountNumber,
                'account_label' => $accountLabel,
                'libelle' => $libelle,
                'debit' => $amount,
                'credit' => 0,
            ]);

            // Crédit Compte de Trésorerie (Caisse / Banque / Mobile)
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_number' => $treasury['account_number'],
                'account_label' => $treasury['label'],
                'libelle' => $libelle,
                'debit' => 0,
                'credit' => $amount,
            ]);

            return $entry;
        });
    }

    /**
     * Supprimer l'écriture liée à une dépense.
     */
    public static function deleteExpenseEntry($expenseId)
    {
        AccountingEntry::where('reference_type', Expense::class)
            ->where('reference_id', $expenseId)
            ->delete();
    }

    /**
     * Enregistrer une écriture comptable pour un règlement / recouvrement d'assurance.
     */
    public static function recordSettlementEntry(InsuranceSettlement $settlement)
    {
        if (!$settlement || !$settlement->hospital_id) {
            return null;
        }

        $hospitalId = $settlement->hospital_id;
        self::initHospitalAccounting($hospitalId);

        $amount = floatval($settlement->amount ?? 0);
        if ($amount <= 0) {
            return null;
        }

        $mode = $settlement->mode_paiement ?? 'virement';
        $treasury = self::getTreasuryAccount($mode);
        $journalCode = ($treasury['account_number'] == '531100') ? 'CAI' : 'BQ';

        $journal = AccountingJournal::where('hospital_id', $hospitalId)->where('code', $journalCode)->first();
        $entryDate = $settlement->settlement_date ? Carbon::parse($settlement->settlement_date)->toDateString() : date('Y-m-d');
        $pieceNumber = $settlement->reference_piece ?: ('REG-ASSUR-' . str_pad($settlement->id, 6, '0', STR_PAD_LEFT));

        $nomAssurance = $settlement->typeAssurance ? ($settlement->typeAssurance->libelle ?? 'Assurance') : 'Assurance';
        $codeAssurance = 'ASSUR-' . $settlement->type_assurance_id;
        $libelle = "Règlement Recouvrement Assurance " . $nomAssurance . ($settlement->reference_piece ? " (Réf: " . $settlement->reference_piece . ")" : "");

        return DB::transaction(function () use ($hospitalId, $journal, $journalCode, $entryDate, $pieceNumber, $settlement, $libelle, $treasury, $amount, $nomAssurance, $codeAssurance) {
            $entry = AccountingEntry::updateOrCreate(
                [
                    'hospital_id' => $hospitalId,
                    'reference_type' => InsuranceSettlement::class,
                    'reference_id' => $settlement->id,
                ],
                [
                    'journal_id' => $journal ? $journal->id : null,
                    'journal_code' => $journalCode,
                    'entry_date' => $entryDate,
                    'piece_number' => $pieceNumber,
                    'libelle' => $libelle,
                    'status' => 'valide',
                ]
            );

            $entry->lines()->delete();

            // Débit Trésorerie (Banque ou Caisse)
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_number' => $treasury['account_number'],
                'account_label' => $treasury['label'],
                'libelle' => $libelle,
                'debit' => $amount,
                'credit' => 0,
            ]);

            // Crédit Compte Tiers Assurance (411200) -> Réduit la créance de l'assurance
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_number' => '411200',
                'account_label' => 'Clients - Assurances (' . $nomAssurance . ')',
                'third_party_code' => $codeAssurance,
                'libelle' => $libelle,
                'debit' => 0,
                'credit' => $amount,
            ]);

            return $entry;
        });
    }

    /**
     * Supprimer l'écriture liée à un règlement d'assurance.
     */
    public static function deleteSettlementEntry($settlementId)
    {
        AccountingEntry::where('reference_type', InsuranceSettlement::class)
            ->where('reference_id', $settlementId)
            ->delete();
    }

    /**
     * Enregistrer une écriture comptable pour un dépôt / versement bancaire multi-sources.
     */
    public static function recordBankDepositEntry(BankDeposit $deposit)
    {
        if (!$deposit || !$deposit->hospital_id) {
            return null;
        }

        $hospitalId = $deposit->hospital_id;
        self::initHospitalAccounting($hospitalId);

        $amount = floatval($deposit->amount ?? 0);
        if ($amount <= 0) {
            return null;
        }

        $journalCode = 'BQ';
        $journal = AccountingJournal::where('hospital_id', $hospitalId)->where('code', $journalCode)->first();
        $entryDate = $deposit->deposit_date ? Carbon::parse($deposit->deposit_date)->toDateString() : date('Y-m-d');
        $pieceNumber = $deposit->reference_piece ?: ('DEP-BQ-' . str_pad($deposit->id, 6, '0', STR_PAD_LEFT));

        $bankAccountNumber = $deposit->bank_account_number ?: '512100';
        $bankAccount = ChartOfAccount::where('hospital_id', $hospitalId)->where('account_number', $bankAccountNumber)->first();
        $bankLabel = $bankAccount ? $bankAccount->label : ($deposit->bank_name ? "Banque ({$deposit->bank_name})" : "Banque");

        $sourceAccountNumber = $deposit->source_account_number ?: '531100';
        $sourceAccount = ChartOfAccount::where('hospital_id', $hospitalId)->where('account_number', $sourceAccountNumber)->first();
        $sourceLabel = $sourceAccount ? $sourceAccount->label : 'Provenance Fonds';

        $libelle = $deposit->label ?: "Dépôt Bancaire - " . ($deposit->depositor_name ? "Par {$deposit->depositor_name}" : "Bordereau #{$pieceNumber}");

        return DB::transaction(function () use ($hospitalId, $journal, $journalCode, $entryDate, $pieceNumber, $deposit, $libelle, $amount, $bankAccountNumber, $bankLabel, $sourceAccountNumber, $sourceLabel) {
            $entry = AccountingEntry::updateOrCreate(
                [
                    'hospital_id' => $hospitalId,
                    'reference_type' => BankDeposit::class,
                    'reference_id' => $deposit->id,
                ],
                [
                    'journal_id' => $journal ? $journal->id : null,
                    'journal_code' => $journalCode,
                    'entry_date' => $entryDate,
                    'piece_number' => $pieceNumber,
                    'libelle' => $libelle,
                    'status' => 'valide',
                ]
            );

            $entry->lines()->delete();

            // 1. Débit Compte Banque (Augmentation du solde bancaire)
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_number' => $bankAccountNumber,
                'account_label' => $bankLabel,
                'libelle' => $libelle,
                'debit' => $amount,
                'credit' => 0,
            ]);

            // 2. Crédit Compte Source (Caisse, Mobile Money, Chèques, Apport, Assurance, etc.)
            AccountingEntryLine::create([
                'accounting_entry_id' => $entry->id,
                'account_number' => $sourceAccountNumber,
                'account_label' => $sourceLabel,
                'libelle' => $libelle,
                'debit' => 0,
                'credit' => $amount,
            ]);

            return $entry;
        });
    }

    /**
     * Supprimer l'écriture liée à un dépôt bancaire.
     */
    public static function deleteBankDepositEntry($depositId)
    {
        AccountingEntry::where('reference_type', BankDeposit::class)
            ->where('reference_id', $depositId)
            ->delete();
    }
}


