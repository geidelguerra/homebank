<?php

namespace App\Providers;

use App\Models\Currency;
use App\Services\MoneyExchangeService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::unguard();

        $this->app->bind(MoneyExchangeService::class, function () {
            return new MoneyExchangeService(Currency::query()->get()->all());
        });
    }
}
