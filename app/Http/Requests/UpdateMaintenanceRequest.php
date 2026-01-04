<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|in:preventive,corrective,inspection',
            'description' => 'required|string|max:500',
            'scheduled_date' => 'required|date',
            'completed_date' => 'nullable|date|after_or_equal:scheduled_date',
            'cost' => 'nullable|numeric|min:0|max:999999.99',
            'status' => 'nullable|in:scheduled,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'O veículo é obrigatório.',
            'vehicle_id.exists' => 'Veículo não encontrado.',
            'type.required' => 'O tipo de manutenção é obrigatório.',
            'type.in' => 'Tipo de manutenção inválido.',
            'description.required' => 'A descrição é obrigatória.',
            'scheduled_date.required' => 'A data agendada é obrigatória.',
            'completed_date.after_or_equal' => 'A data de conclusão deve ser após a data agendada.',
            'status.in' => 'Status inválido.',
        ];
    }
}
