<?php

namespace Wirgen\HermitageClient;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class HermitageClientServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $configPath = __DIR__ . '/../config/hermitage.php';
        $this->mergeConfigFrom($configPath, 'hermitage');

        $this->app->singleton(HermitageClient::class, function () {
            return new HermitageClient($this->app);
        });

        $this->app->alias(HermitageClient::class, 'hermitage');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/hermitage.php' => config_path('hermitage.php'),
        ]);
    }

    public function provides()
    {
        return ['hermitage', HermitageClient::class];
    }
}
