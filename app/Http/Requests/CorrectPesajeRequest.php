<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorrectPesajeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'peso_registrado' => ['required', 'numeric', 'min:1', 'max:2000'],
            'notas_correccion' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'peso_registrado.required' => 'El peso corregido es obligatorio.',
            'peso_registrado.numeric' => 'El peso debe ser un número.',
            'peso_registrado.min' => 'El peso debe ser mayor a 0 kg.',
            'peso_registrado.max' => 'El peso no puede superar los 2000 kg.',
        ];
    }
}
