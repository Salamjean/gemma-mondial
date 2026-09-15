<?php

namespace App\Http\Requests\Hospital;

use App\Rules\FileTypeValidate;
use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'mode_service' => 'nullable|in:existant,nouveau',
            'department' => 'required_without:nom_nouveau_service',
            'nom_nouveau_service' => 'required_without:department',
            'service' => 'nullable|array',
            'prix' => 'nullable|array',
            'description' => 'nullable|array',
            'nouveau_acte_libelle' => 'nullable|array',
            'nouveau_acte_prix' => 'nullable|array',
            'nouveau_acte_description' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'department.required_without' => 'Veuillez sélectionner un service existant ou renseigner le nom d\'un nouveau service.',
            'nom_nouveau_service.required_without' => 'Veuillez indiquer le nom du nouveau service à créer.',
        ];
    }
}
