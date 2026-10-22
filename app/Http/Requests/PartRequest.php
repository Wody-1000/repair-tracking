<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // admin-only via EnsureRole route middleware
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'max:50', Rule::unique('parts', 'sku')->ignore($this->route('part'))],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:part_categories,id'],
            'quantity' => ['required', 'integer', 'min:0', 'max:100000'],
            'reorder_level' => ['required', 'integer', 'min:0', 'max:100000'],
            'unit_price' => ['required', 'numeric', 'min:0', 'max:9999999'],
        ];
    }
}
