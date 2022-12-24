<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Http\Requests\UpdateTransferRequest;
use App\Models\Account;
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

    public function store(StoreTransferRequest $request, MoneyExchangeService $moneyExchange)
    {
        $sourceAccount = Account::query()->with('currency')->find($request->input('source_account_id'));
        $destinationAccount = Account::query()->with('currency')->find($request->input('destination_account_id'));

        try {
            DB::beginTransaction();

            $sourceTransaction = new Transaction([
                'date' => $request->safe()->input('date'),
                'amount' => $request->safe()->integer('amount') * -1,
                'account_id' => $request->safe()->input('source_account_id'),
                'category_id' => $request->safe()->input('category_id'),
            ]);

            $sourceTransaction->save();
            $sourceAccount->updateAmount()->save();

            $destinationTransaction = new Transaction([
                'date' => $request->safe()->input('date'),
                'amount' => $moneyExchange->convert(
                    $request->safe()->integer('amount'),
                    $sourceAccount->currency_code,
                    $destinationAccount->currency_code,
                    $request->safe()->float('exchange_rate')
                )->getAmount(),
                'account_id' => $request->safe()->input('destination_account_id'),
                'category_id' => $request->safe()->input('category_id'),
            ]);

            $destinationTransaction->save();
            $destinationAccount->updateAmount()->save();

            $transfer = new Transfer([
                'date' => $request->safe()->input('date'),
                'amount' => $request->safe()->input('amount'),
                'exchange_rate' => $request->safe()->input('exchange_rate'),
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

    public function update(UpdateTransferRequest $request, MoneyExchangeService $moneyExchange, Transfer $transfer)
    {
        try {
            DB::beginTransaction();

            $transfer->sourceTransaction->fill([
                'amount' => $request->safe()->integer('amount') * -1
            ])->save();

            $transfer->sourceTransaction->account->updateAmount()->save();

            $transfer->destinationTransaction->fill([
                'amount' => $moneyExchange->convert(
                    $request->safe()->integer('amount'),
                    $transfer->sourceTransaction->account->currency_code,
                    $transfer->destinationTransaction->account->currency_code,
                    $request->safe()->float('exchange_rate')
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
