<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Engedélyezzük a módosítást az adminoknak.
     */
    public function authorize(): bool
    {
        // Ellenőrizzük, hogy a user be van-e jelentkezve és admin-e (role == 1)
        return true;
    }

    /**
     * Validációs szabályok.
     */
    public function rules(): array
    {
        // A route-ból kinyerjük a 'category' paramétert.
        // Ez lehet egy ID (szám) vagy egy Category objektum (Route Model Binding esetén).
        $id = $this->route('id');
        
        // Biztosítjuk, hogy a $categoryId mindenképpen csak egy szám (ID) legyen.
        
        return [
            'category_name' => [
                'required',     // Nem lehet üres
                'string',  
                'min:2',     // Szövegnek kell lennie
                'max:50',      // Maximum 100 karakter
                // Egyedi kell legyen, de a jelenlegi kategóriát (amit épp módosítunk) hagyja figyelmen kívül
                Rule::unique('categories', 'category_name')->ignore($id),
            ],
        ];
    }

    /**
     * Egyedi hibaüzenetek.
     */
    public function messages(): array
    {
        return [
            'category_name.unique' => 'This category name is already taken.',
            'category_name.required' => 'The category name cannot be empty.',
            'category_name.string'   => 'The category name must be a valid string.',
            'category_name.min'      => 'The category name must be at least :min characters long.',
            'category_name.max'      => 'The category name may not be greater than :max characters.',
        ];
    }
}