<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCurrencyRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'sometimes',
                'required',
                'string',
                'size:3',
                'unique:currencies,code',
            ],
            'base' => [
                'sometimes',
                'required',
                'array',
            ],
            'base.*' => [
                'numeric',
                'min:1',
            ],
            'exponent' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }
}
