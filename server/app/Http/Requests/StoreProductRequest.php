<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            //
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id', // Ellenőrzi, hogy létezik-e a kategória
            'company_id' => 'required|exists:companies,id',  // Ellenőrzi, hogy létezik-e a cég
            'pcs' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
        ];
    }
}
