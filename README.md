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

Note: Until this package is released publicly, or an internal package server is created you will need to help
Composer find this package by adding the following configuration:

    "repositories": [
        {
            "type": "vcs",
            "url": "ssh://git@stash.ceh.ac.uk:7999/~felnne/laravel-token-blacklist.git"
        }
    ],

Note: [BASWEB-114](https://jira.ceh.ac.uk/browse/BASWEB-114) - If using the antarctica/laravel ansible role to provision the underlying infrastructure on which the app using this package is required, it is necessary to require this package in the app `composer.json` file. Composer will resolve the package requirements in exactly the same way, this change is needed so ansible is aware this package is used and OS support should be provided for its use.

Register the service provider in the `providers` array of your `app/config/app.php` file:

    'Antarctica\LaravelTokenBlacklist\LaravelTokenBlacklistServiceProvider',

This package uses a Repository through which blackListed tokens can be stored/retrieved. By default, an eloquent model 
is used to implement this repository.

The default eloquent model and database migration to create its respective table are included as part of this package.

To run this migration run:

    php artisan migrate --package="antarctica/laravel-token-blacklist"

If you wish to use an alternative repository implementation you can, providing it implements the `TokenBlacklistRepositoryInterface` interface.

To set an alternative implementation, first publish this package's config:

    php artisan config:publish antarctica/laravel-token-blacklist

Then edit the `repository` key.

TODO: Describe scheduled task + general information about package commands.
