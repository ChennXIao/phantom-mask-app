<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterByMaskCountRequest extends FormRequest
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
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0',
            'operator' => 'sometimes|string|in:>,<,=',
            'count' => 'sometimes|integer|min:0',
        ];
    }
}