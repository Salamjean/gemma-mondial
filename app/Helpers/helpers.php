<?php

use App\Models\Admission;
use App\Models\ArretTravail;
use App\Models\BulletinExamen;
use Carbon\Carbon;
use App\Models\Examen;
use App\Models\Patient;
use App\Models\Registre;
use App\Models\Ordonnance;
use App\Models\Traitement;
use App\Models\Observation;
use App\Models\Consultation;
use App\Models\Hospitalisation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

function convertToDate($dateString)
{
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dateString, $matches)) {
        $formattedDate = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        return Carbon::createFromFormat('Y-m-d', $formattedDate);
    }

    return null;
}
function getMois()
{
    $date = date('M');
    $carbon = Carbon::parse($date)->locale('fr_FR');
    $date_fr = $carbon->isoFormat('MMMM');
    return $date_fr;
}
function getAnnee()
{
    $date = date('Y');
    $carbon = Carbon::parse($date)->locale('fr_FR');
    $date_fr = $carbon->isoFormat('YYYY');
    return $date_fr;
}

function rdvImageGenerator()
{
    $table = array('assets/uploads/notif/notif1.png', 'assets/uploads/notif/notif2.png', 'assets/uploads/notif/notif3.png', 'assets/uploads/notif/notif4.png', 'assets/uploads/notif/notif5.png', 'assets/uploads/notif/notif6.png', 'assets/uploads/notif/notif7.png');

    $index = array_rand($table);

    return $table[$index];
}

function noOrdreHospitalisation()
{
    if (Auth::check() && optional(Auth::user())->doctor) {
        return Consultation::where('doctor_id', Auth::user()->doctor->id)->whereDate('created_at', Carbon::today())->count();
    }
    return Consultation::whereDate('created_at', Carbon::today())->count();
}
function noOrdreConsultation()
{
    if (Auth::check() && optional(Auth::user())->doctor) {
        return Consultation::where('doctor_id', Auth::user()->doctor->id)->whereDate('created_at', Carbon::today())->count();
    }
    return Consultation::whereDate('created_at', Carbon::today())->count();
}

function noDossierHospitalisation()
{
    $date = date('Y');
    $count = Hospitalisation::whereDate('created_at', Carbon::now())->count();
    $count = $count + 1;
    $dossier = "HOSP" . $date . $count;

    return $dossier;
}

function codeAdmission()
{
    $date = date('Y');
    $count = Admission::all();
    $count = $count->count() + 1;
    $code = "ADM" . $date . $count;

    return $code;
}
function routeActive($routeName)
{

    $class = 'text-primary';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }
        }
    } elseif (request()->routeIs($routeName)) {
        return $class;
    }
}

function chartDayFrensh($date)
{
    $carbon = Carbon::parse($date)->locale('fr_FR');
    $date_fr = $carbon->isoFormat('D/M');
    return $date_fr;
}

function chartMonthFrensh($date)
{
    $carbon = Carbon::parse($date)->locale('fr_FR');
    $date_fr = $carbon->isoFormat('MMMM YYYY');
    return $date_fr;
}

function dateFr($date)
{
    $carbon = Carbon::parse($date)->locale('fr_FR');
    $date_fr = $carbon->isoFormat('ddd D MMMM YYYY');
    return $date_fr;
}

function dateNumberFr($date)
{
    $carbon = Carbon::parse($date)->locale('fr_FR');
    $date_fr = $carbon->isoFormat('D/M/Y');
    return $date_fr;
}

function dateCompletFr($date)
{
    $carbon = Carbon::parse($date)->locale('fr_FR');
    $date_fr = $carbon->isoFormat('dddd D MMMM YYYY');
    return $date_fr;
}

function heureFr($date)
{
    $carbon = Carbon::parse($date)->locale('fr_FR');
    $date_fr = $carbon->isoFormat('H:m');
    return $date_fr;
}

function jourFr($date)
{
    $carbon = Carbon::parse($date)->locale('fr_FR');
    $date_fr = $carbon->isoFormat('dddd');
    return $date_fr;
}

