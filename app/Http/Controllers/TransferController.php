<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Services\MoneyExchangeService;
use Illuminate\Support\Facades\DB;
use Money\Currency;
use Money\Money;

class TransferController extends Controller
{
    public function store(StoreTransferRequest $request, MoneyExchangeService $moneyExchange)
    {
        $sourceAccount = Account::query()->with('currency')->find($request->input('source_account_id'));
        $destinationAccount = Account::query()->with('currency')->find($request->input('destination_account_id'));

        try {
            DB::beginTransaction();

            $sourceTransaction = new Transaction([
                'date' => $request->input('date'),
                'amount' => intval($request->input('amount')) * -1,
                'account_id' => $request->input('source_account_id'),
                'category_id' => $request->input('category_id'),
            ]);

            $sourceTransaction->save();
            $sourceAccount->updateAmount()->save();

            $destinationTransaction = new Transaction([
                'date' => $request->input('date'),
                'amount' => $moneyExchange->convert(
                    intval($request->input('amount')),
                    $sourceAccount->currency_code,
                    $destinationAccount->currency_code,
                    floatval($request->input('exchange_rate'))
                )->getAmount(),
                'account_id' => $request->input('destination_account_id'),
                'category_id' => $request->input('category_id'),
            ]);

            $destinationTransaction->save();
            $destinationAccount->updateAmount()->save();

            $transfer = new Transfer([
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
}
