<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ListProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:1',
            'created_at' => 'nullable|date',
        ];
    }

    /**
     * Get the custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no puede tener más de 255 caracteres.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'price.min' => 'El precio debe ser positivo y mayor a 0.',
            'created_at.date' => 'La fecha de creación debe ser una fecha válida.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = implode('\n ', $validator->errors()->all());
        throw new HttpResponseException(response()->json(['message' => $errors], 422));
    }
}
