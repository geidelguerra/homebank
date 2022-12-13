<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Models\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        return inertia('currencies/List', [
            'currencies' => function () {
                return Currency::query()->orderByDesc('code')->get();
            }
        ]);
    }

    public function create()
    {
        //
    }

    public function store(StoreCurrencyRequest $request)
    {
        $currency = new Currency($request->validated());
        $currency->save();

        return redirect()->route('currencies.index');
    }

    public function show(Currency $currency)
    {
        //
    }

    public function edit(Currency $currency)
    {
        //
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency)
    {
        $currency->fill($request->validated());
        $currency->save();

        return redirect()->route('currencies.index');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();

        return redirect()->route('currencies.index');
    }
}
