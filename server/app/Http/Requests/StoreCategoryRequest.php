<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Itt engedélyezzük a műveletet, ha a felhasználó admin.
     */
    public function authorize(): bool
    {
        // Csak akkor engedi tovább, ha be van jelentkezve és a role értéke 1 (admin)
        return auth()->check() && auth()->user()->role === 1;
    }

    /**
     * Itt adjuk meg a szabályokat.
     */
    public function rules(): array
    {
        return [
            // A 'category_name' kötelező, szöveg kell legyen és egyedi a categories táblában
            'category_name' => 'required|string|max:100|unique:categories,category_name',
        ];
    }

    /**
     * Opcionális: Egyedi hibaüzenetek angolul
     */
    public function messages(): array
    {
        return [
            'category_name.unique' => 'This category already exists.',
            'category_name.required' => 'The category name is mandatory.',
        ];
    }
}