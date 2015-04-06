<?php namespace Kyalo\Orient;

use Illuminate\Support\ServiceProvider;

class OrientServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		require __DIR__.'/../../../vendor/autoload.php';
		/*$this->publishes([
		    __DIR__.'/../../../config/orient-db.php' => config_path('orient_db.php'),
		]);*/
	}

	/**
 * Register the service provider.
 *
 * @return void
 */
public function register()
{
	$this->setConfig();
    $this->app->singleton('orient', function($app)
	{
		return new OrientClient;
	});
}

public function setConfig(){
	$this->mergeConfigFrom(__DIR__.'/../../../config/orient-db.php', 'database'); 
}

}
