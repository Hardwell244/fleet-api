<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'observation' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O status do evento é obrigatório.',
            'latitude.between' => 'Latitude inválida.',
            'longitude.between' => 'Longitude inválida.',
        ];
    }
}
