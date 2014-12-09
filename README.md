# Laravel Token Blacklist

Enables API tokens to be marked as invalid until natural expiry within Laravel.

More information and proper README soon.

## Installing

Require this package in your `composer.json` file:

    {
        "require-dev": {
            "antarctica/laravel-token-blacklist": "dev-develop"
        }
    }

Register the service provider in the `providers` array of your `app/config/app.php` file:

    'Antarctica\LaravelTokenBlacklist\LaravelTokenBlacklistServiceProvider',

This package uses a Repository through which blackListed tokens can be stored/retrieved. By default, an eloquent model 
is used to implement this repository.

The default eloquent model and database migration to create its respective table are included as part of this package.

To run this migration run:

    php artisan migrate --package="antarctica\laravel-token-blacklist"

