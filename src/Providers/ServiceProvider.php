<?php

namespace Kuchta\Laravel\MahAuth\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Illuminate\Support\Facades\Auth;
use Kuchta\Laravel\MahAuth\Adapters\Adapter;
use Kuchta\Laravel\MahAuth\Guards\Guard;

class ServiceProvider extends AuthServiceProvider
{
    private string $rootDir = __DIR__.'/../..';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(Adapter::class, fn () => new Adapter);

        $this->registerPolicies();
        $this->updateAuthGuardsConfig();
    }

    public function register()
    {
        $this->mergeConfigFrom($this->rootDir.'/config/mah.php', 'mah');
        $this->registerGuard();
    }

    private function registerGuard()
    {
        Auth::extend('mah', function ($app, $name, array $config) {
            $userProvider = new UserProvider();

            return new Guard($userProvider);
        });
    }

    protected function updateAuthGuardsConfig()
    {
        $config = $this->app['config'];

        if (! $config->get('auth.guards.mah')) {
            $config->set('auth.guards.mah', ['driver' => 'mah']);
        }
    }
}
