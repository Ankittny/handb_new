<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HsnRequest extends FormRequest
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
            'hsn_code_under_gst' => 'required|string|max:10', 
            'description' => 'required|string|max:255',
            'tax' => 'required|numeric|min:0', 
        ];
    }

    public function messages(): array
    {
        return [
            'hsn_code.required' => 'The HSN code field is required.',
            'hsn_code.string' => 'The HSN code must be a string.',
            'hsn_code.max' => 'The HSN code may not be greater than 10 characters.',
            
            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
            
            'tax.required' => 'The tax field is required.',
            'tax.numeric' => 'The tax must be a number.',
            'tax.min' => 'The tax must be at least 0.',
        ];
    }
}
