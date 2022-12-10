<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'date' => [
                'sometimes',
                'required',
                'date',
            ],
            'amount' => [
                'sometimes',
                'required',
                'numeric'
            ],
            'description' => [
                'nullable',
                'string',
                'max:500'
            ],
            'category_id' => [
                'sometimes',
                'required',
                'exists:categories,id'
            ],
            'account_id' => [
                'sometimes',
                'required',
                'exists:accounts,id'
            ]
        ];
    }
}
