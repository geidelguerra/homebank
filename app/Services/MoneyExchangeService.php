<?php

namespace App\Services;

use Money\Converter;
use Money\Currencies\CurrencyList;
use Money\Currency;
use Money\Exchange;
use Money\Exchange\FixedExchange;
use Money\Exchange\ReversedCurrenciesExchange;
use Money\Money;

class MoneyExchangeService
{
    private CurrencyList $currencies;

    public function __construct(array $currencies = [])
    {
        $this->currencies = new CurrencyList(collect($currencies)->mapWithKeys(fn ($currency) => [$currency->code => $currency->exponent])->all());
    }

    public function convert(int $amount, string $sourceCurrency, string $destCurrency, float $rate): Money
    {
        $converter = new Converter($this->currencies, $this->makeExchange($sourceCurrency, $destCurrency, $rate));

        return $converter->convert(new Money($amount, new Currency($sourceCurrency)), new Currency($destCurrency));
    }

    private function makeExchange(string $sourceCurrency, string $destCurrency, float $rate, bool $reversed = false): Exchange
    {
        $exchange = new FixedExchange([$sourceCurrency => [$destCurrency => str($rate)->toString()]]);

        if ($reversed) {
            $exchange = new ReversedCurrenciesExchange($exchange);
        }

        return $exchange;
    }
}
