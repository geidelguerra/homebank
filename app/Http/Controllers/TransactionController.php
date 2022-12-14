<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        return inertia('transactions/List', [
            'transactions' => function () {
                return Transaction::query()
                    ->with([
                        'account.currency',
                        'category'
                    ])
                    ->latest('date')
                    ->orderByDesc('id')
                    ->get();
            }
        ]);
    }

    public function create()
    {
        inertia()->share('breadcrumbs', [
            [
                'text' => 'Transactions',
                'url' => route('transactions.index')
            ],
            [
                'text' => 'Add transaction',
            ],
        ]);

        return inertia('transactions/Edit', [
            'availableAccounts' => function () {
                return Account::query()->orderBy('name')->get();
            },
            'availableCategories' => function () {
                return Category::query()->orderBy('name')->get();
            }
        ]);
    }

    public function store(StoreTransactionRequest $request)
    {
        $transaction = new Transaction($request->validated());
        $transaction->save();

        $transaction->account->updateAmount()->save();

        return redirect()->route('transactions.index');
    }

    public function show(Transaction $transaction)
    {
        return inertia('transactions/View', [
            'transactions' => $transaction
        ]);
    }

    public function edit(Transaction $transaction)
    {
        inertia()->share('breadcrumbs', [
            [
                'text' => 'Transactions',
                'url' => route('transactions.index')
            ],
            [
                'text' => 'Edit transaction',
            ],
        ]);

        return inertia('transactions/Edit', [
            'transaction' => $transaction->load(['account', 'category']),
            'availableAccounts' => function () {
                return Account::query()->orderBy('name')->get();
            },
            'availableCategories' => function () {
                return Category::query()->orderBy('name')->get();
            }
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $account = $transaction->account;

        $transaction->fill($request->validated());
        $transaction->save();

        if ($transaction->wasChanged('account_id')) {
            $account->updateAmount()->save();
        }

        $transaction->refresh()->account->updateAmount()->save();

        return redirect()->route('transactions.index');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        $transaction->account->updateAmount()->save();

        return redirect()->route('transactions.index');
    }
}
