# Main Accounts Hub Auth - MAH Auth

This package provide implementation of user's authentication using [MAH](https://github.com/michalkuchtapl/main-accounts-hub).

To install that package you need to add repository information to composer.json file:
```
{
    ...
    "repositories": [
        {
            "type": "vcs",
            "url":  "https://github.com/michalkuchtapl/mah-auth.git"
        }
    ]
    ...
}
```
After that require package with:
```
composer require kuchta/laravel-mah-auth
```
This package uses cookies to store auth token for the user, that token is visible to all other app in that domain, so when user logs into one app it will be logged in everywhere.
Laravel encrypts cookies by default, so you also need to exclude our cookie from encryption by adding
```
Kuchta\Laravel\MahAuth\Adapters\Adapter::COOKIE_NAME
```
to `EncryptCookie` middleware of your application.
## Usage
To use that guard you can use middleware `auth:mah`, or set `mah` as your default auth guard inside `config/auth.php` config file.

Second thing to do is to set two env variables:
- `MAH_API_URI` - Uri of the Main Accounts Hub's api
- `MAH_API_KEY` - Api key generated during creation of application in Main Accounts Hub.

Login and validation of the users is handled automatically by this auth guard.

To create user you need to create `User` object and pass it to `createUser` method of the adapter.

```PHP
use Kuchta\Laravel\MahAuth\Adapters\Adapter;
use Kuchta\Laravel\MahAuth\DataTransferObjects\User;

$adapter = app(Adapter::class);

$user = new User();
$user->name = $input['name'];
$user->email = $input['email'];
$user->password = $input['password'];
$user->passwordConfirmation = $input['password_confirmation'];

$user = $adapter->createUser($user)
```
