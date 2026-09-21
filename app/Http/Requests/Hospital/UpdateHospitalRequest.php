<?php

namespace App\Http\Requests\Hospital;

use App\Rules\FileTypeValidate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHospitalRequest extends FormRequest
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
            'label' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:20',
            'district_sanitaire' => 'nullable|string|max:255',
            'nom_direction_generale' => 'nullable|string|max:255',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp', 'svg'])],
            'watermark' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp', 'svg'])],
            'password' => 'nullable|string|min:6|confirmed',
        ];
    }
}