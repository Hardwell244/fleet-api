<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plate' => ['required', 'string', 'max:7', 'regex:/^[A-Z]{3}[0-9]{4}$/', 'unique:vehicles,plate'],
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'type' => 'required|in:car,motorcycle,truck,van',
            'status' => 'nullable|in:available,in_use,maintenance,inactive',
            'fuel_capacity' => 'nullable|numeric|min:0|max:999999.99',
            'current_km' => 'nullable|numeric|min:0|max:99999999.99',
        ];
    }

    public function messages(): array
    {
        return [
            'plate.required' => 'A placa é obrigatória.',
            'plate.regex' => 'A placa deve estar no formato ABC1234 (3 letras seguidas de 4 números).',
            'plate.unique' => 'Esta placa já está cadastrada.',
            'brand.required' => 'A marca é obrigatória.',
            'model.required' => 'O modelo é obrigatório.',
            'year.required' => 'O ano é obrigatório.',
            'year.min' => 'Ano inválido.',
            'type.required' => 'O tipo de veículo é obrigatório.',
            'type.in' => 'Tipo de veículo inválido.',
        ];
    }
}
