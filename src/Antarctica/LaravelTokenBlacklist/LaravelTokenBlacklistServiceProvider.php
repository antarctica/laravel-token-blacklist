<?php namespace Antarctica\LaravelTokenBlacklist;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class LaravelTokenBlacklistServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Load package resources
        $this->package('antarctica/laravel-token-blacklist', null, __DIR__.'/../../../..');

        // Register package commands
        $this->commands('Antarctica\LaravelTokenBlacklist\Command\DeleteExpiredBlacklistedTokens');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Load package config to allow use of values
        Config::package('antarctica/laravel-token-blacklist', __DIR__.'/../../config');

        // Register package dependencies' service providers and aliases (so the user doesn't have to in app/config/app.php)
        $this->app->register('Indatus\Dispatcher\ServiceProvider');

        // Register package interfaces with their corresponding implementations
        $this->app->bind(
            'Antarctica\LaravelTokenBlacklist\Repository\TokenBlacklistRepositoryInterface',
            Config::get('laravel-token-blacklist::repository')
        );
    }

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
