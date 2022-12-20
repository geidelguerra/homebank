<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],
            'currency_code' => [
                'sometimes',
                'required',
                'string',
                'size:3',
                'exists:currencies,code',
            ],
        ];
    }
}