function moisFr($date)
{
    $carbon = Carbon::parse($date)->locale('fr_FR');
    $date_fr = $carbon->isoFormat('MMMM');
    return $date_fr;
}
function iconsLoad()
{
    $path = "assets/iconFavicon";
    $data["path"] = $path;
    $data['logo'] = "$path/logo.png";
    $data['loading'] = "$path/loading.png";

    $favFile = "$path/favicon.ico";
    foreach (['png', 'ico', 'jpg', 'jpeg', 'svg'] as $ext) {
        if (file_exists(public_path("$path/favicon.$ext"))) {
            $favFile = "$path/favicon.$ext";
            break;
        }
    }
    $data['favicon'] = $favFile;

    return $data;
}

function roleFr($role_as)
{
    $role = match ($role_as) {
        'super' => 'Super',
        'hospital' => 'Hopital',
        'doctor' => 'Docteur',
        'cashier' => 'Caissière',
        'secretariat' => 'Secretaire',
        'accountant' => 'Comptable',
        'infirmier' => 'Infirmier(e)',
        'pharmacy' => 'Pharmacien',

        default => 'none',
    };
    return $role;
}

function dateNaiss($date)
{
    $jour = substr($date, 0, 2);
    $mois = substr($date, 3, 2);
    $annee = substr($date, 8, 2);

    return "$jour$mois$annee";
}

function countNaiss($date)
{

    $count = Patient::where('birth_date', $date)->count();


    return $count + 1;
}

function getUserHospitalId()
{
    if (!Auth::check()) return '';
    $user = Auth::user();
    if ($user->doctor && isset($user->doctor->hospital_id)) return $user->doctor->hospital_id;
    if ($user->infirmier && isset($user->infirmier->hospital_id)) return $user->infirmier->hospital_id;
    if ($user->secretariat && isset($user->secretariat->hospital_id)) return $user->secretariat->hospital_id;
    if (isset($user->hospital_id)) return $user->hospital_id;
    return '';
}

function codeRegistre($code, $id)
{
    $consultation = Consultation::where('patient_id', $id)->get();
    $count = $consultation->count() + 1;
    $code = "RE" . substr($code, 2) . getUserHospitalId() . $count;

    return $code;
}

function codeOrdonnance($code, $id)
{
    $consultation = Consultation::where('patient_id', $id)->get();
    $count = $consultation->count() + 1;
    $code = "ORD" . substr($code, 2) . getUserHospitalId() . $count;

    return $code;
}

function codeBulletin($code, $id)
{
    $consultation = Consultation::where('patient_id', $id)->get();
    $count = $consultation->count() + 1;
    $code = "BE" . substr($code, 2) . getUserHospitalId() . $count;

    return $code;
}
function codeExamen($code, $id)
{
    $consultation = Consultation::where('patient_id', $id)->get();
    $count = $consultation->count() + 1;
    $code = "EX" . substr($code, 2) . getUserHospitalId() . $count;

    return $code;
}

function codeArret($code, $id)
{
    $consultation = Consultation::where('patient_id', $id)->get();
    $count = $consultation->count() + 1;
    $code = "ART" . substr($code, 2) . getUserHospitalId() . $count;

    return $code;
}

function codeTraitement($code, $id)
{
    $consultation = Consultation::where('patient_id', $id)->get();
    $count = $consultation->count() + 1;
    $code = "TRAIT" . substr($code, 2) . getUserHospitalId() . $count;

    return $code;
}

function codeHospitalisation($code, $id)
{
    $consultation = Consultation::where('patient_id', $id)->get();
    $count = $consultation->count() + 1;
    $code = "HOSP" . substr($code, 2) . getUserHospitalId() . $count;

    return $code;
}

function codeObservation($code, $id)
{
    $consultation = Consultation::where('patient_id', $id)->get();
    $count = $consultation->count() + 1;
    $code = "OBS" . substr($code, 2) . getUserHospitalId() . $count;

    return $code;
}

