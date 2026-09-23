<?php

namespace App\Http\Requests;

use App\Support\FulfillmentSchedule;
use App\Support\PaymentMethods;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlaceOrderRequest extends FormRequest
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
        $slots = FulfillmentSchedule::slots();

        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['required', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'string', Rule::in(PaymentMethods::enabledIds())],
            'delivery_date' => ['nullable', 'date', 'after_or_equal:'.FulfillmentSchedule::earliestDate()],
            'delivery_slot' => ['nullable', 'string', 'max:80', Rule::in($slots)],
            'express' => ['nullable', 'boolean'],
            'promo' => ['nullable', 'string', 'max:40'],
            'agree' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'agree.accepted' => 'Please confirm the order details before placing it.',
            'payment_method.in' => 'Please choose an available payment option.',
            'delivery_date.after_or_equal' => 'Please choose a date that gives the bakery enough notice.',
            'delivery_slot.in' => 'Please choose one of the available time windows.',
        ];
    }
}
