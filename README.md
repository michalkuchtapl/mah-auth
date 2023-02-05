# Main Accounts Hub Auth - MAH Auth
<hr>

This package provide implementation of user's authentication using [MAH](https://github.com/michalkuchtapl/main-accounts-hub).

To install that package you need to add repository information to composer.json file:
```
{
    ...
    "repositories": [
        ...
        {
            "type": "vcs",
            "url":  "https://github.com/michalkuchtapl/mah-auth.git"
        }
    ]
    ...
}
```
After that just run:
```
composer require kuchta/laravel-mah-auth
```

## Usage
<hr>
To use that guard you can use middleware **auth:mah**, or set **mah** as your default auth guard inside `config/auth.php` config file.

Second thing to do is to set two env variables:
- `MAH_API_URI` - Uri of the Main Accounts Hub's api
- `MAH_API_KEY` - Api key generated during creation of application in Main Accounts Hub.