function listePosologies()
{
    return [
        '0-0-1',
        '1-0-0',
        '1-0-1',
        '1-1-0',
        '1-1-1',
        '0-0-2',
        '2-0-0',
        '2-0-2',
        '2-2-0',
        '2-2-2',
        '3-0-0',
        '3-0-3',
        '3-3-0',
        '3-3-3',
        '4-0-0',
        '4-0-4',
        '4-4-0',
        '4-4-4',
    ];
}

function grudSaleType($type): string
{
    $formatage = '';
    switch ($type) {
        case 'care_requested':
            $formatage = 'Soins infirmier';
            break;

        case 'ordonnance':
            $formatage = 'Ordonnance interne';
            break;

        case 'hospitalisation':
            $formatage = 'Hospitalisation';
            break;

        default:
            $formatage = 'null';
    }

    return $formatage;
}


if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber($phone, $sep = " ")
    {
        // Vérifier si le numéro de téléphone a déjà été formaté
        if (strpos($phone, $sep) !== false) {
            return $phone;
        }
        // Ajouter des tirets tous les deux chiffres
        $formatted = '';
        $length = strlen($phone);
        for ($i = 0; $i < $length; $i += 2) {
            $formatted .= substr($phone, $i, 2);
            if ($i < $length - 2) {
                $formatted .= $sep;
            }
        }
        return "(+225) " . $formatted;
    }
}

if (!function_exists('formatDate')) {
    function formatDate($dateString, $separator = '-')
    {
        // Créer un objet Carbon à partir de la chaîne de date d'origine
        $date = Carbon::createFromFormat('Y-m-d', $dateString);

        // Formater la date dans le format souhaité
        return $date->format('d' . $separator . 'm' . $separator . 'Y');
    }
}

if (!function_exists('generateCode')) {
    function generateCode($chaine)
    {
        if ($chaine === '') {
            $chaine = "D";
        }

        $numDigits = 10;
        $date = date('d-m-Y'); // Format jour-mois-année
        $date_sans_tirets = str_replace('-', '', $date); // Suppression des tirets


        $min = pow(10, $numDigits - 1);
        $max = pow(10, $numDigits) - 1;
        $code = $chaine . '' . $date_sans_tirets . '' . rand($min, $max);
        return $code;
    }
}

if (!function_exists('formatOrderStatus')) {
    function formatOrderStatus($status)
    {
        $newOrderSatus = "";

        if ($status === "pending") {
            $newOrderSatus = "En attente";
        } elseif ($status === "delivered") {
            $newOrderSatus = "Livrée";
        } else {
            $newOrderSatus = "Annulée";
        }

        return $newOrderSatus;
    }
}

if (!function_exists('formatGender')) {
    function formatGender($gender)
    {
        $newGender = "";

        if ($gender === "homme") {
            $newGender = "M.";
        } elseif ($gender === "femme") {
            $newGender = "Mme";
        } else {
            $newGender = "M./Mme";
        }

        return $newGender;
    }
}

if (!function_exists('formatRole')) {
    function formatRole($role)
    {
        $newRole = "";

        if ($role === "super_admin") {
            $newRole = "Super-admin";
        } elseif ($role === "admin") {
            $newRole = "Admin";
        } elseif ($role === "doctor") {
            $newRole = "Docteur";
        } elseif ($role === "hospital") {
            $newRole = "Hôpital";
        } elseif ($role === "secretaire") {
            $newRole = "Secretaire";
        } elseif ($role === "caisse") {
            $newRole = "Caisse";
        } else {
            $newRole = "Patient";
        }

        return $newRole;
    }
}

if (!function_exists('formatSexe')) {
    function formatSexe($sexe)
    {
        $newSexe = "";

        if ($sexe === "M") {
            $newSexe = "Masculin";
        } elseif ($sexe === "F") {
            $newSexe = "Féminin";
        } else {
            $newSexe = "";
        }

        return $newSexe;
    }
}

if (!function_exists('generateCode2')) {
    function generateCode2($chaine)
    {
        if ($chaine === '') {
            $chaine = "D";
        }

        $time = Carbon::now();
        $code = $chaine . '' . $time->format('dmYHis');
        return $code;
    }
}

