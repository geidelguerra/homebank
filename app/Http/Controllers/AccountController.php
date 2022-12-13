<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;

class AccountController extends Controller
{
    public function index()
    {
        return inertia('accounts/List', [
            'accounts' => function () {
                return Account::query()->get();
            }
        ]);
    }

    public function create()
    {
        return inertia('accounts/Edit');
    }

    public function store(StoreAccountRequest $request)
    {
        $account = new Account([
            'name' => $request->validated('name'),
            'currency' => $request->validated('currency')
        ]);

        $account->save();

        return redirect()->route('accounts.index');
    }

    public function show(Account $account)
    {
        return inertia('accounts/View', [
            'account' => $account,
        ]);
    }

    public function edit(Account $account)
    {
        inertia()->share('breadcrumbs', [
            [
                'text' => 'Accounts',
                'url' => route('accounts.index')
            ],
            [
                'text' => 'Edit account',
            ],
        ]);

        return inertia('accounts/Edit', [
            'account' => $account,
            'availableCurrencies' => [
                'USD',
                'EUR',
                'CUP',
            ]
        ]);
    }

    public function update(UpdateAccountRequest $request, Account $account)
    {
        $account->fill($request->validated());
        $account->save();

        return redirect()->route('accounts.index');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index');
    }
}
