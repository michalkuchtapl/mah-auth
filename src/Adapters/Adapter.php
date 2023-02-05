<?php

namespace Kuchta\Laravel\MahAuth\Adapters;

use Kuchta\Laravel\MahAuth\DataTransferObjects\User;
use Kuchta\Laravel\MahAuth\Extensions\GuzzleClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cookie;

class Adapter
{
    public const COOKIE_NAME = 'MAH_USER';

    private GuzzleClient $client;

    public function __construct()
    {
        $this->client = new GuzzleClient(new Client([
            'base_uri' => trim(config('mah.api.uri'), '/') . '/',
            'headers' => [
                'Authorization' => 'Bearer ' . config('mah.api.key'),
                'origin' => url(config('app.url')),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Mah-Locale' => config('app.locale')
            ]
        ]));
    }

    public function createUser(User $userPayload): User
    {
        $response = $this->client
            ->post('users', $userPayload->toApi());

        return User::fromApi($response->user);
    }

    public function updateUser(User $userPayload): User
    {
        $response = $this->client
            ->put("users/{$userPayload->id}", $userPayload->toApi());

        return User::fromApi($response->user);
    }

    public function authUser(string $email, string $password): User
    {
        $response = $this->client
            ->post('users/auth', [
                'email' => $email,
                'password' => $password,
            ]);

        $this->setToken($response->token->token);

        return User::fromApi($response->user);
    }

    public function logout(): void
    {
        $token = Cookie::get(self::COOKIE_NAME);
        self::clearToken();

        if (empty($token)) {
            return;
        }

        $this->client
            ->post('users/logout', [
                'token' => $token,
            ]);
    }

    public function validateUser(): ?User
    {
        $token = Cookie::get(self::COOKIE_NAME);

        if (empty($token))
            return null;

        try {
            $response = $this->client
                ->post('users/validate', [
                    'token' => $token,
                ]);
        } catch (RequestException $exception) {
            if ($exception->getCode() != '401') {
                report($exception);
            }
            return null;
        }

        return User::fromApi($response->user);
    }

    private static function getCookieDomain()
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST);
        $parts = array_reverse(explode('.', $host));

        $domain = $parts[1] . '.' . $parts[0];
        return '.'.$domain;
    }

    private function setToken($token): void
    {
        $cookie = Cookie::make(self::COOKIE_NAME, $token, 3600, null, self::getCookieDomain(), false, false, true);
        Cookie::queue($cookie);
    }

    public static function clearToken(): void
    {
        Cookie::queue(Cookie::forget(self::COOKIE_NAME, null, self::getCookieDomain()));
    }
}
