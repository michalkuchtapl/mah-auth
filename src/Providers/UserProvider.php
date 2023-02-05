<?php

namespace Kuchta\Laravel\MahAuth\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as BaseUserProvider;
use Kuchta\Laravel\MahAuth\Adapters\Adapter;

class UserProvider implements BaseUserProvider
{
    protected Adapter $adapter;

    public function __construct()
    {
        $this->adapter = app(Adapter::class);
    }

    public function retrieveById($identifier)
    {
        return $this->adapter->validateUser();
    }

    public function retrieveByToken($identifier, $token)
    {
        return $this->adapter->validateUser();
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        //not necessary
    }

    public function retrieveByCredentials(array $credentials)
    {
        return $this->adapter->authUser(data_get($credentials, 'email'), data_get($credentials, 'password'));
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->adapter->authUser(data_get($credentials, 'email'), data_get($credentials, 'password'));
    }

    public function logout()
    {
        $this->adapter->logout();
    }

    public function getModel()
    {
        return $this->adapter->validateUser();
    }
}
