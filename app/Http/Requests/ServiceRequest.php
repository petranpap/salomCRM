<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => 'required|string|max:255',
            'category_id'  => 'nullable|exists:service_categories,id',
            'duration_min' => 'required|integer|min:5|max:480',
            'base_price'   => 'required|numeric|min:0',
            'is_active'    => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'         => 'The service name is required.',
            'category.required'     => 'The service category is required.',
            'duration_min.required' => 'The duration is required.',
            'duration_min.min'      => 'The minimum duration is 5 minutes.',
            'base_price.required'   => 'The base price is required.',
        ];
    }
}
