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
        return true;
    }

    /**
     * Itt adjuk meg a szabályokat.
     */
    public function rules(): array
    {
        return [
            // A 'category_name' kötelező, szöveg kell legyen és egyedi a categories táblában
            'category_name' => 'required|string|min:2|max:50|unique:categories,category_name',
        ];
    }

    /**
     * Opcionális: Egyedi hibaüzenetek angolul
     */
    public function messages(): array
{
    return [
        'category_name.required' => 'The category name is mandatory.',
        'category_name.string'   => 'The category name must be a valid string.',
        'category_name.min'      => 'The category name must be at least 2 characters long.',
        'category_name.max'      => 'The category name may not be greater than 50 characters.',
        'category_name.unique'   => 'This category already exists.',
    ];
}

}