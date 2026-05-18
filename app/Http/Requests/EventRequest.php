<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'event_name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'in:Wedding,Birthday,Corporate,Reception,Engagement,Concert'],
            'event_date' => ['required', 'date'],
            'event_time' => ['required', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:120'],
            'venue_name' => ['nullable', 'string', 'max:120'],
            'guest_count_expected' => ['required', 'integer', 'min:1'],
            'total_budget' => ['required', 'numeric', 'min:1000'],
            'theme' => ['nullable', 'string', 'max:80'],
            'status' => ['nullable', 'in:planning,confirmed,completed'],
            'banner' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
