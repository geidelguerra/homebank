<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportAccountsFromFileRequest;
use App\Models\Account;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ImportAccountsFromFileController extends Controller
{
    public function __invoke(ImportAccountsFromFileRequest $request)
    {
        collect(array_map('str_getcsv', file($request->file)))
            ->slice(intval($request->input('ignored_rows', 0)))
            ->each(function ($data) use ($request) {
                Model::withoutEvents(function () use ($data, $request) {
                    Transaction::query()->updateOrCreate([
                        'date' => Carbon::createFromFormat('d/m/Y', $data[intval($request->input('date_column'))], 'America/Havana'),
                        'description' => $data[intval($request->input('description_column'))],
                        'amount' => $this->parseAmount((string) $data[intval($request->input('amount_column'))]),
                        'category_id' => Category::query()->firstOrCreate(['name' => $data[intval($request->input('category_column'))]])->getKey(),
                        'account_id' => Account::query()->firstOrCreate([
                            'name' => $data[intval($request->input('account_column'))],
                            'currency_code' => Currency::query()->firstOrCreate(['code' => 'USD'])->getKey()
                        ])->getKey(),
                    ])->save();
                });
            });

        Account::query()->each(function (Account $account) {
            try {
                $account->updateAmount()->save();
            } catch (\Throwable $th) {
                //throw $th;
            }
        });

        return redirect()->back();
    }

    private function parseAmount(string $val): int
    {
        return intval(floatval(str($val)->remove(['$', ','])->toString()) * 100);
    }
}
