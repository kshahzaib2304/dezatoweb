<?php

namespace App\Http\Requests;

use App\Support\Catalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCartItemRequest extends FormRequest
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
        $ids = Catalog::products()->pluck('id')->all();

        return [
            'product_id' => ['required', 'string', Rule::in($ids)],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ];
    }
}