if (!function_exists('CodeDoctor')) {
    function CodeDoctor($chaine)
    {
        if ($chaine === '') {
            $chaine = "D";
        }

        $time = Carbon::now();
        $code = $chaine . '' . $time->format('dHmiY');
        return $code;
    }
}

if (!function_exists('getInitials')) {
    function getInitials($words)
    {
        $wordList = explode(" ", $words);
        $firstWord = $wordList[0];
        $secondWord = $wordList[1];
        $firstInitial = $firstWord[0];
        $second_initial = $secondWord[0];
        return strtoupper($firstInitial) . strtoupper($second_initial);
    }
}

if (!function_exists('delete_file')) {
    function delete_file($url)
    {
        if (File::exists(public_path($url))) {
            File::delete(public_path($url));
        }
    }
}

function saveImage($image, $path)
{

    if (!$image) {
        return null;
    }

    $ext = 'png';
    $filename = time() . '.' . $ext;

    $decodedImage = base64_decode($image);

    $destinationPath = public_path('assets/uploads/' . $path . '/' . $filename);

    if (file_put_contents($destinationPath, $decodedImage)) {
        return $filename;
    }
}


function uploadImage($image, $path)
{
    $uploadDirectory = public_path('assets/uploads/' . $path . '/');
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }

    $file = $image;
    $ext = $file->getClientOriginalExtension();
    $filename = time() . '.' . $ext;
    $file->move($uploadDirectory, $filename);

    return $filename;
}

function deleteUploadImage($image, $link)
{
    $uploadDirectory = public_path('assets/uploads/' . $link . '/');

    // Créer le dossier s'il n'existe pas
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }

    // Supprimer l'ancienne image si elle existe
    $oldImagePath = $uploadDirectory . $image;
    if ($image && file_exists($oldImagePath)) {
        unlink($oldImagePath);
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $file = $_FILES['image'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '.' . $ext;

    $destination = $uploadDirectory . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    } else {
        return false;
    }
}

function deleteImage($image, $link)
{
    $path = public_path("assets/uploads/$link/$image");
    if(File::exists($path))
    {
        File::delete($path);
    }
}

function motSortie($name)
{
    $title = '';
    switch ($name) {
        case 'declaration-naissance':
            $title .= 'Naissance';
            break;
        case 'declaration-deces-patient':
            $title .= 'Décès du patient';
            break;
        case 'declaration-deces-enfant':
            $title .= 'Décès du nouveau né';
            break;
        case 'hospitalisation':
            $title .= 'Patient hospitalisé';
            break;
        case 'observation':
            $title .= 'Patient de mis en observation';
            break;
        case 'suite-couche':
            $title .= 'Suite de couche';
            break;
        case 'sortie':
            $title .= 'Sortie du patient';
            break;
        case 'refere-interne':
            $title .= 'Patient reféré en interne';
            break;
        case 'refere-externe':
            $title .= 'Patient reféré en externe';
            break;
        case 'cas-presume-tb-resume':
            $title .= 'Cas presumé TB résumé';
            break;
        case 'a-revoir':
            $title .= 'Patient à revoir';
            break;
        case 'autre':
            $title .= '';
            break;
        case 'sortir-contre-avis-medical':
            $title .= 'Patient sortie contre avis medial';
            break;

        default:
            $title = '';
            return $title;
    }

    return $title;
}

function dayIndexNameString($daysData)
{
    $days = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
    $day = " ";

    if ($daysData !== null) {
        foreach ($daysData as $key => $item) {
            $day .= $days[$item] . ', ';
        }
    } else {
        // Gérer le cas où $daysData est nul
        $day = "Aucune disponibilité disponible.";
    }

    return $day;
}


function dayIndexNameValue($daysData)
{

    $days = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];
    $day = " ";

    foreach ($daysData as $key => $item) {
        $day .= $days[$item];
    }

    return $day;
}

function getLogInUser()
{
    return Auth::user();
}

