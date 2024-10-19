<?php

namespace App\Providers;

use App\Http\Services\ClientService;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ClientService::class, function (Application $app) {
            return new ClientService(new HttpClient([
                'base_uri' => config('alpha_vantage.url'),
            ]), $app->make('cache'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {}
}
