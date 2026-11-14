<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\SubPrefecture;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Import en masse d'hôpitaux depuis un fichier Excel.
 *
 * Ordre des colonnes attendu :
 *  A Nom de l'hôpital | B Code | C Contact | D Localité | E District sanitaire
 *  F Direction générale (régionale) | G Email
 * Le mot de passe de chaque compte est fixé à DEFAULT_PASSWORD.
 */
class HospitalImportController extends Controller
{
    public const DEFAULT_PASSWORD = '1234';

    private const HEADERS = [
        "Nom de l'hôpital",
        'Code',
        'Contact',
        'Localité',
        'District Sanitaire',
        'Direction générale (régionale)',
        'Email',
    ];

    public function import(Request $request)
    {
        $request->validate([
            'is_teleconsultation_active' => 'required|in:0,1',
            'is_ucp_fm' => 'required|in:0,1',
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'is_teleconsultation_active.required' => 'Indiquez si la téléconsultation sera active pour ces établissements.',
            'is_ucp_fm.required' => 'Indiquez si ces établissements appartiennent au UCP-FM.',
            'file.required' => 'Veuillez sélectionner un fichier Excel.',
            'file.mimes' => 'Le fichier doit être au format .xlsx, .xls ou .csv.',
        ]);

        $teleconsultation = $request->input('is_teleconsultation_active') === '1';
        $ucpFm = $request->input('is_ucp_fm') === '1';

        try {
            $rows = IOFactory::load($request->file('file')->getRealPath())
                ->getActiveSheet()
                ->toArray(null, true, true, false);
        } catch (\Throwable $e) {
            Log::error('Import hôpitaux : lecture du fichier impossible - ' . $e->getMessage());
            return back()->with('error', 'Impossible de lire le fichier. Vérifiez qu\'il s\'agit bien d\'un fichier Excel valide.');
        }

        // Correspondance nom de localité -> id (insensible à la casse et aux accents)
        $localites = SubPrefecture::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [$this->normalize($name) => $id]);

        $created = 0;
        $skipped = [];
        $seenCodes = [];
        $seenEmails = [];

        foreach ($rows as $index => $row) {
            $line = $index + 1;
            $row = array_map(fn ($v) => is_string($v) ? trim($v) : (is_null($v) ? '' : trim((string) $v)), array_pad($row, 7, ''));
            [$label, $code, $contact, $localite, $district, $direction, $email] = array_slice($row, 0, 7);

            // Ligne vide
            if (implode('', array_slice($row, 0, 7)) === '') {
                continue;
            }

            // Ligne d'en-tête
            if ($index === 0 && !filter_var($email, FILTER_VALIDATE_EMAIL) && Str::contains($this->normalize($label), ['nom', 'hopital'])) {
                continue;
            }

            $email = Str::lower($email);
            $contact = $this->normalizeContact($contact);

            $error = match (true) {
                $label === '' => "nom de l'hôpital manquant",
                $code === '' => 'code manquant',
                $email === '' => 'email manquant',
                !filter_var($email, FILTER_VALIDATE_EMAIL) => "email invalide ({$email})",
                isset($seenCodes[Str::lower($code)]) => "code {$code} en double dans le fichier",
                isset($seenEmails[$email]) => "email {$email} en double dans le fichier",
                Hospital::where('reference', $code)->exists() => "code {$code} déjà enregistré",
                User::withTrashed()->where('email', $email)->exists() => "email {$email} déjà utilisé",
                default => null,
            };

            if ($error) {
                $skipped[] = "Ligne {$line}" . ($label !== '' ? " ({$label})" : '') . " : {$error}";
                continue;
            }

            $seenCodes[Str::lower($code)] = true;
            $seenEmails[$email] = true;

            $localiteId = $localite !== '' ? ($localites[$this->normalize($localite)] ?? null) : null;

            // Les tables sont en MyISAM (pas de transactions) : on supprime
            // manuellement l'utilisateur si la création de l'hôpital échoue.
            $user = null;
            try {
                $user = new User();
                $user->name = $label;
                $user->email = $email;
                $user->role_as = 'hospital';
                $user->password = Hash::make(self::DEFAULT_PASSWORD);
                $user->save();

                $hospital = new Hospital();
                $hospital->user_id = $user->id;
                $hospital->label = $label;
                $hospital->reference = $code;
                $hospital->contact = $contact ?: null;
                $hospital->localite = $localiteId;
                $hospital->district_sanitaire = $district ?: null;
                $hospital->nom_direction_generale = $direction ?: null;
                $hospital->is_teleconsultation_active = $teleconsultation;
                $hospital->is_ucp_fm = $ucpFm;
                $hospital->save();
                $created++;

                if ($localite !== '' && !$localiteId) {
                    $skipped[] = "Ligne {$line} ({$label}) : importé, mais localité « {$localite} » introuvable (à renseigner manuellement)";
                }
            } catch (\Throwable $e) {
                if ($user?->exists) {
                    User::withTrashed()->whereKey($user->id)->forceDelete();
                }
                Log::error("Import hôpitaux ligne {$line} : " . $e->getMessage());
                $skipped[] = "Ligne {$line} ({$label}) : erreur lors de l'enregistrement";
            }
        }

        if ($created > 0) {
            AuditLogService::log(
                'IMPORT_EXCEL',
                'HOPITAL',
                "Import Excel de {$created} hôpital(aux)",
                [
                    'crees' => $created,
                    'ignores' => count($skipped),
                    'teleconsultation' => $teleconsultation,
                    'ucp_fm' => $ucpFm,
                ]
            );
        }

        return redirect()->route('super.hospital.index')->with('import_report', [
            'created' => $created,
            'messages' => $skipped,
        ]);
    }

    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Hôpitaux');
        $sheet->fromArray(self::HEADERS, null, 'A1');
        $sheet->fromArray(['CHU Exemple', 'HOP-001', '0700000000', 'Cocody', 'Cocody-Bingerville', 'Direction régionale Abidjan 2', 'contact@chu-exemple.ci'], null, 'A2');
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('C:C')->getNumberFormat()->setFormatCode('@'); // garder le 0 initial des numéros
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'modele_import_hopitaux.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function normalize(?string $value): string
    {
        return Str::lower(Str::ascii(trim((string) $value)));
    }

    /** Excel supprime souvent le 0 initial des numéros : on le rétablit. */
    private function normalizeContact(string $contact): string
    {
        $digits = preg_replace('/\D/', '', $contact);
        if (strlen($digits) === 9) {
            $digits = '0' . $digits;
        }
        return $digits !== '' ? $digits : $contact;
    }
}
