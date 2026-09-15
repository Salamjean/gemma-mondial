<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hospital\ServiceRequest;
use App\Models\Doctor;
use App\Models\Infirmier;
use App\Models\PrestationDoctor;
use App\Models\PrestationHospital;
use App\Models\PrestationService;
use App\Models\Service;
use App\Models\ServiceDoctor;
use App\Models\ServiceHospital;
use App\Models\TypeConsultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    public function index()
    {
        $services = ServiceHospital::where('hospital_id', Auth::user()->hospital->id)
            ->whereHas('service')
            ->with(['service', 'prestationHospitals'])
            ->get();
        $title = "Liste des services";
        return view('users.hospital.service.index', ["title" => $title, 'services' => $services]);
    }

    public function show($id)
    {
        $service = ServiceHospital::findOrFail($id);
        $servicei = Service::findOrFail($service->service_id);
        $title = "Détails | $service->libelle";
        return view('users.hospital.service.show', compact('service', 'servicei', 'title'));
    }

    public function add()
    {
        $title = "Ajout d'un service";

        $serviceHospital = ServiceHospital::where('hospital_id', Auth::user()->hospital->id)->get();

        $alreadyAddedIds = $serviceHospital->pluck('service_id')->toArray();

        $servicesDisponibles = Service::whereNotIn('id', $alreadyAddedIds)
            ->orderBy("libelle")
            ->get();

        return view('users.hospital.service.add', [
            "title" => $title,
            "services" => $servicesDisponibles,
            "hasAvailableServices" => $servicesDisponibles->isNotEmpty()
        ]);
    }

    public function store(ServiceRequest $request)
    {
        $hospitalId = Auth::user()->hospital->id;
        $serviceId = null;

        // 1. Déterminer le Service (sélectionné ou nouveau)
        if ($request->filled('nom_nouveau_service')) {
            $nomService = trim($request->nom_nouveau_service);
            $serviceModel = Service::whereRaw('LOWER(libelle) = ?', [strtolower($nomService)])->first();
            if (!$serviceModel) {
                $serviceModel = Service::create([
                    'libelle' => $nomService,
                    'status' => 0
                ]);
            }
            $serviceId = $serviceModel->id;
        } elseif ($request->filled('department')) {
            $serviceId = $request->department;
        }

        if (!$serviceId) {
            return back()->with('error', 'Veuillez sélectionner un service existant ou indiquer le nom d\'un nouveau service.');
        }

        // Vérifier si le service est déjà rattaché à cet hôpital
        $existing = ServiceHospital::where('hospital_id', $hospitalId)
            ->where('service_id', $serviceId)
            ->exists();

        if ($existing) {
            return back()->with('error', 'Ce service est déjà configuré dans votre structure hospitalière !');
        }

        try {
            $service = new ServiceHospital();
            $service->hospital_id = $hospitalId;
            $service->service_id = $serviceId;
            $service->description = $request->service_description ?? null;
            $service->save();

            $actesCount = 0;

            // A. Enregistrer les prestations existantes sélectionnées (service[] + prix[])
            if ($request->has('prix') && is_array($request->prix)) {
                foreach ($request->prix as $key => $prix) {
                    if ($prix !== null && $prix !== '') {
                        $pService = new PrestationHospital();
                        $pService->service_hospital_id = $service->id;
                        $pService->prix = (int)$prix;
                        $pService->prestation_service_id = $request->service[$key];
                        $pService->description = $request->description[$key] ?? null;
                        $pService->save();
                        $actesCount++;
                    }
                }
            }

            // B. Enregistrer les nouveaux actes médicaux personnalisés saisis dynamiquement
            if ($request->has('nouveau_acte_libelle') && is_array($request->nouveau_acte_libelle)) {
                foreach ($request->nouveau_acte_libelle as $key => $libelle) {
                    $nomActe = trim($libelle);
                    $prix = $request->nouveau_acte_prix[$key] ?? null;
                    if (!empty($nomActe) && $prix !== null && $prix !== '') {
                        $prestService = PrestationService::firstOrCreate(
                            [
                                'service_id' => $serviceId,
                                'libelle' => $nomActe
                            ],
                            ['status' => 0]
                        );

                        $pService = new PrestationHospital();
                        $pService->service_hospital_id = $service->id;
                        $pService->prix = (int)$prix;
                        $pService->prestation_service_id = $prestService->id;
                        $pService->description = $request->nouveau_acte_description[$key] ?? null;
                        $pService->save();
                        $actesCount++;
                    }
                }
            }

            // C. Si aucun acte n'a de prix précisé, créer une consultation standard par défaut
            if ($actesCount === 0) {
                $serviceObj = Service::find($serviceId);
                $nomDefault = "Consultation " . ($serviceObj ? $serviceObj->libelle : 'spécialisée');
                $prestService = PrestationService::firstOrCreate(
                    [
                        'service_id' => $serviceId,
                        'libelle' => $nomDefault
                    ],
                    ['status' => 0]
                );

                $pService = new PrestationHospital();
                $pService->service_hospital_id = $service->id;
                $pService->prix = 0;
                $pService->prestation_service_id = $prestService->id;
                $pService->description = "Consultation standard";
                $pService->save();
            }

            return redirect()->route('hospital.service.index')->with('success', 'Le service a été enregistré avec succès.');

        } catch (\Throwable $err) {
            Log::error("Erreur ajout service hospital: " . $err->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'enregistrement : ' . $err->getMessage());
        }
    }

    public function update($id, Request $request)
    {
        $serviceh = ServiceHospital::findOrFail($id);

        if ($request->has('prixupdate')) {
            foreach ($serviceh->prestationHospitals as $key => $item) {
                $service = PrestationHospital::find($item->id);
                if ($service && isset($request->prixupdate[$key])) {
                    $service->prix = (int)$request->prixupdate[$key];
                    $service->description = $request->descriptionupdate[$key] ?? null;
                    $service->save();
                }
            }
        }

        // Ajouter des prestations existantes du catalogue non encore cochées
        if ($request->prix) {
            foreach ($request->prix as $key => $prix) {
                if ($prix !== null && $prix !== '') {
                    $pservice = new PrestationHospital();
                    $pservice->service_hospital_id = $serviceh->id;
                    $pservice->prix = (int)$prix;
                    $pservice->prestation_service_id = $request->service[$key];
                    $pservice->description = $request->description[$key] ?? null;
                    $pservice->save();
                }
            }
        }

        // Ajouter de nouveaux actes médicaux saisis librement
        if ($request->has('nouveau_acte_libelle') && is_array($request->nouveau_acte_libelle)) {
            foreach ($request->nouveau_acte_libelle as $key => $libelle) {
                $nomActe = trim($libelle);
                $prix = $request->nouveau_acte_prix[$key] ?? null;
                if (!empty($nomActe) && $prix !== null && $prix !== '') {
                    $prestService = PrestationService::firstOrCreate(
                        [
                            'service_id' => $serviceh->service_id,
                            'libelle' => $nomActe
                        ],
                        ['status' => 0]
                    );

                    $pService = new PrestationHospital();
                    $pService->service_hospital_id = $serviceh->id;
                    $pService->prix = (int)$prix;
                    $pService->prestation_service_id = $prestService->id;
                    $pService->description = $request->nouveau_acte_description[$key] ?? null;
                    $pService->save();
                }
            }
        }

        return redirect()->route('hospital.service.index')->with('success', 'Ce service a été mis à jour avec succès.');
    }

    public function status($id)
    {

        $service = ServiceHospital::findOrFail($id);

        $service->status = $service->status == 0 ? 1 : 0;

        $service->save();

        return back()->with('success', "Statut modifié avec succès.");
    }

    public function delete($id)
    {

        $type = ServiceHospital::findOrFail($id);
        if (Doctor::where('service_hospital_id', $type->id)->exists() || Infirmier::where('service_hospital_id', $type->id)->exists())
            return redirect()->back()->withErrors('Impossible de supprimé un service où des agents de santé sont affectés.');
        $type->PrestationHospitals()->delete();
        $type->delete();

        return back()->with('success', "Service supprimé avec succès.");
    }

    public function searchService($service)
    {
        $services = PrestationService::where('service_id', $service)->where('status', 0)->get();
        return response()->json($services);
    }

    public function searchServiceServiceHospital($service)
    {
        $services = PrestationHospital::with('prestationService')->where('service_hospital_id', $service)->where('status', 0)->get();
        return response()->json($services);
    }

    public function deleteService($id)
    {
        $pService = PrestationHospital::findOrFail($id);

        $service = ServiceHospital::findOrFail($pService->service_hospital_id);
        if (count($service->PrestationHospitals) <= 1)
            return back()->withErrors("Impossible de supprimer tout les services du service.");

        if (PrestationDoctor::where('prestation_hospital_id', $pService->id)->exists())
            return redirect()->back()->withErrors('Impossible de supprimé un prestation de service où des agents de santé sont affectés.');

        $pService->delete();

        return back()->with('success', "Prestation de Service supprimé avec succès.");
    }
}
