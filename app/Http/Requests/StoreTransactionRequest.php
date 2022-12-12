<?php

namespace App\Http\Requests;

use App\Models\Account;
use App\Rules\AmountRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->accountAmountIsNegative()) {
                $validator->errors()->add('amount', 'This amount would make your account have a negative balance');
            }
        });
    }

    protected function accountAmountIsNegative(): bool
    {
        $account = Account::query()->find($this->input('account_id'));

        if ($account->amount + intval($this->input('amount')) < 0) {
            return true;
        }

        return false;
    }
}
