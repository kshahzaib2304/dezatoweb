<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['services', 'customization', 'newsletter'])],
            'name' => ['required_unless:type,newsletter', 'nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'event_date' => ['nullable', 'date'],
            'guests' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'flavour' => ['nullable', 'string', 'max:120'],
            'size' => ['nullable', 'string', 'max:80'],
            'message_on_cake' => ['nullable', 'string', 'max:120'],
        ];
    }
}
