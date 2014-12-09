<?php namespace Antarctica\LaravelTokenBlacklist;

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
		$this->package('antarctica/laravel-token-blacklist');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app->bind('Antarctica\LaravelTokenBlacklist\Repository\TokenBlacklistRepositoryInterface', function ($app)
        {
            return $app['antarctica.laravel-token-blacklist.repository'];
        });
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
