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
        return auth()->check() && auth()->user()->role === 1;
    }

    /**
     * Validációs szabályok.
     */
    public function rules(): array
    {
        // A route-ból kinyerjük a 'category' paramétert.
        // Ez lehet egy ID (szám) vagy egy Category objektum (Route Model Binding esetén).
        $category = $this->route('category');
        
        // Biztosítjuk, hogy a $categoryId mindenképpen csak egy szám (ID) legyen.
        $categoryId = is_object($category) ? $category->id : $category;

        return [
            'category_name' => [
                'required',     // Nem lehet üres
                'string',       // Szövegnek kell lennie
                'max:100',      // Maximum 100 karakter
                // Egyedi kell legyen, de a jelenlegi kategóriát (amit épp módosítunk) hagyja figyelmen kívül
                Rule::unique('categories', 'category_name')->ignore($categoryId),
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
        ];
    }
}