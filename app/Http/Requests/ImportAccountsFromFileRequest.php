<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportAccountsFromFileRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
            ],
            'ignored_rows' => [
                'required',
                'numeric',
                'min:0',
            ],
            'date_column' => [
                'required',
                'integer',
                'min:0'
            ],
            'category_column' => [
                'required',
                'integer',
                'min:0'
            ],
            'description_column' => [
                'required',
                'integer',
                'min:0'
            ],
            'amount_column' => [
                'required',
                'integer',
                'min:0'
            ],
            'account_column' => [
                'required',
                'integer',
                'min:0'
            ],
            'date_timezone' => [
                'nullable',
                'timezone'
            ]
        ];
    }
}
