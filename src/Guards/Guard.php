<?php

namespace Kuchta\Laravel\MahAuth\Guards;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Kuchta\Laravel\MahAuth\Providers\UserProvider;

class Guard implements StatefulGuard
{
    use GuardHelpers;

    public function __construct(UserProvider $provider)
    {
        $this->provider = $provider;
    }

    public function id()
    {
        return $this->user()?->id;
    }

    public function validate(array $credentials = [])
    {
        $this->provider->retrieveByCredentials($credentials);
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        return $this->user = $this->provider->retrieveByToken(null, null);
    }

    public function attempt(array $credentials = [], $remember = false)
    {
        $this->provider->retrieveByCredentials($credentials);

        return true;
    }

    public function once(array $credentials = [])
    {
        $this->provider->retrieveByCredentials($credentials);

        return true;
    }

    public function login(Authenticatable $user, $remember = false)
    {
        $this->user();
    }

    public function loginUsingId($id, $remember = false)
    {
        return $this->user();
    }

    public function onceUsingId($id)
    {
        return $this->user();
    }

    public function viaRemember()
    {
        return true;
    }

    public function logout()
    {
        $this->provider->logout();
        $this->forgetUser();
    }

    public function recaller()
    {
        return null;
    }

    public function getRecallerName()
    {
        return 'mah';
    }
}
