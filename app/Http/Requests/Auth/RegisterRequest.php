<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        $categories = array_keys(config('smart_budget.service_vendor_category_map', []));

        return [
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'in:planner,vendor,guest'],
            'vendor_category' => ['required_if:role,vendor', 'nullable', 'string', 'in:' . implode(',', $categories)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'residence' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name.',
            'email.unique' => 'An account with this email already exists.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'role.in' => 'Please select a valid role.',
            'vendor_category.required_if' => 'Please select the service category your business falls under.',
            'vendor_category.in' => 'Please select a valid service category.',
        ];
    }
}
