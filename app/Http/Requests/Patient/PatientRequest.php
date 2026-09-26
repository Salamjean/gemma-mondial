<?php

namespace App\Http\Requests\Patient;

use App\Rules\FileTypeValidate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PatientRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'residence_actuelle' => 'nullable|integer',
            'residence_habituelle' => 'nullable|integer',
            'profession' => 'nullable|string|min:2|max:255',
            'situation_matrimoniale' => 'nullable|string|max:100',
            'contact1' => 'nullable|string|max:25',
            'contact2' => 'nullable|string|max:25',
            'nom_persn_sos' => 'nullable|string|max:255',
            'tel_persn_sos' => 'nullable|string|max:25',
            'lien_persn_sos' => 'nullable|string|max:100',
            'nom_persn_sos2' => 'nullable|string|max:255',
            'tel_persn_sos2' => 'nullable|string|max:25',
            'lien_persn_sos2' => 'nullable|string|max:100',
            'adresse' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . ($this->user() ? $this->user()->id : 'NULL'),
            'password' => 'nullable|string|confirmed|min:4',
            'image' => 'nullable',
            'photo' => 'nullable',
            'img_url' => 'nullable',
            'imagef' => 'nullable|string',
        ];
    }
}
