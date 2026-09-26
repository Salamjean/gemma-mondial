<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientNotification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    protected $appends = ['is_read', 'details'];

    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Informations et détails complets sur la ressource liée à la notification
     */
    public function getDetailsAttribute(): ?array
    {
        try {
            $type = $this->type;
            $data = is_array($this->data) ? $this->data : (json_decode($this->data ?? '[]', true) ?: []);

            // 1. Rendez-vous
            $rdvId = $this->rdv_id ?? ($data['rdv_id'] ?? ($data['rendez_vous_id'] ?? null));
            if ($type === 'rdv' || !empty($rdvId)) {
                if ($rdvId) {
                    $rdv = RendezVous::with(['doctor.user', 'consultation.hospital'])->find($rdvId);
                    if ($rdv) {
                        $doctorName = null;
                        if ($rdv->doctor && $rdv->doctor->user) {
                            $doctorName = 'Dr. ' . trim(($rdv->doctor->user->nom ?? $rdv->doctor->user->name ?? '') . ' ' . ($rdv->doctor->user->prenom ?? ''));
                        } elseif ($rdv->doctorUser) {
                            $doctorName = 'Dr. ' . trim(($rdv->doctorUser->nom ?? $rdv->doctorUser->name ?? '') . ' ' . ($rdv->doctorUser->prenom ?? ''));
                        }

                        $hospitalName = null;
                        if ($rdv->consultation && $rdv->consultation->hospital) {
                            $h = $rdv->consultation->hospital;
                            $hospitalName = $h->label ?: ($h->nom_direction_generale ?: $h->reference);
                        } elseif ($this->patient && $this->patient->hospital) {
                            $h = $this->patient->hospital;
                            $hospitalName = $h->label ?: ($h->nom_direction_generale ?: $h->reference);
                        }

                        return [
                            'type' => 'rdv',
                            'id' => $rdv->id,
                            'title' => $rdv->title,
                            'date' => $rdv->date,
                            'heure' => $rdv->heure,
                            'status' => $rdv->status,
                            'motif' => $rdv->motif ?? (is_array($rdv->details) ? ($rdv->details['motif'] ?? null) : null),
                            'doctor_name' => $doctorName,
                            'hospital_name' => $hospitalName,
                            'image' => $rdv->image ? asset('storage/rendez-vous/' . $rdv->image) : null,
                        ];
                    }
                }
            }

            // 2. Consultation / Parcours
            $consultationId = $this->consultation_id ?? ($data['consultation_id'] ?? null);
            if ($type === 'consultation' || !empty($consultationId)) {
                if ($consultationId) {
                    $consultation = Consultation::with([
                        'hospital',
                        'doctor.user',
                        'ordonnance',
                        'examen',
                        'arret',
                        'declaration'
                    ])->find($consultationId);

                    if ($consultation) {
                        $doctorName = null;
                        if ($consultation->doctor && $consultation->doctor->user) {
                            $doctorName = 'Dr. ' . trim(($consultation->doctor->user->nom ?? $consultation->doctor->user->name ?? '') . ' ' . ($consultation->doctor->user->prenom ?? ''));
                        }

                        $h = $consultation->hospital;
                        $hospitalName = $h ? ($h->label ?: ($h->nom_direction_generale ?: $h->reference)) : null;

                        // Rassembler les documents et leurs liens PDF
                        $documents = [];
                        if ($consultation->ordonnance) {
                            $documents['ordonnance'] = [
                                'id' => $consultation->ordonnance->id,
                                'pdf_url' => url('api/v1/patient/documents/ordonnance/' . $consultation->ordonnance->id . '/pdf'),
                            ];
                        }
                        if ($consultation->examen) {
                            $documents['examen'] = [
                                'id' => $consultation->examen->id,
                                'pdf_url' => url('api/v1/patient/documents/examen/' . $consultation->examen->id . '/pdf'),
                            ];
                        }
                        if ($consultation->arret) {
                            $documents['arret_travail'] = [
                                'id' => $consultation->arret->id,
                                'pdf_url' => url('api/v1/patient/documents/arret/' . $consultation->arret->id . '/pdf'),
                            ];
                        }

                        return [
                            'type' => 'consultation',
                            'id' => $consultation->id,
                            'code' => $consultation->code_consultation ?? $consultation->id,
                            'date' => $consultation->created_at ? $consultation->created_at->format('Y-m-d H:i:s') : null,
                            'status' => $consultation->status == 1 ? 'Terminée' : 'En cours',
                            'diagnostic' => $consultation->diagnostic,
                            'motif' => $consultation->motif ?? null,
                            'doctor_name' => $doctorName,
                            'hospital_name' => $hospitalName,
                            'documents' => $documents,
                        ];
                    }
                }
            }

            // 3. Déclaration médicale (Naissance / Décès)
            $declarationId = $this->declaration_id ?? ($data['declaration_id'] ?? null);
            if ($type === 'declaration' || !empty($declarationId)) {
                if ($declarationId) {
                    $dec = Declaration::with(['hospital', 'doctor.user', 'naissance', 'deces'])->find($declarationId);
                    if ($dec) {
                        $doctorName = null;
                        if ($dec->doctor && $dec->doctor->user) {
                            $doctorName = 'Dr. ' . trim(($dec->doctor->user->nom ?? $dec->doctor->user->name ?? '') . ' ' . ($dec->doctor->user->prenom ?? ''));
                        }

                        $h = $dec->hospital;
                        $hospitalName = $h ? ($h->label ?: ($h->nom_direction_generale ?: $h->reference)) : null;
                        $decType = $dec->naissance ? 'naissance' : ($dec->deces ? 'deces' : ($dec->type_declaration ?? 'declaration'));

                        return [
                            'type' => 'declaration',
                            'id' => $dec->id,
                            'type_declaration' => $decType,
                            'date' => $dec->created_at ? $dec->created_at->format('Y-m-d H:i:s') : null,
                            'doctor_name' => $doctorName,
                            'hospital_name' => $hospitalName,
                            'pdf_url' => $dec->pdf_url,
                        ];
                    }
                }
            }

            // 4. Admission
            $admissionId = $this->admission_id ?? ($data['admission_id'] ?? null);
            if ($type === 'admission' || !empty($admissionId)) {
                if ($admissionId) {
                    $admission = Admission::with(['hospital'])->find($admissionId);
                    if ($admission) {
                        $h = $admission->hospital;
                        $hospitalName = $h ? ($h->label ?: ($h->nom_direction_generale ?: $h->reference)) : null;

                        return [
                            'type' => 'admission',
                            'id' => $admission->id,
                            'date_admission' => $admission->created_at ? $admission->created_at->format('Y-m-d H:i:s') : null,
                            'motif' => $admission->motif ?? $admission->diagnostic,
                            'hospital_name' => $hospitalName,
                        ];
                    }
                }
            }

            // 5. Affectation / Dossier patient
            if ($type === 'affectation' || !empty($data['patient_id'])) {
                $patient = $this->patient ?: Patient::with('hospital')->find($data['patient_id'] ?? $this->patient_id);
                if ($patient) {
                    $h = $patient->hospital;
                    $hospitalName = $h ? ($h->label ?: ($h->nom_direction_generale ?: $h->reference)) : null;

                    return [
                        'type' => 'affectation',
                        'patient_id' => $patient->id,
                        'code_patient' => $patient->code_patient,
                        'nom_complet' => trim(($patient->nom ?? '') . ' ' . ($patient->prenom ?? '')),
                        'hospital_name' => $hospitalName,
                    ];
                }
            }

            return !empty($data) ? $data : null;
        } catch (\Throwable $e) {
            return !empty($data) ? $data : null;
        }
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function rendezVous(): BelongsTo
    {
        return $this->belongsTo(RendezVous::class, 'rdv_id');
    }

    public function declaration(): BelongsTo
    {
        return $this->belongsTo(Declaration::class);
    }
}
