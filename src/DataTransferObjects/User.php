<?php

namespace Kuchta\Laravel\MahAuth\DataTransferObjects;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelData\Data;

class User extends Data implements Authenticatable
{
    public ?int $id = null;

    public string $name;

    public string $email;

    public ?string $password = null;

    public ?string $passwordConfirmation = null;

    public function toApi(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
            $data['password_confirmation'] = $this->passwordConfirmation;
        }

        return $data;
    }

    public static function fromApi(\stdClass $payload): self
    {
        $user = new self();
        $user->id = $payload->id;
        $user->name = $payload->name;
        $user->email = $payload->email;

        return $user;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword()
    {
        return '';
    }

    public function getRememberToken()
    {
        return '';
    }

    public function setRememberToken($value)
    {
        //
    }

    public function getRememberTokenName()
    {
        return '';
    }
}
