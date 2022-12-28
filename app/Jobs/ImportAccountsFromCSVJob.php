<?php

namespace App\Jobs;

use App\Events\RefreshUI;
use App\Models\Account;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Support\ImportAccountsFromFileOptions as ImportOptions;
use DateTimeInterface;
use Illuminate\Bus\Batchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ImportAccountsFromCSVJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public function __construct(
        public User $user,
        public array $data,
        public ImportOptions $options
    ) {
        //
    }

    public function handle(): void
    {
        collect(array_map('str_getcsv', $this->data))
            ->slice($this->options->getIgnoredRows())
            ->each(function ($data) {
                if ($this->batch()?->canceled()) {
                    return;
                }

                Model::withoutEvents(function () use ($data) {
                    $amount = $this->parseAmount((string) $data[$this->options->getAmountColumnIndex()]);

                    try {
                        DB::beginTransaction();

                        Transaction::query()->create([
                            'date' => $this->parseDate($data[$this->options->getDateColumnIndex()], $this->options->getDateTimezone()),
                            'description' => $data[$this->options->getDescriptionColumnIndex()],
                            'amount' => $amount,
                            'category_id' => Category::query()->firstOrCreate(['name' => $data[$this->options->getCategoryColumnIndex()]])->getKey(),
                            'account_id' => Account::query()->firstOrCreate([
                                'name' => $data[$this->options->getAccountColumnIndex()],
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
                // ignore
            }
        });

        event(new RefreshUI($this->user));
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
