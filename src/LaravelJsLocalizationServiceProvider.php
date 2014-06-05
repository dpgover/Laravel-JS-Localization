<?php namespace Mariuzzo\LaravelJsLocalization;

use Illuminate\Support\ServiceProvider;

class LaravelJsLocalizationServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        $this->app['localization.js'] = $this->app->share(function ($app) {
            $generator = new Generators\LangJsGenerator($app['files']);
            return new Commands\LangJsCommand($generator);
        });

        $this->commands('localization.js');
    }

    public function provides()
    {
        return array('localization.js');
    }

	public function boot()
	{
		$this->package('mariuzzo/js-localization');

		$this->registerNamespaces();
	}

	private function registerNamespaces()
	{
		if (\File::isDirectory(app_path() . '/config/packages/mariuzzo/js-localization'))
		{
			\Config::addNamespace('js-localization', app_path() . '/config/packages/mariuzzo/js-localization');
		}
		else
		{
			\Config::addNamespace('js-localization', __DIR__.'/../config');
		}
	}
}
