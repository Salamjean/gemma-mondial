<?php

namespace App\Services;

use App\Models\AccountingEntry;
use App\Models\AccountingEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SageSaariExportService
{
    /**
     * Génère le contenu d'export au format standard Sage SAARI (Sage 100)
     * Format : Journal;Date(JJMMAA);CompteGénéral;CompteTiers;NumPièce;Libellé;Débit;Crédit
     */
    public static function generateSageExport($hospitalId, $startDate = null, $endDate = null, $onlyUnexported = false)
    {
        $query = AccountingEntry::with('lines')
            ->where('hospital_id', $hospitalId)
            ->where('status', 'valide');

        if ($startDate) {
            $query->where('entry_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('entry_date', '<=', $endDate);
        }
        if ($onlyUnexported) {
            $query->where('is_exported_sage', false);
        }

        $entries = $query->orderBy('entry_date', 'asc')->orderBy('id', 'asc')->get();

        $lines = [];
        // En-tête informatif pour le fichier
        $lines[] = "# EXPORT COMPTABLE SAGE SAARI - GEMMA SANTE";
        $lines[] = "# Date generation : " . Carbon::now()->format('d/m/Y H:i:s');
        $lines[] = "# Format : Journal;Date(JJMMAA);CompteGeneral;CompteTiers;NumeroPiece;Libelle;Debit;Credit";
        $lines[] = "";

        $exportedEntryIds = [];

        foreach ($entries as $entry) {
            $exportedEntryIds[] = $entry->id;
            $journal = $entry->journal_code ?: 'CAI';
            $dateSage = Carbon::parse($entry->entry_date)->format('dmy'); // JJMMAA standard Sage
            $piece = substr(preg_replace('/[^A-Za-z0-9\-_]/', '', $entry->piece_number ?: ('PIECE-' . $entry->id)), 0, 13);
            
            foreach ($entry->lines as $line) {
                $compteGeneral = trim($line->account_number);
                $compteTiers = trim($line->third_party_code ?? '');
                $libelle = substr(trim(str_replace(';', ',', $line->libelle ?: $entry->libelle)), 0, 35);
                $debit = number_format($line->debit, 2, '.', '');
                $credit = number_format($line->credit, 2, '.', '');

                // Format standard Sage SAARI séparateur point-virgule
                $lines[] = "{$journal};{$dateSage};{$compteGeneral};{$compteTiers};{$piece};{$libelle};{$debit};{$credit}";
            }
        }

        return [
            'content' => implode("\r\n", $lines),
            'count_entries' => count($entries),
            'entry_ids' => $exportedEntryIds,
        ];
    }

    /**
     * Marquer les écritures comme exportées vers Sage.
     */
    public static function markAsExported(array $entryIds)
    {
        if (empty($entryIds)) {
            return;
        }

        AccountingEntry::whereIn('id', $entryIds)->update([
            'is_exported_sage' => true,
            'exported_at' => Carbon::now(),
        ]);
    }
}
