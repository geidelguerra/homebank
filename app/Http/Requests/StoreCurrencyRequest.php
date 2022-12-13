<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'size:3',
                'unique:currencies,code'
            ],
            'base' => [
                'required',
                'array',
            ],
            'base.*' => [
                'numeric',
                'min:1'
            ],
            'exponent' => [
                'required',
                'numeric',
                'min:0'
            ]
        ];
    }
}
