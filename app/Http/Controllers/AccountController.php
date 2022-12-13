<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Models\Currency;

class AccountController extends Controller
{
    public function index()
    {
        return inertia('accounts/List', [
            'accounts' => function () {
                return Account::query()->with('currency')->get();
            }
        ]);
    }

    public function create()
    {
        inertia()->share('breadcrumbs', [
            [
                'text' => 'Accounts',
                'url' => route('accounts.index')
            ],
            [
                'text' => 'Add account',
            ],
        ]);

        return inertia('accounts/Edit', [
            'availableCurrencies' => Currency::query()->orderBy('code')->pluck('code')
        ]);
    }

    public function store(StoreAccountRequest $request)
    {
        $account = new Account($request->validated());

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
            'availableCurrencies' => Currency::query()->orderBy('code')->pluck('code')
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
