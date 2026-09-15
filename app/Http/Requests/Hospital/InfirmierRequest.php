<?php

namespace App\Http\Requests\Hospital;

use App\Rules\FileTypeValidate;
use Illuminate\Foundation\Http\FormRequest;

class InfirmierRequest extends FormRequest
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
            'name' => "required|string|min:3",
            'email' => "required|email|unique:users,email",
            'matricule' => "required|string",
            'password' => "required|string|confirmed|min:4",
            "service" => "nullable",
            "services" => "required_without:service|array",
            "services.*" => "integer|exists:service_hospitals,id",
            "contact" => "required|regex:/^[0-9]{10}$/",
            "address" => "nullable|string|min:3",
            "day" =>    "required|array",
            "time" =>    "required|array",
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ];
    }
}
