<?php

namespace ElCoop\Datatable\Providers;

use Illuminate\Support\ServiceProvider;

class DatatableServiceProvider extends ServiceProvider {
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register() {
		//
	}
	
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot() {
		$this->loadViewsFrom(__DIR__ . '/../views', 'elcoop:datatable');
		$this->publishes([
			__DIR__ . '/../../assets' => resource_path('js/vendor/elcoop')
		]);
	}
}
