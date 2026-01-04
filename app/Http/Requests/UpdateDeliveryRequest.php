<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'driver_id' => 'nullable|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'status' => 'nullable|in:pending,assigned,in_transit,delivered,failed,cancelled',

            // Origem
            'origin_address' => ['sometimes', 'required', 'string', 'max:500'],
            'origin_lat' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'origin_lng' => ['sometimes', 'required', 'numeric', 'between:-180,180'],

            // Destino
            'destination_address' => ['sometimes', 'required', 'string', 'max:500'],
            'destination_lat' => ['sometimes', 'required', 'numeric', 'between:-90,90'],
            'destination_lng' => ['sometimes', 'required', 'numeric', 'between:-180,180'],

            // Métricas
            'distance_km' => 'nullable|numeric|min:0|max:99999.99',
            'estimated_time_minutes' => 'nullable|integer|min:0|max:999999',

            // Cliente
            'recipient_name' => ['sometimes', 'required', 'string', 'max:255'],
            'recipient_phone' => ['sometimes', 'required', 'string', 'max:15'],

            // Outros
            'delivery_notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'driver_id.exists' => 'Motorista não encontrado.',
            'vehicle_id.exists' => 'Veículo não encontrado.',
            'origin_address.required' => 'O endereço de origem é obrigatório.',
            'destination_address.required' => 'O endereço de destino é obrigatório.',
            'recipient_name.required' => 'O nome do destinatário é obrigatório.',
            'recipient_phone.required' => 'O telefone do destinatário é obrigatório.',
        ];
    }
}
