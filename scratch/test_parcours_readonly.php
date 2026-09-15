<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Consultation;
use App\Models\Ordonnance;
use App\Models\Registre;
use App\Models\User;

try {
    $user = User::first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
    }
    view()->share('errors', new \Illuminate\Support\ViewErrorBag());

    $consultation = Consultation::with([
        'patient.user',
        'doctor.user',
        'infirmier.user',
        'admission.infirmier.user',
        'admission.doctor.user',
        'admission.cashier.user',
        'admission.secretariat.user',
        'prestationHospital.prestationService.service',
        'registre.registreConsultationCurative',
        'registre.registreAccouchement',
        'registre.registreConsultationPreNatale',
        'registre.registreConsultationPostNatale',
        'ordonnances.prescriptions.drug',
        'ordonnances.prescriptions.drugHospital.drug',
        'examen',
        'arret',
        'declaration',
        'hospitalisation'
    ])->first();

    if (!$consultation) {
        echo "No consultation found.\n";
        exit(0);
    }

    $patient = $consultation->patient;
    $ordonnance_interne = Ordonnance::where('type', 'interne')->where('consultation_id', $consultation->id)->first();
    $ordonnance_externe = Ordonnance::where('type', 'externe')->where('consultation_id', $consultation->id)->first();
    $registres = Registre::where('consultation_id', $consultation->id)->get();

    $html = view('users.patient.parcours_intervention', compact('consultation', 'patient', 'ordonnance_interne', 'ordonnance_externe', 'registres'))->render();
    echo "SUCCESS: parcours_intervention view rendered successfully! Size: " . strlen($html) . " bytes\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
