<?php

namespace App\Http\Requests;

use App\Support\Catalog;
use App\Support\Fulfillment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SaveFulfillmentRequest extends FormRequest
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
        $locationIds = Catalog::locations()->pluck('id')->all();
        $method = $this->string('method')->toString();
        $needsStore = in_array($method, [Fulfillment::METHOD_PICKUP, Fulfillment::METHOD_DELIVERY], true);

        return [
            'method' => ['required', Rule::in(Fulfillment::METHODS)],
            'location_id' => [
                Rule::requiredIf($needsStore),
                'nullable',
                'string',
                Rule::in($locationIds),
            ],
            'address' => [
                Rule::requiredIf(in_array($method, [Fulfillment::METHOD_DELIVERY, Fulfillment::METHOD_SHIPPING], true)),
                'nullable',
                'string',
                'max:255',
            ],
            'city' => [
                Rule::requiredIf($method === Fulfillment::METHOD_SHIPPING),
                'nullable',
                'string',
                'max:120',
            ],
            'region' => [
                Rule::requiredIf($method === Fulfillment::METHOD_SHIPPING),
                'nullable',
                'string',
                'max:64',
            ],
            'postal_code' => [
                Rule::requiredIf($method === Fulfillment::METHOD_SHIPPING),
                'nullable',
                'string',
                'max:32',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $method = $this->string('method')->toString();

            if ($method === Fulfillment::METHOD_SHIPPING) {
                return;
            }

            $location = Catalog::findLocation($this->string('location_id')->toString());

            if ($location === null) {
                $validator->errors()->add('location_id', 'Please choose a valid bakery location.');

                return;
            }

            if ($method === Fulfillment::METHOD_DELIVERY && empty($location['delivers'])) {
                $validator->errors()->add('location_id', 'That bakery does not offer delivery.');
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        $this->session()->flash('open_fulfillment', true);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag())
            ->redirectTo(route('home', ['fulfillment' => 1]));
    }
}
