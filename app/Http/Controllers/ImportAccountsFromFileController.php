<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportAccountsFromFileRequest;
use App\Jobs\ImportAccountsFromCSVJob;
use App\Models\User;
use App\Support\ImportAccountsFromFileOptions as ImportOptions;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class ImportAccountsFromFileController extends Controller
{
    public function __invoke(ImportAccountsFromFileRequest $request)
    {
        DB::table('transactions')->truncate();
        DB::table('accounts')->truncate();

        $options = new ImportOptions(
            $request->integer('date_column'),
            $request->integer('amount_column'),
            $request->integer('category_column'),
            $request->integer('description_column'),
            $request->integer('account_column'),
            0,
            $request->string('date_timezone', 'UTC'),
        );

        $jobs = collect(file($request->file('file')))->slice($request->integer('ignored_rows'))->chunk(1000)->map(function ($data) use ($options) {
            return new ImportAccountsFromCSVJob(User::auth(), $data->all(), $options);
        })->all();

        Bus::batch($jobs)->dispatch();

        return redirect()->back();
    }
}
