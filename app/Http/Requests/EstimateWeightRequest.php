<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstimateWeightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bovino_id' => ['required', 'exists:bovinos,id'],
            'foto' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'raza_id' => ['required', 'exists:razas,id'],
            'modo_offline' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'bovino_id.required' => 'El bovino es obligatorio.',
            'bovino_id.exists' => 'El bovino seleccionado no existe.',
            'foto.required' => 'La fotografía del bovino es obligatoria.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La imagen debe ser JPEG, PNG o WEBP.',
            'foto.max' => 'La imagen no debe superar los 10 MB.',
            'raza_id.required' => 'La raza es obligatoria.',
            'raza_id.exists' => 'La raza seleccionada no existe.',
        ];
    }
}
