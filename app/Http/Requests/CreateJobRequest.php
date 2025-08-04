<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|min:10',
            'sueldo' => 'required|numeric|min:0'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es requerido',
            'titulo.max' => 'El título no puede tener más de 255 caracteres',
            'descripcion.required' => 'La descripción es requerida',
            'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
            'sueldo.required' => 'El sueldo es requerido',
            'sueldo.numeric' => 'El sueldo debe ser un número',
            'sueldo.min' => 'El sueldo no puede ser negativo'
        ];
    }
}
