<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string:255',
                'email',
            ],
            'password' => [
                'required',
                'string:255',
            ],
            'remember' => [
                'boolean',
            ],
        ];
    }
}
