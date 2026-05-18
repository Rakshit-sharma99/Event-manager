<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'rsvp_status' => ['nullable', 'in:pending,yes,no,maybe'],
            'category' => ['nullable', 'string', 'max:50'],
            'dietary_preference' => ['nullable', 'string', 'max:50'],
            'plus_one_count' => ['nullable', 'integer', 'min:0', 'max:5'],
            'seat' => ['nullable', 'string', 'max:30'],
        ];
    }
}
