<?php

namespace Antarctica\LaravelTokenBlacklist;

use Illuminate\Support\ServiceProvider;

class TokenBlacklistServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->bind('Antarctica\LaravelTokenBlacklist\Repository\TokenBlacklistRepositoryInterface', 'Antarctica\LaravelTokenBlacklist\Repository\TokenBlacklistRepositoryEloquent');
    }
}