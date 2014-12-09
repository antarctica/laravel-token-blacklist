<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Provider
    |--------------------------------------------------------------------------
    |
    | Specify the Repository which stores and retrieves details of blacklisted
    | tokens. By default this package uses an eloquent model.
    |
    | The repository must extend the TokenBlacklistRepositoryInterface.
    |
	| e.g. 'Antarctica\LaravelTokenBlacklist\Repository\TokenBlacklistRepositoryEloquent'
    |
    */

    'repository' => 'Antarctica\LaravelTokenBlacklist\Repository\TokenBlacklistRepositoryEloquent'

];