<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization logic as needed
    }

    public function rules()
    {
        return [
            'sku' => 'required|string|max:255|unique:products,sku,' . $this->product,
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'threshold_qty' => 'required|integer|min:0',
            'status' => 'required|in:active,archived',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Adjust max size as needed
        ];
    }

    public function messages()
    {
        return [
            'sku.required' => 'The SKU field is required.',
            'name.required' => 'The name field is required.',
            'category.required' => 'The category field is required.',
            'cost_price.required' => 'The cost price field is required.',
            'sell_price.required' => 'The sell price field is required.',
            'stock_qty.required' => 'The stock quantity field is required.',
            'threshold_qty.required' => 'The threshold quantity field is required.',
            'status.required' => 'The status field is required.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'Each image may not be greater than 2MB.',
        ];
    }
}