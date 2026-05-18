<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'expense_name' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:1'],
            'category' => ['required', 'string', 'max:60'],
            'date' => ['required', 'date'],
        ];
    }
}
