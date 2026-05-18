<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'vendor_id' => ['required', 'string'],
            'booking_date' => ['required', 'date'],
            'booking_time_from' => ['required', 'date_format:H:i'],
            'booking_time_to' => ['required', 'date_format:H:i', 'after:booking_time_from'],
            'amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:500'],
            'add_to_budget' => ['nullable', 'boolean'],
        ];
    }
}
