<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         return auth()->user()->role === 1;  // 1 = admin
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
            'name' => 'nullable|string|max:150',  // Név csak opcionális, ha módosítani akarják
            'category_id' => 'nullable|exists:categories,id',  // Kategória nem kötelező módosítani
            'company_id' => 'nullable|exists:companies,id',  // Cég nem kötelező módosítani
            'pcs' => 'nullable|integer|min:1',  // Darabszám, ha van, minimum 1
            'price' => 'nullable|numeric|min:0',  // Ár, ha van, minimum 0
            'description' => 'nullable|string',  // Leírás opcionális
        ];
    }
}
