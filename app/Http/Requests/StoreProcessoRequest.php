<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProcessoRequest extends FormRequest
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
            'valor_estimado' => ['required', 'regex:/^\d{1,3}(?:\.\d{3})*(?:,\d{2})?$/'],
            'objeto' => ['required'],
            'ano_processo' => ['required', 'integer', 'regex:/^\d{4}$/'],
            'solicitante_id' => ['required', Rule::exists('users', 'id')],
        ];
    }
}
