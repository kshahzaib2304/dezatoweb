<?php

namespace App\Http\Requests;

use App\Support\Catalog;
use App\Support\Fulfillment;
use App\Support\KarachiAreas;
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
        $storeIds = Catalog::locations()->pluck('id')->filter()->values()->all();

        return [
            'method' => ['required', Rule::in([Fulfillment::METHOD_PICKUP, Fulfillment::METHOD_DELIVERY])],
            'area_id' => [
                'nullable',
                'string',
                Rule::requiredIf(fn (): bool => $this->string('method')->toString() === Fulfillment::METHOD_DELIVERY),
                Rule::in(KarachiAreas::ids()),
            ],
            'location_id' => [
                'nullable',
                'string',
                Rule::requiredIf(fn (): bool => $this->string('method')->toString() === Fulfillment::METHOD_PICKUP),
                Rule::in($storeIds),
            ],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'area_id.required' => 'Please select your neighbourhood in Karachi.',
            'area_id.in' => 'Please select a valid Karachi neighbourhood.',
            'location_id.required' => 'Please choose a bakery for pickup.',
            'location_id.in' => 'Please choose a valid bakery store.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $method = $this->string('method')->toString();

            if ($method === Fulfillment::METHOD_DELIVERY) {
                $area = KarachiAreas::find($this->string('area_id')->toString());

                if ($area === null) {
                    $validator->errors()->add('area_id', 'Please select your location in Karachi.');

                    return;
                }

                $location = Catalog::findLocation($area['location_id']);

                if ($location === null) {
                    $validator->errors()->add('area_id', 'That area is not available right now.');

                    return;
                }

                if (empty($location['delivers'])) {
                    $validator->errors()->add('area_id', 'Delivery is not available for that area yet.');
                }

                return;
            }

            $location = Catalog::findLocation($this->string('location_id')->toString());

            if ($location === null) {
                $validator->errors()->add('location_id', 'Please choose a valid bakery store.');
            }
        });
    }

    /**
     * @return array{method: string, area_id: string|null, location_id: string, address: string|null}
     */
    public function fulfillmentPayload(): array
    {
        $method = $this->string('method')->toString();

        if ($method === Fulfillment::METHOD_PICKUP) {
            return [
                'method' => $method,
                'area_id' => null,
                'location_id' => $this->string('location_id')->toString(),
                'address' => null,
            ];
        }

        $area = KarachiAreas::find($this->string('area_id')->toString());

        return [
            'method' => $method,
            'area_id' => $area['id'],
            'location_id' => $area['location_id'],
            'address' => $area['label'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $this->session()->flash('open_fulfillment', true);

        throw (new ValidationException($validator))
            ->redirectTo(route('home', ['fulfillment' => 1]));
    }
}
