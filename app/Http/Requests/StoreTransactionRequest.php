<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'date',
            ],
            'amount' => [
                'required',
                'numeric'
            ],
            'description' => [
                'nullable',
                'string',
                'max:500'
            ],
            'category_id' => [
                'required',
                'exists:categories,id'
            ],
            'account_id' => [
                'required',
                'exists:accounts,id'
            ]
        ];
    }
}
