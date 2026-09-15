<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use App\Models\Consultation;
use App\Models\Ordonnance;
use App\Models\Registre;
use App\Models\User;

try {
    $user = User::first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
    }

    // Share errors view bag like middleware does
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
        'ordonnances.prescriptions.drug',
        'ordonnances.prescriptions.drugHospital.drug',
        'examen',
        'arret',
        'declaration',
        'hospitalisation'
    ])->first();

    if (!$consultation) {
        echo "No consultation found in DB.\n";
        exit(0);
    }

    $patient = $consultation->patient;
    $ordonnance_interne = Ordonnance::where('type', 'interne')->where('consultation_id', $consultation->id)->first();
    $ordonnance_externe = Ordonnance::where('type', 'externe')->where('consultation_id', $consultation->id)->first();
    $registres = Registre::where('consultation_id', $consultation->id)->get();

    echo "Testing rendering parcours_intervention for Consultation ID: {$consultation->id}...\n";
    $viewContent = view('users.patient.parcours_intervention', compact('consultation', 'patient', 'ordonnance_interne', 'ordonnance_externe', 'registres'))->render();
    echo "SUCCESS: parcours_intervention view rendered (" . strlen($viewContent) . " bytes)\n";

    echo "Testing rendering dossier_medical for Patient ID: {$patient->id}...\n";
    $consultations = Consultation::where('patient_id', $patient->id)->get();
    $dossierContent = view('users.doctor.patient.dossier_medical', compact('patient', 'consultations', 'consultation', 'ordonnance_interne', 'ordonnance_externe', 'registres'))->render();
    echo "SUCCESS: dossier_medical view rendered (" . strlen($dossierContent) . " bytes)\n";

} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
