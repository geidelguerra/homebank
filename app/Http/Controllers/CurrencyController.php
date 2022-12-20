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
            },
        ]);
    }

    public function create()
    {
        inertia()->share('breadcrumbs', [
            [
                'text' => 'Currencies',
                'url' => route('currencies.index'),
            ],
            [
                'text' => 'Add currency',
            ],
        ]);

        return inertia('currencies/Edit');
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
        inertia()->share('breadcrumbs', [
            [
                'text' => 'Currencies',
                'url' => route('currencies.index'),
            ],
            [
                'text' => 'Edit currency',
            ],
        ]);

        return inertia('currencies/Edit', [
            'currency' => $currency,
        ]);
    }

    public function update(UpdateCurrencyRequest $request, Currency $currency)
    {
        $currency->fill($request->validated());
        $currency->save();

        return redirect()->route('currencies.index');
    }

    public function destroy(Currency $currency)
    {
        if ($currency->accounts()->exists()) {
            return redirect()
                ->back()
                ->with('message', 'You can not delete this currency because there is accounts using it. First delete those accounts or change their currency');
        }

        $currency->delete();

        return redirect()->route('currencies.index');
    }
}
