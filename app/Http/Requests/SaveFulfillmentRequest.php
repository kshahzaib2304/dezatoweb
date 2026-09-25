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
        return [
            'method' => ['required', Rule::in([Fulfillment::METHOD_PICKUP, Fulfillment::METHOD_DELIVERY])],
            'area_id' => ['required', 'string', Rule::in(KarachiAreas::ids())],
            'location_id' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

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

            if ($this->string('method')->toString() === Fulfillment::METHOD_DELIVERY && empty($location['delivers'])) {
                $validator->errors()->add('area_id', 'Delivery is not available for that area yet.');
            }
        });
    }

    /**
     * @return array{method: string, area_id: string, location_id: string, address: string|null}
     */
    public function fulfillmentPayload(): array
    {
        $area = KarachiAreas::find($this->string('area_id')->toString());
        $method = $this->string('method')->toString();

        return [
            'method' => $method,
            'area_id' => $area['id'],
            'location_id' => $area['location_id'],
            'address' => $method === Fulfillment::METHOD_DELIVERY ? $area['label'] : null,
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $this->session()->flash('open_fulfillment', true);

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag())
            ->redirectTo(route('home', ['fulfillment' => 1]));
    }
}
