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
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|min:1',
            'min_count' => 'nullable|integer|min:0|required_without_all:max_count',
            'max_count' => 'nullable|integer|min:1|required_without_all:min_count',
        ];
    }
}
