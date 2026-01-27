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
            // A 'unique:products,name' azt jelenti: nézd meg a products tábla name oszlopát
            'name' => 'required|string|max:150|unique:products,name',
            'category_id' => 'required|exists:categories,id',
            'company_id' => 'required|exists:companies,id',
            'pcs' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
        ];
    }
    public function messages(): array
    {
        return [
            'name.unique' => 'This product name already exists in our database.',
        ];
    }
}
