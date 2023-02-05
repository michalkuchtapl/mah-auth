<?php

namespace Kuchta\Laravel\MahAuth\Extensions;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\ValidationException;

class GuzzleClient
{
    public function __construct(private Client $client)
    {
    }

    public function get(string $uri, array $query = null): \stdClass
    {
        return $this->request('get', $uri, $query);
    }

    public function put(string $uri, array $data = null): \stdClass
    {
        return $this->request('put', $uri, $data);
    }

    public function post(string $uri, array $data = null): \stdClass
    {
        return $this->request('post', $uri, $data);
    }

    public function delete(string $uri): \stdClass
    {
        return $this->request('delete', $uri, null);
    }

    private function request(string $method, string $uri, ?array $data): \stdClass
    {
        $options = [
            $method === 'get' ? 'query' : 'json' => $data
        ];

        try {
            $response = $this->client->request($method, $uri, $options);
        } catch (RequestException $exception) {
            $this->handleException($exception);
        }

        return json_decode($response->getBody());
    }

    private function handleException(RequestException $exception)
    {
        if ($exception->getCode() === 422)
            $this->handleValidationErrors($exception);

        else if ($exception->getCode() === 590)
            $this->handleUnauthorized($exception);

        else if ($exception->getCode() === 401)
            $this->handleUnauthenticated($exception);

        else
            throw $exception;
    }

    private function handleValidationErrors(RequestException $exception)
    {
        $response = json_decode($exception->getResponse()->getBody()->getContents(), true);

        $errors = data_get($response, 'errors', []);

        if (empty($errors))
            throw new \Exception("Undefined error");

        throw ValidationException::withMessages($errors);
    }

    private function handleUnauthorized(RequestException $exception)
    {
        throw ValidationException::withMessages([
            'email' => "Your account doesn't have permission to use that app. Contact support to authenticate your account",
            'password' => "Your account doesn't have permission to use that app. Contact support to authenticate your account",
        ]);
    }

    private function handleUnauthenticated(RequestException $exception)
    {
        throw ValidationException::withMessages([
            'email' => 'Wrong email or password',
            'password' => 'Wrong email or password',
        ]);
    }
}
