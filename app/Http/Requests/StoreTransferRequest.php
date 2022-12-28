<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
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
            'source_account_id' => [
                'required',
                'exists:accounts,id'
            ],
            'destination_account_id' => [
                'required',
                'exists:accounts,id'
            ],
            'amount' => [
                'required',
                'numeric'
            ],
            'exchange_rate' => [
                'required',
                'numeric'
            ]
        ];
    }
}
