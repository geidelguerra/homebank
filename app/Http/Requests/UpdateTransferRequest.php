<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransferRequest extends FormRequest
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
            'exchange_rate' => [
                'sometimes',
                'required',
                'numeric'
            ]
        ];
    }
}
