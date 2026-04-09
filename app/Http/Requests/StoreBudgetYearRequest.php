<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetYearRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all authenticated users (can add permission check here)
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'year' => [
                'required',
                'integer',
                'min:2020',
                'max:2100',
                'unique:budget_years,year', // Prevent duplicate years
            ],
        ];
    }
    
    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'year.required' => 'Year is required.',
            'year.integer' => 'Year must be a valid number.',
            'year.min' => 'Year must be 2020 or later.',
            'year.max' => 'Year cannot exceed 2100.',
            'year.unique' => 'This year already exists in the budget system.',
        ];
    }
}
