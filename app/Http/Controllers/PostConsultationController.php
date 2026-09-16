<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Registre;
use App\Models\Ordonnance;
use App\Models\ArretTravail;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Consultation;
use App\Models\BulletinExamen;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Doctor\PostConsultationRepository;

class PostConsultationController extends Controller
{
    public function instance()
    {
        return new PostConsultationRepository();
    }

    public function storePostOrdonnance(Request $request, $type)
    {
        if($type === 'externe')
            $res = $this->instance()->storeOrdonnance($request);
        elseif ($type === 'interne')
            $res = $this->instance()->storeOrdonnanceI($request);
        else
            return response()->json(['status' => 'error', 'message' => 'Type d\'ordonnance introuvable.'], 200);

        if ($res['status'] == 'error')
            return response()->json(['status' => $res['status'], 'message' => $res['message']], 200);

        return response()->json(['status' => $res['status'], 'message' => $res['message'], 'id' => $res['id']], 200);
    }

    public function storePostBulletinExamen(Request $request)
    {
        $rules = [
            'consultation_id' => 'required',
        ];
        /***Messages de validation  ****/
        $messages = [
            'consultatin_id.required' => 'Le champ ID Consultation est obligatoire.',
        ];

        //consultation
        $consultation = Consultation::findOrFail($request->consultation_id);

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity
        }

        if (BulletinExamen::where('consultation_id', $request->consultation_id)->exists()) {
            $exist = BulletinExamen::where('consultation_id', $request->consultation_id)->first();
            // $exist->examens->delete();
            $exist->delete();
        }

        $bulletin = BulletinExamen::create([
            "code_bulletin" => codeBulletin($consultation->patient->code_patient, $consultation->patient->id),
            "consultation_id" => $request->consultation_id,
        ]);
        $exam = [];
        $natureExamen = is_array($request->nature_examen) ? $request->nature_examen : ($request->filled('nature_examen') ? [$request->nature_examen] : []);

        if (!empty($natureExamen)) {
            foreach ($natureExamen as $key => $exams) {
                if (empty($exams)) continue;
                $exam['code_examen'] = codeExamen($consultation->patient->code_patient, $consultation->patient->id);
                $exam['bulletin_examen_id'] = $bulletin->id;
                $exam['nature_examen'] = $exams;
                Examen::create($exam);
            }
        }
        return response()->json(['success' => 'Examen enregistré avec succès.', 'id' => $bulletin->id], 200);
    }

    public function storePostArretTravail(Request $request)
    {
        $res = $this->instance()->storeArretTravail($request);

        if ($res['status'] == 'error')
            return response()->json(['status' => $res['status'], 'message' => $res['message']], 200);

        return response()->json(['status' => $res['status'], 'message' => $res['message'], 'id' => $res['id']], 200);
    }




    public function Impression($post, $id)
    {
        if ($post == 'ordonnance')
            $pdf =  Pdf::loadView('users.doctor.consultation.formulaire.post-consultation.pdf.ordonnance', ['ordonnance' => Ordonnance::findOrFail($id)]);
        elseif ($post == 'examen')
            $pdf =  Pdf::loadView('users.doctor.consultation.formulaire.post-consultation.pdf.examen', ['bulletin' => BulletinExamen::findOrFail($id)]);
        elseif ($post == 'arret')
            $pdf =  Pdf::loadView('users.doctor.consultation.formulaire.post-consultation.pdf.arret', ['arret' => ArretTravail::findOrFail($id)]);
        else
            return redirect()->back();

        $pdf->setPaper('A4', 'portrait')->render();

        $response = new Response();
        $response->setContent($pdf->output())->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', "inline; filename=$post$id-" . date('dmY') . ".pdf");

        return $response;
    }
}
