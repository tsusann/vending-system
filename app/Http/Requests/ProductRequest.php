<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_name' => 'required|max:255',
            'price'        => 'required|integer|min:0',
            'stock'        => 'required|integer|min:0',
            'company_id'   => 'required|exists:companies,id',
            'comment'      => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 画像のバリデーション
        ];
    }
}
