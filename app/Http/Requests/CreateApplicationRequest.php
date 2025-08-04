<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateApplicationRequest extends FormRequest
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
            'trabajo_id' => 'required|exists:trabajos,id',
            'mensaje' => 'required|string|min:10',
            'cv' => 'required|file|mimes:pdf|max:5120'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'trabajo_id.required' => 'El trabajo es requerido',
            'trabajo_id.exists' => 'El trabajo seleccionado no existe',
            'mensaje.required' => 'El mensaje es requerido',
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres',
            'cv.required' => 'El CV es requerido',
            'cv.file' => 'El CV debe ser un archivo',
            'cv.mimes' => 'El CV debe ser un archivo PDF',
            'cv.max' => 'El CV no puede ser mayor a 5MB'
        ];
    }
}
