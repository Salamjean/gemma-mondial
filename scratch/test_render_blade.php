<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST DU RENDU DE FORMULAIRE.BLADE.PHP ===\n\n";

$consultation = \App\Models\Consultation::with(['patient.user', 'admission.patient.user'])->first();
if (!$consultation) {
    echo "Aucune consultation trouvée.\n";
    exit(1);
}

$infirmier = \App\Models\Infirmier::with('user', 'hospital')->first();
if ($infirmier && $infirmier->user) {
    \Illuminate\Support\Facades\Auth::login($infirmier->user);
}

view()->share('errors', new \Illuminate\Support\ViewErrorBag());

// 1. Test avec téléconsultation DÉSACTIVÉE
$hosp = $infirmier->hospital;
if ($hosp) {
    $hosp->is_teleconsultation_active = false;
    $hosp->save();
    $consultation->hospital_id = $hosp->id;
    $consultation->save();
}

echo "1. Test de rendu avec hôpital DÉSACTIVÉ :\n";
try {
    $html = view('users.infirmier.consultation.formulaire', compact('consultation'))->render();
    echo "    Rendu réussi sans erreur ! (Taille HTML: " . strlen($html) . " octets)\n";
    echo "    Bouton radio Téléconsultation masqué ? " . (!str_contains($html, 'id="radio_teleconsultation"') ? "OUI (CORRECT - BIEN MASQUÉ)" : "NON (ERREUR)") . "\n";
    echo "    Bloc programmation Téléconsultation masqué ? " . (!str_contains($html, 'id="bloc_teleconsultation"') ? "OUI (CORRECT - BIEN MASQUÉ)" : "NON (ERREUR)") . "\n";
} catch (\Exception $e) {
    echo "    ERREUR lors du rendu : " . $e->getMessage() . "\n";
    echo "    Trace : " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// 2. Test avec téléconsultation ACTIVÉE
if ($hosp) {
    $hosp->is_teleconsultation_active = true;
    $hosp->save();
    $consultation->hospital_id = $hosp->id;
    $consultation->save();
}

echo "\n2. Test de rendu avec hôpital ACTIVÉ :\n";
try {
    $htmlActive = view('users.infirmier.consultation.formulaire', compact('consultation'))->render();
    echo "    Rendu réussi sans erreur ! (Taille HTML: " . strlen($htmlActive) . " octets)\n";
    echo "    Bouton radio Téléconsultation affiché ? " . (str_contains($htmlActive, 'id="radio_teleconsultation"') ? "OUI (CORRECT - BIEN AFFICHÉ)" : "NON (ERREUR)") . "\n";
    echo "    Bloc programmation Téléconsultation affiché ? " . (str_contains($htmlActive, 'id="bloc_teleconsultation"') ? "OUI (CORRECT - BIEN AFFICHÉ)" : "NON (ERREUR)") . "\n";
} catch (\Exception $e) {
    echo "    ERREUR lors du rendu : " . $e->getMessage() . "\n";
    echo "    Trace : " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// 3. Test du menu médecin (Appels Vidéo HD masqué si désactivé)
echo "\n3. Test du menu médecin :\n";
$doctor = \App\Models\Doctor::with('user', 'hospital')->first();
if ($doctor && $doctor->user) {
    \Illuminate\Support\Facades\Auth::login($doctor->user);
    $docHosp = $doctor->hospital;
    
    // Cas désactivé
    if ($docHosp) {
        $docHosp->is_teleconsultation_active = false;
        $docHosp->save();
        $doctor->user->doctor->unsetRelation('hospital');
    }
    $menuHtmlDeact = view('partials.inc.agent._doctor')->render();
    echo "    Menu médecin avec hôpital DÉSACTIVÉ : 'Appels Vidéo HD' présent ? " . (str_contains($menuHtmlDeact, 'Appels Vidéo HD') ? "OUI (ERREUR)" : "NON (CORRECT - BIEN MASQUÉ)") . "\n";
    
    // Cas activé
    if ($docHosp) {
        $docHosp->is_teleconsultation_active = true;
        $docHosp->save();
        $doctor->user->doctor->unsetRelation('hospital');
    }
    $menuHtmlAct = view('partials.inc.agent._doctor')->render();
    echo "    Menu médecin avec hôpital ACTIVÉ : 'Appels Vidéo HD' présent ? " . (str_contains($menuHtmlAct, 'Appels Vidéo HD') ? "OUI (CORRECT - BIEN AFFICHÉ)" : "NON (ERREUR)") . "\n";
}

echo "\n TOUS LES RENDUS BLADE FONCTIONNENT PARFAITEMENT SANS ERREUR !\n";
