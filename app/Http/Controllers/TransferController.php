<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Services\MoneyExchangeService;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    public function index()
    {
        return inertia('transfers/List', [
            'transfers' => function () {
                return Transfer::query()->latest()->paginate();
            }
        ]);
    }

    public function create()
    {
        inertia()->share('breadcrumbs', [
            [
                'text' => 'Transfers',
                'url' => route('transfers.index'),
            ],
            [
                'text' => 'Add transfer',
            ],
        ]);

        return inertia('transfers/Edit', [
            'transfer' => [
                'exchange_rate' => 1
            ],
            'availableCategories' => function () {
                return Category::query()->orderByDesc('name')->get();
            },
            'availableAccounts' => function () {
                return Account::query()->orderByDesc('name')->get();
            }
        ]);
    }

    public function store(StoreTransferRequest $request, MoneyExchangeService $moneyExchange)
    {
        $sourceAccount = Account::query()->with('currency')->find($request->input('source_account_id'));
        $destinationAccount = Account::query()->with('currency')->find($request->input('destination_account_id'));

        try {
            DB::beginTransaction();

            $category = Category::getOrCreateTransferCategory();

            $sourceTransaction = new Transaction([
                'date' => $request->input('date'),
                'amount' => $request->integer('amount') * -1,
                'account_id' => $request->input('source_account_id'),
                'category_id' => $category->getKey(),
            ]);

            $sourceTransaction->save();
            $sourceAccount->updateAmount()->save();

            $destinationTransaction = new Transaction([
                'date' => $request->input('date'),
                'amount' => $moneyExchange->convert(
                    $request->integer('amount'),
                    $sourceAccount->currency_code,
                    $destinationAccount->currency_code,
                    $request->float('exchange_rate')
                )->getAmount(),
                'account_id' => $request->input('destination_account_id'),
                'category_id' => $category->getKey(),
            ]);

            $destinationTransaction->save();
            $destinationAccount->updateAmount()->save();

            $transfer = new Transfer([
                'date' => $request->input('date'),
                'amount' => $request->input('amount'),
                'exchange_rate' => $request->input('exchange_rate'),
                'source_transaction_id' => $sourceTransaction->getKey(),
                'destination_transaction_id' => $destinationTransaction->getKey(),
            ]);

            $transfer->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return redirect()->back();
    }

    public function edit(Transfer $transfer)
    {
        return inertia('transfers/Edit', [
            'transfer' => $transfer,
            'availableCategories' => function () {
                return Category::query()->orderByDesc('name')->get();
            },
            'availableAccounts' => function () {
                return Account::query()->orderByDesc('name')->get();
            }
        ]);
    }

    public function update(UpdateTransferRequest $request, MoneyExchangeService $moneyExchange, Transfer $transfer)
    {
        try {
            DB::beginTransaction();

            $transfer->sourceTransaction->fill([
                'amount' => $request->integer('amount') * -1
            ])->save();

            $transfer->sourceTransaction->account->updateAmount()->save();

            $transfer->destinationTransaction->fill([
                'amount' => $moneyExchange->convert(
                    $request->integer('amount'),
                    $transfer->sourceTransaction->account->currency_code,
                    $transfer->destinationTransaction->account->currency_code,
                    $request->float('exchange_rate')
                )->getAmount()
            ])->save();

            $transfer->destinationTransaction->account->updateAmount()->save();

            $transfer->fill($request->validated());
            $transfer->save();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }

        return redirect()->route('transfers.index');
    }

    public function destroy(Transfer $transfer)
    {
        $sourceAccount = $transfer->sourceTransaction->account;

        $transfer->sourceTransaction()->delete();

        $sourceAccount->updateAmount()->save();

        $destinationAccount = $transfer->destinationTransaction->account;

        $transfer->destinationTransaction()->delete();

        $destinationAccount->updateAmount()->save();

        $transfer->delete();

        return redirect()->route('transfers.index');
    }
}
