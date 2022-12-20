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
            ],
        ];
    }
}
