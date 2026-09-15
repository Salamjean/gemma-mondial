<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use App\Models\PrestationService;
use App\Models\ServiceHospital;
use App\Models\PrestationHospital;

echo "=== SERVICES ===\n";
foreach (Service::all() as $s) {
    echo "ID: {$s->id} | Libelle: {$s->libelle}\n";
}

echo "\n=== PRESTATION SERVICES ===\n";
foreach (PrestationService::with('service')->get() as $ps) {
    echo "ID: {$ps->id} | Libelle: {$ps->libelle} | Service: " . ($ps->service->libelle ?? 'N/A') . "\n";
}

echo "\n=== SERVICE HOSPITALS ===\n";
foreach (ServiceHospital::with('service')->get() as $sh) {
    echo "ID: {$sh->id} | Hospital ID: {$sh->hospital_id} | Service: " . ($sh->service->libelle ?? 'N/A') . "\n";
}

echo "\n=== PRESTATION HOSPITALS ===\n";
foreach (PrestationHospital::with(['prestationService.service', 'serviceHospital.service'])->get() as $ph) {
    $serv = $ph->prestationService->libelle ?? $ph->serviceHospital->service->libelle ?? 'N/A';
    echo "ID: {$ph->id} | Hospital ID: {$ph->hospital_id} | Service/Prestation: {$serv}\n";
}
