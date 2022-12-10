<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        return inertia('transactions/List');
    }

    public function create()
    {
        return inertia('transactions/Edit');
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
        return inertia('transactions/Edit', [
            'transaction' => $transaction
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $transaction->fill($request->validated());
        $transaction->save();

        $transaction->account->updateAmount()->save();

        return redirect()->route('transactions.index');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        $transaction->account->updateAmount()->save();

        return redirect()->route('transactions.index');
    }
}
