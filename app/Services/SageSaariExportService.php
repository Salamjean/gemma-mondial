<?php

namespace App\Services;

use App\Models\AccountingEntry;
use App\Models\AccountingEntryLine;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SageSaariExportService
{
    /**
     * Nettoyage strict des chaînes pour Sage 100 i7 (ASCII / sans accents / sans séparateurs)
     */
    public static function sanitizeSageString($string, $maxLength = 35)
    {
        if (empty($string)) {
            return '';
        }
        $unwanted = [
            'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C',
            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
            'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a',
            'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i',
            'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u',
            'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', '\''=>' ', '"'=>' ', ';'=>' ', "\t"=>' ', "\r"=>'', "\n"=>''
        ];
        $str = strtr($string, $unwanted);
        $str = preg_replace('/[^A-Za-z0-9 \-\_\.\/]/', ' ', $str);
        $str = preg_replace('/\s+/', ' ', trim($str));
        return mb_substr($str, 0, $maxLength);
    }

    /**
     * Génère le contenu d'export au format standard Sage 100 Comptabilité i7
     */
    public static function generateSageExport($hospitalId, $startDate = null, $endDate = null, $onlyUnexported = false, $formatType = 'sage100_standard', $decimalSeparator = '.', $journalMapping = [])
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
        $exportedEntryIds = [];
        $delimiter = ($formatType === 'sage100_tab') ? "\t" : ";";

        // Si format Excel CSV, ajouter la ligne d'en-tête de colonnes
        if ($formatType === 'excel_csv') {
            $lines[] = "Journal;Date;Numero_Piece;Compte_General;Compte_Tiers;Libelle;Sens;Montant;Debit;Credit";
        }

        // Si format d'échange natif Sage PNM (#FLG 000)
        if ($formatType === 'sage100_pnm') {
            $lines[] = "#FLG 000";
            $lines[] = "#VER 10";
            $lines[] = "#DAF " . Carbon::now()->format('dmy');
        }

        foreach ($entries as $entry) {
            $exportedEntryIds[] = $entry->id;
            $rawJournal = $entry->journal_code ?: 'CAI';
            $mappedJournal = $journalMapping[$rawJournal] ?? $rawJournal;
            $journal = substr(preg_replace('/[^A-Za-z0-9]/', '', $mappedJournal), 0, 6);
            $dateSage = ($formatType === 'excel_csv') ? Carbon::parse($entry->entry_date)->format('d/m/Y') : Carbon::parse($entry->entry_date)->format('dmy');
            $piece = substr(preg_replace('/[^A-Za-z0-9\-_]/', '', $entry->piece_number ?: ('P' . $entry->id)), 0, 13);
            
            foreach ($entry->lines as $line) {
                $compteGeneral = substr(preg_replace('/[^0-9]/', '', $line->account_number), 0, 13);
                $compteTiers = substr(preg_replace('/[^A-Za-z0-9\-_]/', '', $line->third_party_code ?? ''), 0, 17);
                
                $libelleRaw = $line->libelle ?: $entry->libelle ?: 'Ecriture comptable';
                $libelle = self::sanitizeSageString($libelleRaw, 35);

                $debitVal = floatval($line->debit ?? 0);
                $creditVal = floatval($line->credit ?? 0);

                $debit = number_format($debitVal, 2, $decimalSeparator, '');
                $credit = number_format($creditVal, 2, $decimalSeparator, '');

                if ($formatType === 'excel_csv') {
                    $sens = $debitVal > 0 ? 'D' : 'C';
                    $montantVal = $debitVal > 0 ? $debitVal : $creditVal;
                    $montant = number_format($montantVal, 2, $decimalSeparator, '');
                    $lines[] = implode($delimiter, [$journal, $dateSage, $piece, $compteGeneral, $compteTiers, $libelle, $sens, $montant, $debit, $credit]);
                } elseif ($formatType === 'sage100_standard' || $formatType === 'sage100_pnm') {
                    // Format officiel Sage 100 : Journal;Date(JJMMAA);Piece;CompteGeneral;CompteTiers;Libelle;Sens(D/C);Montant
                    $sens = $debitVal > 0 ? 'D' : 'C';
                    $montantVal = $debitVal > 0 ? $debitVal : $creditVal;
                    $montant = number_format($montantVal, 2, $decimalSeparator, '');
                    $lines[] = implode($delimiter, [$journal, $dateSage, $piece, $compteGeneral, $compteTiers, $libelle, $sens, $montant]);
                } elseif ($formatType === 'sage100_debit_credit' || $formatType === 'sage100_tab') {
                    // Format délimité Débit/Crédit Sage 100 : Journal;Date(JJMMAA);Piece;CompteGeneral;CompteTiers;Libelle;Debit;Credit
                    $lines[] = implode($delimiter, [$journal, $dateSage, $piece, $compteGeneral, $compteTiers, $libelle, $debit, $credit]);
                } else {
                    // Format par défaut
                    $lines[] = implode($delimiter, [$journal, $dateSage, $piece, $compteGeneral, $compteTiers, $libelle, $debit, $credit]);
                }
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
