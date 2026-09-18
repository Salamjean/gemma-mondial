<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST DU SYSTÈME DE DÉSACTIVATION DE LA TÉLÉCONSULTATION ===\n\n";

// 1. Récupérer un hôpital de test
$hospital = \App\Models\Hospital::first();
if (!$hospital) {
    echo "Aucun hôpital trouvé dans la base de données.\n";
    exit(1);
}

echo "1. Hôpital sélectionné : {$hospital->id} - {$hospital->label}\n";
echo "   Statut initial téléconsultation : " . ($hospital->is_teleconsultation_active ? "ACTIVÉ" : "DÉSACTIVÉ") . "\n";

// 2. Tester le toggle
$superController = new \App\Http\Controllers\Super\HospitalController();
$initialState = (bool)$hospital->is_teleconsultation_active;

// Exécuter toggleTeleconsultation
$hospital->is_teleconsultation_active = !$initialState;
$hospital->save();
$hospital->refresh();
echo "2. Bascule du statut -> Nouveau statut : " . ($hospital->is_teleconsultation_active ? "ACTIVÉ" : "DÉSACTIVÉ") . "\n";
assert($hospital->is_teleconsultation_active === !$initialState, "La bascule a échoué");

// 3. Tester le filtrage des hôpitaux dans Infirmier ConsultationController
echo "\n3. Test du filtre des hôpitaux dans l'API Infirmier :\n";
$hospital->is_teleconsultation_active = false;
$hospital->save();

$activeHospitals = \App\Models\Hospital::where(function($q) {
    $q->where('delete', 0)->orWhereNull('delete');
})->where('is_teleconsultation_active', true)->pluck('id')->toArray();

echo "   Hôpital {$hospital->id} présent dans la liste des téléconsultations actives ? " . (in_array($hospital->id, $activeHospitals) ? "OUI (ERREUR)" : "NON (CORRECT - BIEN MASQUÉ)") . "\n";

// 4. Tester avec un médecin rattaché à cet hôpital
$doctor = \App\Models\Doctor::where('hospital_id', $hospital->id)->with('user')->first();
if ($doctor && $doctor->user) {
    \Illuminate\Support\Facades\Auth::login($doctor->user);
    $docController = new \App\Http\Controllers\Doctor\ConsultationController();
    $pendingRes = $docController->getPendingOnlineRequests();
    $incomingRes = $docController->getIncomingCallRequests();
    
    $pendingData = json_decode($pendingRes->getContent(), true);
    $incomingData = json_decode($incomingRes->getContent(), true);
    
    echo "\n4. Test API Médecin avec hôpital désactivé (Dr. {$doctor->user->name}) :\n";
    echo "   - getPendingOnlineRequests : " . json_encode($pendingData) . "\n";
    echo "   - getIncomingCallRequests : " . json_encode($incomingData) . "\n";
    assert(empty($pendingData['requests']), "Les requêtes de téléconsultation doivent être vides pour un hôpital désactivé");
    assert($incomingData['has_incoming'] === false, "Les appels entrants doivent être bloqués pour un hôpital désactivé");
    \Illuminate\Support\Facades\Auth::logout();
}

// 5. Tester avec un infirmier rattaché à cet hôpital
$infirmier = \App\Models\Infirmier::where('hospital_id', $hospital->id)->with('user')->first();
if ($infirmier && $infirmier->user) {
    \Illuminate\Support\Facades\Auth::login($infirmier->user);
    $infController = new \App\Http\Controllers\Infirmier\ConsultationController();
    $infIncomingRes = $infController->getIncomingCall(new \Illuminate\Http\Request());
    $infActiveRes = $infController->getActiveTeleconsultations(new \Illuminate\Http\Request());
    
    $infIncomingData = json_decode($infIncomingRes->getContent(), true);
    $infActiveData = json_decode($infActiveRes->getContent(), true);
    
    echo "\n5. Test API Infirmier avec hôpital désactivé (Inf. {$infirmier->user->name}) :\n";
    echo "   - getIncomingCall : " . json_encode($infIncomingData) . "\n";
    echo "   - getActiveTeleconsultations : " . json_encode($infActiveData) . "\n";
    assert($infIncomingData['has_incoming'] === false, "Appel entrant infirmier doit être désactivé");
    assert(empty($infActiveData['teleconsultations']), "Liste active infirmier doit être vide");
    \Illuminate\Support\Facades\Auth::logout();
}

// 6. Rétablir le statut actif de l'hôpital
$hospital->is_teleconsultation_active = true;
$hospital->save();
echo "\n6. Statut de l'hôpital restauré à ACTIVÉ : " . ($hospital->is_teleconsultation_active ? "ACTIVÉ" : "DÉSACTIVÉ") . "\n";

echo "\n TOUS LES TESTS DE DÉSACTIVATION DE LA TÉLÉCONSULTATION SONT VALIDES !\n";
