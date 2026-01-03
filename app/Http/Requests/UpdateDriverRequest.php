<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'cpf' => ['sometimes', 'required', 'string', 'size:11', 'regex:/^\d{11}$/'],
            'cnh' => ['sometimes', 'required', 'string', 'max:20'],
            'cnh_category' => ['sometimes', 'required', 'string', 'in:A,B,C,D,E,AB,AC,AD,AE'],
            'cnh_expiry' => ['sometimes', 'required', 'date', 'after:today'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,inactive,on_leave'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.size' => 'O CPF deve ter 11 dígitos.',
            'cpf.regex' => 'O CPF deve conter apenas números.',
            'cnh.required' => 'A CNH é obrigatória.',
            'cnh_category.required' => 'A categoria da CNH é obrigatória.',
            'cnh_category.in' => 'Categoria de CNH inválida.',
            'cnh_expiry.required' => 'A data de validade da CNH é obrigatória.',
            'cnh_expiry.after' => 'A CNH deve estar válida.',
            'email.email' => 'Email inválido.',
            'status.in' => 'Status inválido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('cpf')) {
            $this->merge([
                'cpf' => preg_replace('/\D/', '', $this->cpf)
            ]);
        }
    }
}
