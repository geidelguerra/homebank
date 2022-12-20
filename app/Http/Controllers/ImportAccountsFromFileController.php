<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportAccountsFromFileRequest;
use App\Models\Account;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Transaction;
use App\Support\TransactionType;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ImportAccountsFromFileController extends Controller
{
    public function __invoke(ImportAccountsFromFileRequest $request)
    {
        DB::table('transactions')->truncate();
        DB::table('accounts')->truncate();

        collect(array_map('str_getcsv', file($request->file)))
            ->slice(intval($request->input('ignored_rows', 0)))
            ->each(function ($data) use ($request) {
                Model::withoutEvents(function () use ($data, $request) {
                    $amount = $this->parseAmount((string) $data[intval($request->validated('amount_column'))]);

                    try {
                        DB::beginTransaction();

                        Transaction::query()->create([
                            'date' => $this->parseDate($data[intval($request->validated('date_column'))], $request->validated('date_timezone')),
                            'description' => $data[intval($request->validated('description_column'))],
                            'amount' => $amount,
                            'type' => $amount > 0 ? TransactionType::Income : TransactionType::Expense,
                            'category_id' => Category::query()->firstOrCreate(['name' => $data[intval($request->validated('category_column'))]])->getKey(),
                            'account_id' => Account::query()->firstOrCreate([
                                'name' => $data[intval($request->validated('account_column'))],
                                'currency_code' => Currency::query()->firstOrCreate(['code' => 'USD'])->getKey(),
                            ])->getKey(),
                        ])->save();

                        DB::commit();
                    } catch (\Throwable $th) {
                        DB::rollBack();

                        logger()->error('Error importing accounts from file: ' . $th->getMessage(), [$th->getTraceAsString()]);
                    }
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

    private function parseDate(string $val, ?string $timezone = null): DateTimeInterface
    {
        return Carbon::createFromFormat('d/m/Y', $val, $timezone)->timezone('UTC');
    }

    private function parseAmount(string $val): int
    {
        return intval(floatval(str($val)->remove(['$', ','])->toString()) * 100);
    }
}
